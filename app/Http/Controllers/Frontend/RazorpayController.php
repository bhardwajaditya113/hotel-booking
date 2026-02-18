<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Razorpay\Api\Api;
use App\Mail\BookConfirm;

class RazorpayController extends Controller
{
    /**
     * Display Razorpay payment page
     */
    public function paymentPage($booking_id)
    {
        $booking = Booking::with(['room', 'property'])->findOrFail($booking_id);
        
        // Check if booking belongs to current user
        if ($booking->user_id != auth()->id()) {
            abort(403, 'Unauthorized');
        }
        
        // Check if already paid
        if ($booking->payment_status == 1) {
            return redirect()->route('user.booking')->with([
                'message' => 'This booking is already paid',
                'alert-type' => 'info'
            ]);
        }
        
        return view('frontend.payment.razorpay', compact('booking'));
    }
    
    /**
     * Process Razorpay payment
     */
    public function processPayment(Request $request)
    {
        $request->validate([
            'razorpay_payment_id' => 'required',
            'razorpay_order_id' => 'required',
            'razorpay_signature' => 'required',
            'booking_id' => 'required',
        ]);
        
        $booking = Booking::findOrFail($request->booking_id);
        
        // Check if this is a test payment (for development)
        $isTestPayment = $request->has('test_payment') && $request->test_payment == 'true';
        
        if ($isTestPayment) {
            // Skip signature verification for test payments
            // This allows testing without actual Razorpay integration
        } else {
            // Verify signature
            $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
            
            try {
                $attributes = [
                    'razorpay_order_id' => $request->razorpay_order_id,
                    'razorpay_payment_id' => $request->razorpay_payment_id,
                    'razorpay_signature' => $request->razorpay_signature
                ];
                
                $api->utility->verifyPaymentSignature($attributes);
            } catch (\Exception $e) {
                return redirect('/')->with([
                    'message' => 'Payment verification failed: ' . $e->getMessage(),
                    'alert-type' => 'error'
                ]);
            }
        }
        
        try {
            
            // Payment verified, update booking
            $booking->payment_status = 1;
            $booking->transation_id = $request->razorpay_payment_id;
            $booking->status = 1; // Confirmed
            $booking->save();
            
            // Send email confirmation to user
            try {
                $mailData = [
                    'check_in' => $booking->check_in,
                    'check_out' => $booking->check_out,
                    'name' => $booking->name,
                    'email' => $booking->email,
                    'phone' => $booking->phone,
                    'booking_code' => $booking->code,
                    'total_price' => $booking->total_price,
                    'room' => $booking->room,
                    'property' => $booking->property,
                ];
                Mail::to($booking->email)->send(new BookConfirm($mailData));
            } catch (\Exception $e) {
                // Log error but don't fail the payment
                \Log::error('Failed to send booking confirmation email: ' . $e->getMessage());
            }
            
            // Send notification to admin users
            $adminUsers = User::where('role', 'admin')->get();
            foreach ($adminUsers as $admin) {
                DB::table('notifications')->insert([
                    'id' => Str::uuid()->toString(),
                    'type' => 'App\\Notifications\\BookingComplete',
                    'notifiable_type' => 'App\\Models\\User',
                    'notifiable_id' => $admin->id,
                    'user_id' => $admin->id,
                    'data' => json_encode(['message' => 'New Booking Added by ' . $booking->name]),
                    'read_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            // Redirect to booking confirmation page
            return redirect()->route('booking.confirmation', ['booking_id' => $booking->id])->with([
                'message' => 'Payment Successful! Your booking is confirmed.',
                'alert-type' => 'success'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Payment processing error: ' . $e->getMessage());
            return redirect('/')->with([
                'message' => 'Payment processing failed: ' . $e->getMessage(),
                'alert-type' => 'error'
            ]);
        }
    }
    
    /**
     * Create Razorpay order
     */
    public function createOrder(Request $request)
    {
        $booking = Booking::findOrFail($request->booking_id);
        
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
        
        // Amount in paise (1 INR = 100 paise)
        // If price is already in INR, use directly; otherwise convert
        $amount_in_inr = $booking->total_price;
        // If price seems to be in USD (less than 100), convert to INR
        if ($amount_in_inr < 100) {
            $amount_in_inr = $amount_in_inr * 83; // Approximate conversion
        }
        $amount_in_paise = round($amount_in_inr * 100);
        
        try {
            $order = $api->order->create([
                'receipt' => 'booking_' . $booking->id,
                'amount' => round($amount_in_paise),
                'currency' => 'INR',
                'notes' => [
                    'booking_id' => $booking->id,
                    'booking_code' => $booking->code,
                ]
            ]);
            
            return response()->json([
                'success' => true,
                'order_id' => $order->id,
                'amount' => round($amount_in_paise),
                'currency' => 'INR',
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Handle payment failure
     */
    public function paymentFailed(Request $request)
    {
        return redirect('/')->with([
            'message' => 'Payment was cancelled or failed. Please try again.',
            'alert-type' => 'error'
        ]);
    }
    
    /**
     * Test payment bypass (for development only)
     * This allows completing payment without actual Razorpay integration
     */
    public function testPayment(Request $request)
    {
        if (app()->environment('production')) {
            abort(403, 'Test payment not allowed in production');
        }
        
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
        ]);
        
        $booking = Booking::findOrFail($request->booking_id);
        
        // Check if booking belongs to current user
        if ($booking->user_id != auth()->id()) {
            abort(403, 'Unauthorized');
        }
        
        // Check if already paid
        if ($booking->payment_status == 1) {
            return redirect()->route('booking.confirmation', ['booking_id' => $booking->id])->with([
                'message' => 'This booking is already paid.',
                'alert-type' => 'info'
            ]);
        }
        
        // Mark as paid
        $booking->payment_status = 1;
        $booking->status = 1;
        $booking->transation_id = 'test_txn_' . time();
        $booking->save();
        
        // Send email confirmation
        try {
            $mailData = [
                'check_in' => $booking->check_in,
                'check_out' => $booking->check_out,
                'name' => $booking->name,
                'email' => $booking->email,
                'phone' => $booking->phone,
                'booking_code' => $booking->code,
                'total_price' => $booking->total_price,
                'room' => $booking->room,
                'property' => $booking->property,
            ];
            Mail::to($booking->email)->send(new BookConfirm($mailData));
        } catch (\Exception $e) {
            \Log::error('Failed to send booking confirmation email: ' . $e->getMessage());
        }
        
        // Send notification to admin users
        $adminUsers = User::where('role', 'admin')->get();
        foreach ($adminUsers as $admin) {
            DB::table('notifications')->insert([
                'id' => Str::uuid()->toString(),
                'type' => 'App\\Notifications\\BookingComplete',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => $admin->id,
                'user_id' => $admin->id,
                'data' => json_encode(['message' => 'New Booking Added by ' . $booking->name]),
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        return redirect()->route('booking.confirmation', ['booking_id' => $booking->id])->with([
            'message' => 'Test Payment Successful! Your booking is confirmed.',
            'alert-type' => 'success'
        ]);
    }
}

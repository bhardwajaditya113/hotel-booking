<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Events\HostBookingRequestReceived;
use App\Mail\BookConfirm;
use App\Models\Booking;
use App\Models\BookingRoomList;
use App\Models\Notification;
use App\Models\Property;
use App\Models\Room;
use App\Models\RoomBookedDate;
use App\Models\RoomNumber;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

class BookingController extends Controller
{
    public function Checkout()
    {

        if (Session::has('book_date')) {
            $book_data = Session::get('book_date');
            $room = Room::with('property')->find($book_data['room_id']);

            // Get property if available
            $property = null;
            if (isset($book_data['property_id'])) {
                $property = Property::find($book_data['property_id']);
            } elseif ($room && $room->property_id) {
                $property = Property::find($room->property_id);
            }

            $toDate = Carbon::parse($book_data['check_in']);
            $fromDate = Carbon::parse($book_data['check_out']);
            $nights = $toDate->diffInDays($fromDate);

            return view('frontend.checkout.checkout', compact('book_data', 'room', 'nights', 'property'));
        } else {

            $notification = [
                'message' => 'Something want to wrong!',
                'alert-type' => 'error',
            ];

            return redirect('/')->with($notification);
        } // end else

    }// End Method

    public function BookingStore(Request $request)
    {

        $validated = $request->validate([
            'check_in' => ['required', 'date'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'persion' => ['required', 'integer', 'min:1', 'max:99'],
            'number_of_rooms' => ['required', 'integer', 'min:1', 'max:99'],
            'available_room' => ['required', 'integer', 'min:0'],
            'room_id' => ['required', 'integer', 'exists:rooms,id'],
        ]);

        if ($validated['available_room'] < $validated['number_of_rooms']) {

            $notification = [
                'message' => 'Something want to wrong!',
                'alert-type' => 'error',
            ];

            return redirect()->back()->with($notification);
        }
        Session::forget('book_date');

        $data = [];
        $data['number_of_rooms'] = $validated['number_of_rooms'];
        $data['available_room'] = $validated['available_room'];
        $data['persion'] = $validated['persion'];
        $data['check_in'] = date('Y-m-d', strtotime($validated['check_in']));
        $data['check_out'] = date('Y-m-d', strtotime($validated['check_out']));
        $data['room_id'] = $validated['room_id'];

        Session::put('book_date', $data);

        return redirect()->route('checkout');

    }// End Method

    public function CheckoutStore(Request $request)
    {

        $user = User::where('role', 'admin')->get();

        $this->validate($request, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'country' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:64'],
            'address' => ['required', 'string', 'max:500'],
            'state' => ['required', 'string', 'max:255'],
            'zip_code' => ['required', 'string', 'max:32'],
            'payment_method' => ['required', Rule::in(['COD', 'Razorpay'])],
        ]);

        $book_data = Session::get('book_date');
        if (! is_array($book_data) || empty($book_data['room_id']) || empty($book_data['check_in']) || empty($book_data['check_out'])) {
            return redirect()->route('froom.all')->with([
                'message' => 'Your booking session expired. Please choose dates again.',
                'alert-type' => 'error',
            ]);
        }

        $toDate = Carbon::parse($book_data['check_in']);
        $fromDate = Carbon::parse($book_data['check_out']);
        $total_nights = $toDate->diffInDays($fromDate);

        $room = Room::find($book_data['room_id']);
        if (! $room) {
            return redirect()->route('froom.all')->with([
                'message' => 'This room is no longer available.',
                'alert-type' => 'error',
            ]);
        }

        $bookingProperty = Property::find($room->property_id);
        $needsHostApproval = $bookingProperty && ! $bookingProperty->instant_book_enabled;

        // Use calculated values from session if available, otherwise calculate
        if (isset($book_data['subtotal']) && isset($book_data['total_price'])) {
            $subtotal = $book_data['subtotal'];
            $discount = $book_data['discount'] ?? 0;
            $total_price = $book_data['total_price'];
        } else {
            $subtotal = $room->price * $total_nights * $book_data['number_of_rooms'];
            $discount = isset($room->discount) ? ($room->discount / 100) * $subtotal : 0;
            $total_price = $subtotal - $discount;
        }

        $code = rand(000000000, 999999999);

        $payment_status = 0;
        $transation_id = '';

        if ($request->payment_method == 'Razorpay') {
            $data = new Booking;
            $data->rooms_id = $room->id;
            $data->property_id = isset($book_data['property_id']) ? $book_data['property_id'] : $room->property_id;
            $data->user_id = Auth::user()->id;
            $data->check_in = date('Y-m-d', strtotime($book_data['check_in']));
            $data->check_out = date('Y-m-d', strtotime($book_data['check_out']));
            $data->persion = $book_data['persion'];
            $data->number_of_rooms = $book_data['number_of_rooms'];
            $data->total_night = $total_nights;

            $data->actual_price = $room->price;
            $data->subtotal = $subtotal;
            $data->discount = $discount;
            $data->total_price = $total_price;
            $data->payment_method = $request->payment_method;
            $data->transation_id = '';
            $data->payment_status = 0;

            $data->name = $request->name;
            $data->email = $request->email;
            $data->phone = $request->phone;
            $data->country = $request->country;
            $data->state = $request->state;
            $data->zip_code = $request->zip_code;
            $data->address = $request->address;

            $data->code = $code;
            $data->status = 0;
            $data->host_approval_status = $needsHostApproval ? 'pending' : null;
            $data->created_at = Carbon::now();
            $data->save();

            if ($needsHostApproval) {
                $this->notifyHostPendingBooking($data, $bookingProperty);
                foreach ($user as $admin) {
                    Notification::query()->create([
                        'user_id' => $admin->id,
                        'type' => 'booking',
                        'title' => 'New booking request',
                        'message' => 'Booking request '.$data->code.' awaits host approval.',
                        'data' => ['booking_id' => $data->id],
                    ]);
                }
                Session::forget('book_date');

                return redirect()->route('booking.confirmation', ['booking_id' => $data->id])->with([
                    'message' => __('frontend.booking.flash_host_pending'),
                    'alert-type' => 'info',
                ]);
            }

            Booking::attachRoomBookedNights($data, $room);

            Session::forget('book_date');

            return redirect()->route('razorpay.payment', ['booking_id' => $data->id]);
        }

        $data = new Booking;
        $data->rooms_id = $room->id;
        $data->property_id = isset($book_data['property_id']) ? $book_data['property_id'] : $room->property_id;
        $data->user_id = Auth::user()->id;
        $data->check_in = date('Y-m-d', strtotime($book_data['check_in']));
        $data->check_out = date('Y-m-d', strtotime($book_data['check_out']));
        $data->persion = $book_data['persion'];
        $data->number_of_rooms = $book_data['number_of_rooms'];
        $data->total_night = $total_nights;

        $data->actual_price = $room->price;
        $data->subtotal = $subtotal;
        $data->discount = $discount;
        $data->total_price = $total_price;
        $data->payment_method = $request->payment_method;
        $data->transation_id = $transation_id;
        $data->payment_status = $payment_status;

        $data->name = $request->name;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->country = $request->country;
        $data->state = $request->state;
        $data->zip_code = $request->zip_code;
        $data->address = $request->address;

        $data->code = $code;
        $data->status = 0;
        $data->host_approval_status = $needsHostApproval ? 'pending' : null;
        $data->created_at = Carbon::now();
        $data->save();

        if ($needsHostApproval) {
            $this->notifyHostPendingBooking($data, $bookingProperty);
            foreach ($user as $admin) {
                Notification::query()->create([
                    'user_id' => $admin->id,
                    'type' => 'booking',
                    'title' => 'New booking request',
                    'message' => 'Booking request '.$data->code.' awaits host approval.',
                    'data' => ['booking_id' => $data->id],
                ]);
            }
            Session::forget('book_date');

            return redirect()->route('booking.confirmation', ['booking_id' => $data->id])->with([
                'message' => __('frontend.booking.flash_host_pending'),
                'alert-type' => 'info',
            ]);
        }

        Booking::attachRoomBookedNights($data, $room);

        Session::forget('book_date');

        // In-app notifications for admins (user_notifications table)
        foreach ($user as $admin) {
            Notification::query()->create([
                'user_id' => $admin->id,
                'type' => 'booking',
                'title' => 'New booking',
                'message' => 'New Booking Added by '.$request->name,
                'data' => ['message' => 'New Booking Added by '.$request->name],
            ]);
        }

        try {
            Mail::to($request->email)->send(new BookConfirm([
                'check_in' => $data->check_in,
                'check_out' => $data->check_out,
                'name' => $data->name,
                'email' => $data->email,
                'phone' => $data->phone,
            ]));
        } catch (\Throwable $e) {
            report($e);
        }

        $notification = [
            'message' => 'Booking added successfully.',
            'alert-type' => 'success',
        ];

        return redirect()->route('booking.confirmation', ['booking_id' => $data->id])->with($notification);

    }// End Method

    public function BookingList()
    {

        $allData = Booking::query()
            ->with(['user', 'room.type', 'property'])
            ->orderByDesc('id')
            ->get();

        return view('backend.booking.booking_list', compact('allData'));

    }// End Method

    public function EditBooking($id)
    {

        $editData = Booking::query()
            ->with(['room.type', 'property', 'user'])
            ->findOrFail($id);

        return view('backend.booking.edit_booking', compact('editData'));

    }// End Method

    public function UpdateBookingStatus(Request $request, $id)
    {

        $booking = Booking::find($id);
        $booking->payment_status = $request->payment_status;
        $booking->status = $request->status;
        $booking->save();

        // / Start Sent Email

        $sendmail = Booking::find($id);

        $data = [
            'check_in' => $sendmail->check_in,
            'check_out' => $sendmail->check_out,
            'name' => $sendmail->name,
            'email' => $sendmail->email,
            'phone' => $sendmail->phone,
        ];

        Mail::to($sendmail->email)->send(new BookConfirm($data));

        // / End Sent Email

        $notification = [
            'message' => 'Information Updated Successfully',
            'alert-type' => 'success',
        ];

        return redirect()->back()->with($notification);

    }   // End Method

    public function UpdateBooking(Request $request, $id)
    {

        if ($request->available_room < $request->number_of_rooms) {

            $notification = [
                'message' => 'Something Want To Wrong!',
                'alert-type' => 'error',
            ];

            return redirect()->back()->with($notification);
        }

        $data = Booking::find($id);
        $data->number_of_rooms = $request->number_of_rooms;
        $data->check_in = date('Y-m-d', strtotime($request->check_in));
        $data->check_out = date('Y-m-d', strtotime($request->check_out));
        $data->save();

        BookingRoomList::where('booking_id', $id)->delete();
        RoomBookedDate::where('booking_id', $id)->delete();

        $sdate = date('Y-m-d', strtotime($request->check_in));
        $edate = date('Y-m-d', strtotime($request->check_out));
        $eldate = Carbon::create($edate)->subDay();
        $d_period = CarbonPeriod::create($sdate, $eldate);
        foreach ($d_period as $period) {
            $booked_dates = new RoomBookedDate;
            $booked_dates->booking_id = $data->id;
            $booked_dates->room_id = $data->rooms_id;
            $booked_dates->book_date = date('Y-m-d', strtotime($period));
            $booked_dates->save();
        }

        $notification = [
            'message' => 'Booking Updated Successfully',
            'alert-type' => 'success',
        ];

        return redirect()->back()->with($notification);

    }  // End Method

    public function AssignRoom($booking_id)
    {

        $booking = Booking::find($booking_id);

        $booking_date_array = RoomBookedDate::where('booking_id', $booking_id)->pluck('book_date')->toArray();

        $check_date_booking_ids = RoomBookedDate::whereIn('book_date', $booking_date_array)->where('room_id', $booking->rooms_id)->distinct()->pluck('booking_id')->toArray();

        $booking_ids = Booking::whereIn('id', $check_date_booking_ids)->pluck('id')->toArray();

        $assign_room_ids = BookingRoomList::whereIn('booking_id', $booking_ids)->pluck('room_number_id')->toArray();

        $room_numbers = RoomNumber::where('rooms_id', $booking->rooms_id)->whereNotIn('id', $assign_room_ids)->where('status', 'Active')->get();

        return view('backend.booking.assign_room', compact('booking', 'room_numbers'));

    } // End Method

    public function AssignRoomStore($booking_id, $room_number_id)
    {

        $booking = Booking::find($booking_id);
        $check_data = BookingRoomList::where('booking_id', $booking_id)->count();

        if ($check_data < $booking->number_of_rooms) {
            $assign_data = new BookingRoomList;
            $assign_data->booking_id = $booking_id;
            $assign_data->room_id = $booking->rooms_id;
            $assign_data->room_number_id = $room_number_id;
            $assign_data->save();

            $notification = [
                'message' => 'Room Assign Successfully',
                'alert-type' => 'success',
            ];

            return redirect()->back()->with($notification);

        } else {

            $notification = [
                'message' => 'Room Already Assign',
                'alert-type' => 'error',
            ];

            return redirect()->back()->with($notification);

        }

    }// End Method

    public function AssignRoomDelete($id)
    {

        $assign_room = BookingRoomList::find($id);
        $assign_room->delete();

        $notification = [
            'message' => 'Assign Room Deleted Successfully',
            'alert-type' => 'success',
        ];

        return redirect()->back()->with($notification);

    }// End Method

    public function DownloadInvoice($id)
    {

        $editData = Booking::with('room')->find($id);
        $pdf = Pdf::loadView('backend.booking.booking_invoice', compact('editData'))->setPaper('a4')->setOption([
            'tempDir' => public_path(),
            'chroot' => public_path(),
        ]);

        return $pdf->download('invoice.pdf');

    }// End Method

    public function UserBooking()
    {
        $id = Auth::user()->id;
        $allData = Booking::with(['room.type', 'property'])->where('user_id', $id)->orderBy('id', 'desc')->get();

        return view('frontend.dashboard.user_booking', compact('allData'));

    }// End Method

    public function BookingConfirmation($booking_id)
    {
        $booking = Booking::with(['room.type', 'property', 'user'])->findOrFail($booking_id);

        // Check if booking belongs to current user
        if ($booking->user_id != Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return view('frontend.booking.confirmation', compact('booking'));
    }// End Method

    public function UserInvoice($id)
    {

        $editData = Booking::with('room')->find($id);
        $pdf = Pdf::loadView('backend.booking.booking_invoice', compact('editData'))->setPaper('a4')->setOption([
            'tempDir' => public_path(),
            'chroot' => public_path(),
        ]);

        return $pdf->download('invoice.pdf');

    }// End Method

    public function MarkAsRead(Request $request, $notificationId)
    {

        $user = Auth::user();
        if (! $user) {
            return response()->json(['count' => 0], 401);
        }
        $notification = $user->panelNotifications()->where('id', $notificationId)->first();

        if ($notification) {
            $notification->markAsRead();
        }

        return response()->json(['count' => $user->panelNotifications()->unread()->count()]);

    }// End Method

    protected function notifyHostPendingBooking(Booking $booking, ?Property $property): void
    {
        if (! $property || ! $property->user_id) {
            return;
        }

        Notification::query()->create([
            'user_id' => $property->user_id,
            'type' => 'booking',
            'title' => __('frontend.host_hub.notif_booking_request_title'),
            'message' => __('frontend.host_hub.notif_booking_request_body', ['code' => $booking->code]),
            'data' => ['booking_id' => $booking->id],
        ]);

        if (config('broadcasting.default') !== 'null') {
            try {
                broadcast(new HostBookingRequestReceived(
                    (int) $property->user_id,
                    $booking->id,
                    (string) $booking->code,
                ));
            } catch (\Throwable $e) {
                report($e);
            }
        }
    }

}

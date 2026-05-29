<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BookArea;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\Room;
use App\Models\MultiImage;
use App\Models\Facility;
use App\Models\RoomBookedDate;
use App\Models\Booking;

class FrontendRoomController extends Controller
{
    public function AllFrontendRoomList(){

        $rooms = Room::with(['type', 'property.type'])->latest()->get();
        return view('frontend.room.all_rooms',compact('rooms'));
    } // End Method 


    public function RoomDetailsPage($id){

        $roomdetails = Room::find($id);
        $multiImage = MultiImage::where('rooms_id',$id)->get();
        $facility = Facility::where('rooms_id',$id)->get();

        $otherRooms = Room::where('id','!=', $id)->orderBy('id','DESC')->limit(2)->get();
        // Determine any date filters passed in query string
        $checkIn = request()->query('check_in');
        $checkOut = request()->query('check_out');

        // Compute active pricing rule once in the controller to avoid DB in the view
        $activePricingRule = \App\Models\PricingRule::where('is_active', true)
            ->where(function($q) use ($checkIn) {
                $q->whereNull('start_date')
                  ->orWhere('start_date', '<=', $checkIn ?? now());
            })
            ->where(function($q) use ($checkOut) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', $checkOut ?? now());
            })
            ->orderBy('priority', 'desc')
            ->first();

        // Compute simple availability stat used by the booking form
        $room_numbers_count = \App\Models\RoomNumber::where('rooms_id', $id)->where('status', 'Active')->count();

        return view('frontend.room.room_details',compact('roomdetails','multiImage','facility','otherRooms','checkIn','checkOut','activePricingRule','room_numbers_count'));

    } // End Method 


    public function BookingSeach(Request $request){
   
        $request->flash();

        if ($request->check_in == $request->check_out) {

            $notification = array(
                'message' => 'Something want to worng',
                'alert-type' => 'error'
            );
    
            return redirect()->back()->with($notification);
        }

        $sdate = date('Y-m-d',strtotime($request->check_in));
        $edate = date('Y-m-d',strtotime($request->check_out));
        $alldate = Carbon::create($edate)->subDay();
        $d_period = CarbonPeriod::create($sdate,$alldate);
        $dt_array = [];
        foreach ($d_period as $period) {
           array_push($dt_array, date('Y-m-d', strtotime($period)));
        }

        $check_date_booking_ids = RoomBookedDate::whereIn('book_date',$dt_array)->distinct()->pluck('booking_id')->toArray();

        $rooms = Room::withCount('room_numbers')->with(['type'])->where('status',1)->get();

        // Precompute availability per room to avoid DB queries in the view
        $availability = [];
        if (count($check_date_booking_ids) > 0) {
            $bookingCounts = \App\Models\Booking::withCount('assign_rooms')
                ->whereIn('id', $check_date_booking_ids)
                ->whereIn('rooms_id', $rooms->pluck('id')->toArray())
                ->get()
                ->groupBy('rooms_id')
                ->map(function ($group) {
                    return array_sum($group->pluck('assign_rooms_count')->toArray());
                })->toArray();

            foreach ($rooms as $r) {
                $booked = $bookingCounts[$r->id] ?? 0;
                $availability[$r->id] = max(0, ($r->room_numbers_count ?? 0) - $booked);
            }
        } else {
            foreach ($rooms as $r) {
                $availability[$r->id] = $r->room_numbers_count ?? 0;
            }
        }

        return view('frontend.room.search_room', compact('rooms', 'check_date_booking_ids'))->with('roomAvailability', $availability);

    } // End Method 


    public function SearchRoomDetails(Request $request,$id){
        $request->flash();
        $roomdetails = Room::find($id);
        $multiImage = MultiImage::where('rooms_id',$id)->get();
        $facility = Facility::where('rooms_id',$id)->get();

        $otherRooms = Room::where('id','!=', $id)->orderBy('id','DESC')->limit(2)->get();
        $room_id = $id;
        $room_numbers_count = \App\Models\RoomNumber::where('rooms_id', $id)->where('status', 'Active')->count();
        $checkIn = $request->query('check_in');
        $checkOut = $request->query('check_out');

        $activePricingRule = \App\Models\PricingRule::where('is_active', true)
            ->where(function($q) use ($checkIn) {
                $q->whereNull('start_date')
                  ->orWhere('start_date', '<=', $checkIn ?? now());
            })
            ->where(function($q) use ($checkOut) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', $checkOut ?? now());
            })
            ->orderBy('priority', 'desc')
            ->first();

        return view('frontend.room.search_room_details',compact('roomdetails','multiImage','facility','otherRooms','room_id','checkIn','checkOut','activePricingRule','room_numbers_count'));

    }// End Method 


    public function CheckRoomAvailability(Request $request){

        $sdate = date('Y-m-d',strtotime($request->check_in));
        $edate = date('Y-m-d',strtotime($request->check_out));
        $alldate = Carbon::create($edate)->subDay();
        $d_period = CarbonPeriod::create($sdate,$alldate);
        $dt_array = [];
        foreach ($d_period as $period) {
           array_push($dt_array, date('Y-m-d', strtotime($period)));
        }

        $check_date_booking_ids = RoomBookedDate::whereIn('book_date',$dt_array)->distinct()->pluck('booking_id')->toArray();

        $room = Room::withCount('room_numbers')->find($request->room_id);

        $bookings = Booking::withCount('assign_rooms')->whereIn('id',$check_date_booking_ids)->where('rooms_id',$room->id)->get()->toArray();

        $total_book_room = array_sum(array_column($bookings,'assign_rooms_count'));

        $av_room = @$room->room_numbers_count-$total_book_room;

        $toDate = Carbon::parse($request->check_in);
        $fromDate = Carbon::parse($request->check_out);
        $nights = $toDate->diffInDays($fromDate);

        return response()->json(['available_room'=>$av_room, 'total_nights'=>$nights ]);
    }// End Method 


}
 
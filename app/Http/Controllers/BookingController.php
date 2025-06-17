<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mail;
use DB;
use App\Models\{MdRule,MdRoomType,MdRoom,MdLocation,MdCancelPlan,
    MdCautionMoney,TdRoomBook,TdRoomLock,TdRoomBookDetails,TdUser,MdRoomRent,
    MdParam,MdState,TdRoomPayment,MdMenu,TdRoomMenu,TdPayment
};
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class BookingController extends Controller
{
    public function Search(Request $request)
    {
        // return $request;
        $location_id=$request->location_id;
        $room_type_id=$request->room_type_id;
        $from_date=$request->checkInDate;
        $to_date=$request->checkOutDate;
        $roomsCount=$request->rooms;

        $interval =Carbon::parse($from_date)->diff(Carbon::parse($to_date))->days;
        // return $interval;

        $lock_rooms=TdRoomLock::where('room_type_id',$request->room_type_id)
            ->whereDate('date','>=',date('Y-m-d',strtotime($request->checkInDate)))
            ->whereDate('date','<=',date('Y-m-d',strtotime($request->checkOutDate)))
            ->groupBy('room_id')
            ->get();
        // return $lock_rooms;
        $lock_room_array=[];
        foreach($lock_rooms as $lock_room){
            array_push($lock_room_array,$lock_room->room_id);
        }
        $total_rooms=MdRoom::where('room_type_id',$request->room_type_id)
        ->get();
        // return $total_rooms;
        // $room_type=MdRoomType::where('id',$room_type_id)->value('type');
        $room_types=MdRoomType::where('id',$room_type_id)->get();
        foreach ($room_types as $key => $value) {
            $room_type=$value->type;
            $max_person_number=$value->max_accomodation_number;
            $max_child_number=$value->max_child_number;
        }

        // if(count($lock_rooms) >= count($total_rooms)){
        //     return "<h2>Room not available</h2>";
        //     // booking cancel
        // }else{
            // return "else";
            // booking success
            
            if(count($lock_room_array) > 0){
                $datas=DB::table('md_room')
                    ->leftJoin('md_room_type','md_room_type.id','=','md_room.room_type_id')
                    ->select('md_room.*','md_room_type.type as room_type')
                    ->where('md_room.room_type_id',$room_type_id)
                    ->where('md_room.location_id',$location_id)
                    ->whereNotIn('md_room.id',$lock_room_array)
                    // ->groupBy('md_room.room_type_id')
                    ->get();
            }else{
                $datas=DB::table('md_room')
                    ->leftJoin('md_room_type','md_room_type.id','=','md_room.room_type_id')
                    ->select('md_room.*','md_room_type.type as room_type')
                    ->where('md_room.room_type_id',$room_type_id)
                    ->where('md_room.location_id',$location_id)
                    // ->whereNotIn('md_room.id',$lock_room_array)
                    // ->groupBy('md_room.room_type_id')
                    ->get();
            }
            // return $datas;
        // }
        $room_rent=MdRoomRent::where('location_id',$location_id)
            ->where('room_type_id',$room_type_id)
            ->orderBy('effective_date','DESC')
            ->get();
        // return $datas;
            // return $request;
        return view('room_details',['room_type'=>$room_type,'datas'=>$datas,
            'max_person_number'=>$max_person_number,'lock_room_array'=>$lock_room_array,
            'room_rent'=>$room_rent,'interval'=>$interval,'max_child_number'=>$max_child_number,
            'searched'=>$request
        ]);
        
    }

    public function Guest(Request $request)
    {
        // return $request;
        $location_id=$request->location_id;
        $room_type_id=$request->room_type_id;
        $from_date=$request->checkInDate;
        $to_date=$request->checkOutDate;
        $roomsCount=$request->rooms;

        $interval =Carbon::parse($from_date)->diff(Carbon::parse($to_date))->days;
        $room_rent=MdRoomRent::where('location_id',$location_id)
            ->where('room_type_id',$room_type_id)
            ->orderBy('effective_date','DESC')
            ->get();
        $menus=MdMenu::get();
        $food_cgst_charge=MdParam::where('id',10)->value('value');
        $food_sgst_charge=MdParam::where('id',11)->value('value');

        $menuselect_day=MdParam::where('id',12)->value('value');
        $today=date('Y-m-d');
        $interval_menuselect =Carbon::parse($today)->diff(Carbon::parse($from_date))->days;
        // return $interval_menuselect;
        return view('guest_details',['searched'=>$request,'interval'=>$interval,
            'room_rent'=>$room_rent,'menus'=>$menus,
            'food_cgst_charge'=>$food_cgst_charge,'food_sgst_charge'=>$food_sgst_charge,
            'menuselect_day'=>$menuselect_day,'interval_menuselect'=>$interval_menuselect
        ]);
    }


    public function Payment(Request $request)
    {
        // return $request;
        $location_id=$request->location_id;
        $room_type_id=$request->room_type_id;
        $from_date=$request->checkInDate;
        $to_date=$request->checkOutDate;
        $roomsCount=$request->rooms;

        $interval =Carbon::parse($from_date)->diff(Carbon::parse($to_date))->days;
        $room_rent=MdRoomRent::where('location_id',$location_id)
            ->where('room_type_id',$room_type_id)
            ->orderBy('effective_date','DESC')
            ->get();
        $advance_payment_needed=MdParam::where('id',7)->value('value');
        $advance_payment=MdParam::where('id',2)->value('value');

        $normal_rate =$room_rent[0]->normal_rate;
        $total_room_charage=$normal_rate * $request->rooms * $interval;
        $cgst_rate_percent =$room_rent[0]->cgst_rate;
        $sgst_rate_percent =$room_rent[0]->sgst_rate;
        $cgst_rate= ($total_room_charage * $cgst_rate_percent)/100 ;
        $sgst_rate= ($total_room_charage * $sgst_rate_percent)/100 ;
        $tot_amt= ($total_room_charage + $cgst_rate + $sgst_rate) ;
        //   Newly added Code for booking       ////
        $rooms=$request->rooms;
        $adult_no=0;
        $child_no=0;
        for ($i=1; $i <=$rooms; $i++) {
            $adult_name="adults_room".$i;
            $child1_room="child1_room".$i;
            $child2_room="child2_room".$i;
            $adult_no +=$request->$adult_name;
          
            if ($request->$child1_room>0) {
                $child_no +=1;
            }
            if ($request->$child2_room>0) {
                $child_no +=1;
            }
        }
       
        $booking_id='BKI'.date('YmdHis');
        // return $booking_id;
        $interval =Carbon::parse($request->checkInDate)->diff(Carbon::parse($request->checkOutDate))->days;
        // return $interval;
        $is_user=TdUser::where('mobile_no',$request->contact_no)->get();
        if(count($is_user)){
            $user_id=$is_user[0]['id'];
        }else{
            $data=TdUser::create(array(
                'name'=>$request->room1_adult1_first_name." ".$request->room1_adult1_last_name,
                'email'=>$request->email,
                'password'=>Hash::make('Pass@123'),
                'mobile_no'=>$request->contact_no,
                'active'=>'A',
            ));

            $user_id=$data->id;
        }
         $book_username = $request->room1_adult1_first_name;

         $template= "Dear $book_username, Thank you for the booking. Your profile has been successfully created. You can now manage your bookings and enjoy exclusive benefits. Login with your registered mobile no. - ICMARD";
        
       
        //die();
        $lock_rooms=TdRoomLock::where('room_type_id',$request->room_type_id)
            ->whereDate('date','>=',date('Y-m-d',strtotime($request->checkInDate)))
            ->whereDate('date','<=',date('Y-m-d',strtotime($request->checkOutDate)))
            ->groupBy('room_id')
            ->get();
        $lock_room_array=[];
        foreach($lock_rooms as $lock_room){
            array_push($lock_room_array,$lock_room->room_id);
        }

        $ava_rooms=MdRoom::where('location_id',$request->location_id)
            ->where('room_type_id',$request->room_type_id)
            ->whereNotIn('id',$lock_room_array)
            ->get();
        // return $ava_rooms;
        if (count($ava_rooms) >= $request->rooms) {
            // return "if";
            TdRoomBook::create(array(
                'booking_id'=> $booking_id,
                'location_id'=> $request->location_id,
                'user_id'=> $user_id,
                'from_date'=> date('Y-m-d',strtotime($request->checkInDate)),
                'to_date'=> date('Y-m-d',strtotime($request->checkOutDate)),
                'no_room'=> $request->rooms,
                'no_adult'=> $adult_no,
                'no_child'=> $child_no,
                'room_type_id'=> $request->room_type_id,
                'booking_time'=> date('Y-m-d H:i:s'),
                'booking_status'=> "A",
                'amount'=>$total_room_charage,
                'total_cgst_amount'=>$cgst_rate_percent,
                'total_sgst_amount'=>$sgst_rate_percent,
                'total_amount'=>$tot_amt,
                'final_amount'=>$tot_amt,
                'book_type' => 'ON',
                'emailid'   => $request->email,
                'mobileno'  => $request->contact_no
                // 'payment_status'=> "Paid",
                // 'created_by'=> auth()->user()->id,
            ));

            if ($request->payment!='') {
                TdRoomPayment::create(array(
                    'booking_id'=> $booking_id,
                    'amount'=> $tot_amt,
                    'payment_date'=> date('Y-m-d H:i:s'),
                    'payment_made_by'=> 'Payment',
                ));
            }

            for ($j=0; $j < $request->rooms; $j++) { 
                // how many dates are book room
                $room_id=$ava_rooms[$j]['id'];
                for ($i=0; $i < $interval; $i++) { 
                    $date=date('Y-m-d', strtotime($request->checkInDate. ' + '.$i.' day'));
                    // echo $j."-".$date;
                    // echo "  --  ";
                    TdRoomLock::create(array(
                        'date'=>$date,
                        'booking_id'=>$booking_id,
                        'room_id'=>$room_id,
                        'room_type_id'=>$request->room_type_id,
                        'status'=>'U',
                    ));
                }
            }
            
            for ($k=1; $k <= $request->rooms; $k++) { 
                $adult="adults_room".$k;
                $child1_room="child1_room".$k;
                $child2_room="child2_room".$k;

                for($l=1; $l<=$request->$adult ; $l++){
                    $room1_adult1_first_name="room".$k."_adult".$l."_first_name";
                    $room1_adult1_last_name="room".$k."_adult".$l."_last_name";
                    TdRoomBookDetails::create(array(
                        'customer_type_flag'=>$request->customer_type_flag,
                        'booking_id'=>$booking_id,
                        'first_name'=>$request->$room1_adult1_first_name,
                        // 'middle_name'=>$request->$adt_middle_name,
                        'last_name'=>$request->$room1_adult1_last_name,
                        'address'=>$request->address.",".$request->state.",".$request->post_code,
                        'child_flag'=>'N',
                        'organisation_gst_no'=>$request->GSTIN,
                        'pan'=>$request->PAN,
                        'tan'=>$request->TAN,
                        'registration_no'=>$request->RegistrationNo,
                    ));
                }
                
                $room1_child1_first_name="room".$k."_child1_first_name";
                $room1_child1_last_name="room".$k."_child1_last_name";
                $room1_child2_first_name="room".$k."_child2_first_name";
                $room1_child2_last_name="room".$k."_child2_last_name";
                if($request->$child1_room >0){
                    TdRoomBookDetails::create(array(
                        'customer_type_flag'=>$request->customer_type_flag,
                        'booking_id'=>$booking_id,
                        'first_name'=>$request->$room1_child1_first_name,
                        // 'middle_name'=>$request->$adt_middle_name,
                        'last_name'=>$request->$room1_child1_last_name,
                        'address'=>$request->address.",".$request->state.",".$request->post_code,
                        'child_flag'=>'Y',
                    ));
                }
                if($request->$child2_room >0){
                    TdRoomBookDetails::create(array(
                        'customer_type_flag'=>$request->customer_type_flag,
                        'booking_id'=>$booking_id,
                        'first_name'=>$request->$room1_child2_first_name,
                        // 'middle_name'=>$request->$adt_middle_name,
                        'last_name'=>$request->$room1_child2_last_name,
                        'address'=>$request->address.",".$request->state.",".$request->post_code,
                        'child_flag'=>'Y',
                    ));
                }
            }

            // if($menus !=''){
            //     for ($m=0; $m < count($menus); $m++) { 
            //         $rate=MdMenu::where('id',$menus[$m])->value('price');
            //         TdRoomMenu::create(array(
            //             'booking_id' =>$booking_id,
            //             'menu_id'=>$menus[$m],
            //             'no_of_head' =>$no_of_head[$m],
            //             'rate' =>$rate,
            //             'amount' => ((int)$no_of_head[$m] * (int)$rate),
            //         ));
            //     }
            // }
        
            $success='S';
            $failed_id='';
        }else{
            // return "else";
            $success='F';
            $booking_id='';
            $failed_id='Fail_'.rand(0000,9999);
        }
        $this->sendsms($request->contact_no,$template);

         //  End Newly added code for booking   //

        // return view('payment',['searched'=>$request,'interval'=>$interval,'room_rent'=>$room_rent,
        // 'advance_payment_needed'=>$advance_payment_needed,'advance_payment'=>$advance_payment
        // ]);

        return redirect()->route('paymentgateway',['booking_id'=>$booking_id]);
    }

    // public function ConfirmPayment(Request $request)
    // {
    //     // return $request;
    //     $menus=json_decode($request->menus,true);
    //     $no_of_head=json_decode($request->no_of_head,true);
    //     // return $menus;
    //     $rooms=$request->rooms;
    //     $adult_no=0;
    //     $child_no=0;
    //     for ($i=1; $i <=$rooms; $i++) {
    //         $adult_name="adults_room".$i;
    //         $child1_room="child1_room".$i;
    //         $child2_room="child2_room".$i;
    //         $adult_no +=$request->$adult_name;
    //         // $child_no +=$request->$child1_room;
    //         // $child_no +=$request->$child2_room;
    //         if ($request->$child1_room>0) {
    //             $child_no +=1;
    //         }
    //         if ($request->$child2_room>0) {
    //             $child_no +=1;
    //         }
    //     }

       
    //     $booking_id='BKI'.date('YmdHis');
    //     // return $booking_id;
    //     $interval =Carbon::parse($request->checkInDate)->diff(Carbon::parse($request->checkOutDate))->days;
    //     // return $interval;
    //     $is_user=TdUser::where('email',$request->email)->get();
    //     if(count($is_user)){
    //         $user_id=$is_user[0]['id'];
    //     }else{
    //         $data=TdUser::create(array(
    //             'name'=>$request->room1_adult1_first_name." ".$request->room1_adult1_last_name,
    //             'email'=>$request->email,
    //             'password'=>Hash::make('Pass@123'),
    //             'mobile_no'=>$request->contact_no,
    //             'active'=>'A',
    //         ));

    //         $user_id=$data->id;
    //     }

    //     $lock_rooms=TdRoomLock::where('room_type_id',$request->room_type_id)
    //         ->whereDate('date','>=',date('Y-m-d',strtotime($request->checkInDate)))
    //         ->whereDate('date','<=',date('Y-m-d',strtotime($request->checkOutDate)))
    //         ->groupBy('room_id')
    //         ->get();
    //     $lock_room_array=[];
    //     foreach($lock_rooms as $lock_room){
    //         array_push($lock_room_array,$lock_room->room_id);
    //     }

    //     $ava_rooms=MdRoom::where('location_id',$request->location_id)
    //         ->where('room_type_id',$request->room_type_id)
    //         ->whereNotIn('id',$lock_room_array)
    //         ->get();
    //     // return $ava_rooms;
    //     if (count($ava_rooms) >= $request->rooms) {
    //         // return "if";
    //         TdRoomBook::create(array(
    //             'booking_id'=> $booking_id,
    //             'location_id'=> $request->location_id,
    //             'user_id'=> $user_id,
    //             'from_date'=> date('Y-m-d',strtotime($request->checkInDate)),
    //             'to_date'=> date('Y-m-d',strtotime($request->checkOutDate)),
    //             'no_room'=> $request->rooms,
    //             'no_adult'=> $adult_no,
    //             'no_child'=> $child_no,
    //             'room_type_id'=> $request->room_type_id,
    //             'accomodation_type' => 'R',
    //             'booking_time'=> date('Y-m-d H:i:s'),
    //             'booking_status'=> "A",
    //             'amount'=>$request->amount,
    //             'total_cgst_amount'=>$request->total_cgst_amount,
    //             'total_sgst_amount'=>$request->total_sgst_amount,
    //             'total_amount'=>$request->total_amount,
    //             'final_amount'=>$request->total_amount,
    //             'book_type' => 'ON',
    //             'emailid'   => $request->email,
    //             'mobileno'  => $request->contact_no
    //             // 'payment_status'=> "Paid",
    //             // 'created_by'=> auth()->user()->id,
    //         ));

    //         if ($request->payment!='') {
    //             TdRoomPayment::create(array(
    //                 'booking_id'=> $booking_id,
    //                 'amount'=> $request->payment,
    //                 'payment_date'=> date('Y-m-d H:i:s'),
    //                 'payment_made_by'=> 'Payment',
    //             ));
    //         }

    //         for ($j=0; $j < $request->rooms; $j++) { 
    //             // how many dates are book room
    //             $room_id=$ava_rooms[$j]['id'];
    //             for ($i=0; $i < $interval; $i++) { 
    //                 $date=date('Y-m-d', strtotime($request->checkInDate. ' + '.$i.' day'));
    //                 // echo $j."-".$date;
    //                 // echo "  --  ";
    //                 TdRoomLock::create(array(
    //                     'date'=>$date,
    //                     'booking_id'=>$booking_id,
    //                     'room_id'=>$room_id,
    //                     'room_type_id'=>$request->room_type_id,
    //                     'status'=>'U',
    //                 ));
    //             }
    //         }
            
    //         for ($k=1; $k <= $request->rooms; $k++) { 
    //             $adult="adults_room".$k;
    //             $child1_room="child1_room".$k;
    //             $child2_room="child2_room".$k;

    //             for($l=1; $l<=$request->$adult ; $l++){
    //                 $room1_adult1_first_name="room".$k."_adult".$l."_first_name";
    //                 $room1_adult1_last_name="room".$k."_adult".$l."_last_name";
    //                 TdRoomBookDetails::create(array(
    //                     'customer_type_flag'=>$request->customer_type_flag,
    //                     'booking_id'=>$booking_id,
    //                     'first_name'=>$request->$room1_adult1_first_name,
    //                     // 'middle_name'=>$request->$adt_middle_name,
    //                     'last_name'=>$request->$room1_adult1_last_name,
    //                     'address'=>$request->address.",".$request->state.",".$request->post_code,
    //                     'child_flag'=>'N',
    //                     'organisation_gst_no'=>$request->GSTIN,
    //                     'pan'=>$request->PAN,
    //                     'tan'=>$request->TAN,
    //                     'registration_no'=>$request->RegistrationNo,
    //                 ));
    //             }
                
    //             $room1_child1_first_name="room".$k."_child1_first_name";
    //             $room1_child1_last_name="room".$k."_child1_last_name";
    //             $room1_child2_first_name="room".$k."_child2_first_name";
    //             $room1_child2_last_name="room".$k."_child2_last_name";
    //             if($request->$child1_room >0){
    //                 TdRoomBookDetails::create(array(
    //                     'customer_type_flag'=>$request->customer_type_flag,
    //                     'booking_id'=>$booking_id,
    //                     'first_name'=>$request->$room1_child1_first_name,
    //                     // 'middle_name'=>$request->$adt_middle_name,
    //                     'last_name'=>$request->$room1_child1_last_name,
    //                     'address'=>$request->address.",".$request->state.",".$request->post_code,
    //                     'child_flag'=>'Y',
    //                 ));
    //             }
    //             if($request->$child2_room >0){
    //                 TdRoomBookDetails::create(array(
    //                     'customer_type_flag'=>$request->customer_type_flag,
    //                     'booking_id'=>$booking_id,
    //                     'first_name'=>$request->$room1_child2_first_name,
    //                     // 'middle_name'=>$request->$adt_middle_name,
    //                     'last_name'=>$request->$room1_child2_last_name,
    //                     'address'=>$request->address.",".$request->state.",".$request->post_code,
    //                     'child_flag'=>'Y',
    //                 ));
    //             }
    //         }

    //         if($menus !=''){
    //             for ($m=0; $m < count($menus); $m++) { 
    //                 $rate=MdMenu::where('id',$menus[$m])->value('price');
    //                 TdRoomMenu::create(array(
    //                     'booking_id' =>$booking_id,
    //                     'menu_id'=>$menus[$m],
    //                     'no_of_head' =>$no_of_head[$m],
    //                     'rate' =>$rate,
    //                     'amount' => ((int)$no_of_head[$m] * (int)$rate),
    //                 ));
    //             }
    //         }
    //         $success='S';
    //         $failed_id='';
    //     }else{
    //         // return "else";
    //         $success='F';
    //         $booking_id='';
    //         $failed_id='Fail_'.rand(0000,9999);
    //     }
     
    //      return redirect()->route('paymentgateway',['booking_id'=>$booking_id]);
      
    // }

    public function sendsms($phone_no,$template)
    {
            $phone_no = $phone_no; // Replace with the actual phone number
            // Message text
            $text = $template;
            // URL encode the text to handle special characters
            $encoded_text = urlencode($text);
            $user = env('SMS_TEMP_USER');
            $pass = env('SMS_TEMP_PASS');
            $senderid = env('SMS_TEMP_SENDID');
            // Construct the URL with the encoded text
            // $url = 'https://bulksms.sssplsales.in/api/api_http.php?username='.$user.'&password='.$pass.'&senderid='.$senderid.'&to=' . $phone_no . '&text=' . $encoded_text . '&route=Informative&type=text';
            $url = 'http://sms.synergicapi.in/api.php?username='.$user.'&apikey='.$pass.'&senderid='.$senderid.'&route=OTP&mobile='.$phone_no.'&text='.$encoded_text;
            // Initialize cURL
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30, // Set a reasonable timeout
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ));

            // Execute the request
            $response = curl_exec($curl);
            // Check for cURL errors
            if (curl_errno($curl)) {
                $error_msg = curl_error($curl);
                echo "cURL Error: $error_msg";
            } else {
                // Output the response from the API
                echo $response;
            }
            // Close cURL session
            curl_close($curl);
    }

    public function PaymentSuccess(Request $request)
    {
        // return $request;
        $booking_id=$request->booking_id;
        // $hall_book=TdHallbook::where('booking_id',$booking_id)->get();
        $hall_book=DB::table('td_room_book')
            ->leftJoin('md_location','md_location.id','=','td_room_book.location_id')
            ->leftJoin('td_users','td_users.id','=','td_room_book.user_id')
            ->leftJoin('md_room_type','md_room_type.id','=','td_room_book.room_type_id')
            ->select('td_room_book.*','md_location.location as location_name','td_users.email as email','td_users.mobile_no as mobile_no','md_room_type.type as type')
            ->where('td_room_book.booking_id',$booking_id)
            ->get();
            $hall_book_details=TdRoomBookDetails::where('booking_id',$booking_id)->get();
            $payment_details=TdPayment::where('booking_id',$booking_id)->get();

            //  $email = 'lk60588@gmail.com'; 
            //  $template_data = ['hall_book'=>$hall_book,'hall_book_details'=>$hall_book_details];
            // Mail::send(['html' => 'booking_confirm_message'], $template_data,
            //                 function ($message) use ($email) {
            //                     $message->from('lokesh@synergicsoftek.com','Lokesh');
            //                     $message->to('lk60588@gmail.com')
            //                     ->subject('Booking Confirm');
            // }); 
        
        
        return view('confirm_payment',['searched'=>$request,'hall_book'=>$hall_book,'payment_details'=>$payment_details,
        'hall_book_details'=>$hall_book_details
        ]);
    }

    public function encrypt_cc($plainText,$key)
	{
		$key = $this->hextobin(md5($key));
		$initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
		$openMode = openssl_encrypt($plainText, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $initVector);
		$encryptedText = bin2hex($openMode);
		return $encryptedText;
	}

	public function decrypt_cc($encryptedText,$key)
	{
		$key = $this->hextobin(md5($key));
		$initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
		$encryptedText = $this->hextobin($encryptedText);
		$decryptedText = openssl_decrypt($encryptedText, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $initVector);
		return $decryptedText;
	}
	//*********** Padding Function *********************

	public function pkcs5_pad ($plainText, $blockSize)
	{
	    $pad = $blockSize - (strlen($plainText) % $blockSize);
	    return $plainText . str_repeat(chr($pad), $pad);
	}

	//********** Hexadecimal to Binary function for php 4.0 version ********

	public function hextobin($hexString) 
   	{ 
        	$length = strlen($hexString); 
        	$binString="";   
        	$count=0; 
        	while($count<$length) 
        	{       
        	    $subString =substr($hexString,$count,2);           
        	    $packedString = pack("H*",$subString); 
        	    if ($count==0)
		    {
				$binString=$packedString;
		    } 
        	    
		    else 
		    {
				$binString.=$packedString;
		    } 
        	    
		    $count+=2; 
        	} 
  	        return $binString; 
    }

    public function paymentgateway(Request $request){

            $booking_id=$request->booking_id;
            $redirect_url = url('/paymentgatewayres'); 
            $cancel_url   = url('/paymentcancel');
            
            $test_url =env('HDFC_PRODURL');
            $merchant_data='2';
            $merchant_id =2908482;
            $transaction_id = time().rand(10,100);
            $working_key=env('HDFC_WORKINGKEY') ;//Shared by CCAVENUES
            $access_code=env('HDFC_ACCESSCODE') ;//Shared by CCAVENUES
            $booking_details=TdRoomBook::where('booking_id',$booking_id)->get();
            $guest_details=TdRoomBookDetails::where('booking_id',$booking_id)->get();
            $booking_amt = $booking_details[0]->total_amount;
            $pg_charge = round($booking_amt*.02);
            $tot_amt = $booking_details[0]->total_amount+$pg_charge;
            $txtAdditionalInfo_pgcharge = $pg_charge;
            $merchant_data.='tid='.$transaction_id.'&';
            $merchant_data.='merchant_id='.$merchant_id.'&';
            $merchant_data.='order_id='.$booking_id.'&';
            $merchant_data.='amount='.$tot_amt.'&';
            $merchant_data.='currency=INR&';
            $merchant_data.='redirect_url='.$redirect_url.'&';
            $merchant_data.='cancel_url='.$cancel_url.'&';
            $merchant_data.='language=EN&';

            //  Code for Billdesk payment gateway 
            
           // $security_id = "bdskuaty";
           // $checksum_key = "G3eAmyVkAzKp8jFq0fqPEqxF4agynvtJ";
           // $merchant_idb = "BDSKUATY";
           // $billdesk_url  = 'https://uat.billdesk.com/pgidsk/PGIMerchantPayment';

            // Live credential
               $security_id = env('BILLDESK_SECURITY_ID');
               $checksum_key = env('BILLDESK_CHECKSUM_KEY');
               $merchant_idb = env('BILLDESK_MERCHANT_ID');
               $billdesk_url  = 'https://pgi.billdesk.com/pgidsk/PGIMerchantPayment';

            $customer_id = $transaction_id;
            $amount = $tot_amt;
            $txtAdditionalInfo1 = $booking_id;
            $return_url = url('/paymentgatewayres_billdesk');
            $str = $merchant_idb.'|'.$customer_id.'|NA|'.$amount.'|NA|NA|NA|INR|NA|R|'.$security_id.'|NA|NA|F|'.$txtAdditionalInfo1.'|'.$txtAdditionalInfo_pgcharge.'|NA|NA|NA|NA|NA|'.$return_url;
            $checksumlower = hash_hmac("sha256", $str, $checksum_key, false);
            $checksum = strtoupper($checksumlower);
            $newmsg = $str.'|'.$checksum;
           
            TdPayment::create(array(
                'trans_date'=> date('Y-m-d H:i:s'),
                'amount'=> $tot_amt,
                'pg_charge'=> $pg_charge,
                'booking_id'=> $request->booking_id,
                'transaction_id'=> $transaction_id,
                'email'=> $booking_details[0]->emailid,
                'contact'=> $booking_details[0]->mobileno,
                'room_type_id'=> $request->room_type_id,
                'booking_time'=> date('Y-m-d H:i:s'),
                'created_by' =>$booking_id,
                'created_ip' =>$_SERVER['REMOTE_ADDR']
            ));
           
            $encrypted_data=$this->encrypt_cc($merchant_data,$working_key);
            return view('payment_pg',['tot_amt'=>$tot_amt,'encrypted_data'=>$encrypted_data,'test_url'=>$test_url,'pg_charge'=>$pg_charge,
            'msg'=>$newmsg,'billdesk_url'=>$billdesk_url,
            'booking_details'=>$booking_details,'access_code'=>$access_code,'guest_details'=>$guest_details]);
          // return $booking_details;


    }
    public function paymentgatewayres(Request $request){
        //return 'test';
        $working_key=env('HDFC_WORKINGKEY');//Shared by CCAVENUES
        $encResponse=$request->encResp;			//This is the response sent by the CCAvenue Server
        $rcvdString=$this->decrypt_cc($encResponse,$working_key);		//Crypto Decryption used as per the specified working key.
        $order_status="";
        $decryptValues=explode('&', $rcvdString);
        $dataSize=sizeof($decryptValues);
        $failed_id = '';
        $bank_ref_no = '';
        $failure_message = '';
        $payment_mode = '';
        $card_name = '';
        $status_code = '';
        $status_message = '';
        $success = '';
       // echo "<center>";
        for($i = 0; $i < $dataSize; $i++) 
        {
            $information=explode('=',$decryptValues[$i]);
            if($i==3)	$order_status=$information[1];
            if($i==0)	$booking_id=$information[1];
            if($i==10)	$amount= $information[1];
            if($i==1)	$tracking_id = $information[1];
            if($i==2)	$bank_ref_no = $information[1];
            if($i==4)	$failure_message = $information[1];
            if($i==5)	$payment_mode = $information[1];
            if($i==6)	$card_name = $information[1];
            if($i==7)	$status_code = $information[1];
            if($i==8)	$status_message = $information[1];
        }
      
        if($order_status==="Success")
        {
            $success = 'Success';
        }
        else if($order_status==="Aborted")
        {
            $failed_id = 'Failure';
        }
        else if($order_status==="Failure")
        {
            $failed_id = 'Failure';
        }
        else
        {
            $failed_id = 'Failure';
        }
        // echo "<table cellspacing=4 cellpadding=4>";
        $updateDetails = ['status' => $order_status,'tracking_id' => $tracking_id,
        'bank_ref_no' =>$bank_ref_no,'failure_message'=>$failure_message,'payment_mode'=>$payment_mode,
        'card_name'=>$card_name,'status_code'=>$status_code,'status_message' => $status_message];
   
        DB::table('td_payment')->where('booking_id',$booking_id)->where('amount', $amount)->update($updateDetails);
       
        $payments=TdPayment::where('booking_id',$booking_id)->get();
        if($payments[0]->booking_id == $booking_id && $payments[0]->amount == $amount){
            if($order_status==="Success")
            {
            DB::table('td_room_lock')->where('booking_id',$booking_id)->update(['status' =>'L']);

            DB::table('td_room_book')->where('booking_id',$booking_id)->update(
                ['final_amount' =>$amount,'full_paid' => 'Y','final_bill_flag'=>'Y','total_amount'=>$amount,'paid_amount'=>$amount]);
          
            }
            TdRoomPayment::create(array(
                'booking_id' =>$booking_id,
                'amount' =>$amount,
                'payment_date' => date('Y-m-d'),
                'payment_made_by' =>'ONLINE',
                'cheque_no' =>'',
                'cheque_dt' =>'',
                'payment_id' =>''
            ));
           return redirect()->route('paymentSuccess',['booking_id'=>$booking_id,'failed_id'=>$failed_id,'success'=>$success]);

        }else{
            return redirect()->route('paymentSuccess',['booking_id'=>$booking_id,'failed_id'=>'Failure','success'=>'']);
        }

    
    }
    public function paymentgatewayres_billdesk(Request $request){
        
        $msg=$request->msg;			//This is the response sent by the Bill Desk
        $arg = $msg;
        
        list($mid,$transaction_id,$tr_refno,$bank_refno,$amount,$bankid,$bankmerid,$txntype,$currency,$ItemCode,$SecurityType,$SecurityID,$SecurityPassword,$TxnDate,$AuthStatus,$SettlementType,$txtAdditionalInfo1,$txtAdditionalInfo2,$AdditionalInfo3,$AdditionalInfo4,$AdditionalInfo5,$AdditionalInfo6,$AdditionalInfo7,$ErrorStatus,$ErrorDescription,$CheckSum)=explode("|",$arg);

        $failed_id = '';
        $bank_ref_no = '';
        $failure_message = '';
        $payment_mode = '';
        $card_name = '';
        $status_code = '';
        $status_message = '';
        $success = '';
        $tracking_id = '';
        $booking_id = $txtAdditionalInfo1;
        $pg_charges = $txtAdditionalInfo2;
       // echo "<center>";
        
      
        if($AuthStatus=="0300")
        {
            $success = 'Success';
            $order_status = 'Success';
        }
        else
        {
            $failed_id = 'Failure';
            $order_status = 'Failure';
        }
        
        $updateDetails = ['status' => $order_status,'tracking_id' => $tr_refno,'payment_gateway' => 'BILLDESK',
        'bank_ref_no' =>$bank_refno,'failure_message'=>$failure_message,'payment_mode'=>$txntype,
        'card_name'=>$card_name,'status_code'=>$AuthStatus,'status_message' => $status_message];
       
        DB::table('td_payment')->where('booking_id',$booking_id)->where('amount', $amount)->update($updateDetails);
        if($AuthStatus=="0300"){
        DB::table('td_room_lock')->where('booking_id',$booking_id)->update(['status' =>'L']);

        DB::table('td_room_book')->where('booking_id',$booking_id)->update(
            ['final_amount' =>$amount-$pg_charges,'full_paid' => 'Y','final_bill_flag'=>'Y','total_amount'=>$amount-$pg_charges,'paid_amount'=>$amount-$pg_charges]);
        }    
        TdRoomPayment::create(array(
            'booking_id' =>$booking_id,
            'amount' =>$amount-$pg_charges,
            'payment_date' => date('Y-m-d'),
            'payment_made_by' =>'ONLINE',
            'cheque_no' =>'',
            'cheque_dt' =>'',
            'payment_id' =>''
        ));
      
       
       return redirect()->route('paymentSuccess',['booking_id'=>$booking_id,'failed_id'=>$failed_id,'success'=>$success]);
        //return $arg;
    }


    public function paymentcancel(Request $request){

        return view('payment_cancel');
    }

    public function Testemail(){

        $email = 'lk60588@gmail.com'; 
        $template_data = ['Username'=> 'Lokesh','link'=> '','booking_id'=>$booking_id,'from'=>'','to'=>''];
        Mail::send(['html' => 'booking_confirm_message'], $template_data,
                        function ($message) use ($email) {
                            $message->from('lokesh@synergicsoftek.com','Lokesh');
                            $message->to('lk60588@gmail.com')
                            ->subject('Booking Confirm');
        }); 

    }

    public function hdfcorderStatusTracker(Request $request){

        $merchant_json_data =array('order_no' => $request->booking_id,'reference_no' =>$request->reference_no);
        $headers = array('Content-Type'=>'application/json');
        //print_r($merchant_json_data);
        $working_key=env('HDFC_WORKINGKEY');//Shared by CCAVENUES
        $access_code=env('HDFC_ACCESSCODE');//Shared by CCAVENUES
        $merchant_data = json_encode($merchant_json_data);
       // echo $merchant_data; die();
        $encrypted_data = $this->encrypt_cc($merchant_data, $working_key);
        $final_data = 'enc_request='.$encrypted_data.'&access_code='.$access_code.'&command=orderStatusTracker&request_type=JSON&response_type=JSON';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, env('HDFC_STATUSURL'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$headers) ;
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $final_data);
        // Get server response ...
        $result = curl_exec($ch);
        curl_close($ch);
        $status = '';
        $information = explode('&', $result);

        $dataSize = sizeof($information);
        for ($i = 0; $i < $dataSize; $i++) {
            $info_value = explode('=', $information[$i]);
            if ($info_value[0] == 'enc_response') {
                $status = $this->decrypt_cc(trim($info_value[1]), $working_key);
                
            }
        }

        echo 'Status revert is: ' . $status.'<pre>';
        $obj = json_decode($status);
        //print_r($obj);

    }

    
}

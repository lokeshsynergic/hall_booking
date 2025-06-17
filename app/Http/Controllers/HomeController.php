<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use Session;
use App\Models\{MdRule,MdRoomType,MdRoom,MdLocation,MdCancelPlan,
    MdCautionMoney,TdRoomBook,TdRoomLock,TdRoomBookDetails,TdUser,MdRoomRent,Tdotp,
    MdParam,MdState
};
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class HomeController extends Controller
{
    public function Show(Request $request)
    {
        $locations=MdLocation::where('id',1)->get();
        $room_types=MdRoomType::get();
        $book_date=MdParam::where('id',1)->value('value');
        // return $locations;
        $Date=date('Y-m-d');
        $advance_book_date=date('Y-m-d', strtotime($Date. ' + '.$book_date.' months'));

        $checking_time=MdParam::where('id',4)->value('value');
        $checkout_time=MdParam::where('id',5)->value('value');
        return view('index',['locations'=>$locations,'room_types'=>$room_types,'advance_book_date'=>$advance_book_date,
            'checking_time'=>$checking_time,'checkout_time'=>$checkout_time
        ]);
    }

    public function RoomTypeAjax(Request $request)
    {
        $location_id=$request->location_id;
        $code=$request->code;
        if($location_id == 1){
            $room_types=MdRoomType::where('location_id',$location_id)->where('code','=',$code)->whereIn('id',[1, 2, 3])->get();
        }else{
            $room_types=MdRoomType::where('location_id',$location_id)->where('code','=',$code)->get();
        }
        
        
        return view('room_type_ajax',['room_types'=>$room_types]);
    }

    public function MaxGuestDetailsAjax(Request $request)
    {
        $location_id=$request->location_id;
        $room_type_id=$request->room_type_id;
        $code=$request->code;

        $room_types=MdRoomType::where('id',$room_type_id)->get();
        foreach ($room_types as $key => $value) {
            $room_type=$value->type;
            $max_person_number=$value->max_accomodation_number;
            $max_child_number=$value->max_child_number;
        }

        $datas=[];
        $datas['room_type']=$room_type;
        $datas['room_type_id']=$room_type_id;
        $datas['max_person_number']=$max_person_number;
        $datas['max_child_number']=$max_child_number;
        echo json_encode($datas);
    }

    public function HallbookingDates(Request $request)
    {
        $days=$request->days;
        $book_date=MdParam::where('id',1)->value('value');
        // return $book_date;
        $Date=date('Y-m-d');
        $advance_book_date=date('Y-m-d', strtotime($Date. ' + '.$book_date.' months'));
        return view('hall_booking_dates_ajax',['days'=>$days,'advance_book_date'=>$advance_book_date]);
    }

    public function GuestDetailsFieldsAjax(Request $request)
    {
        $rooms=$request->rooms;
        $room_type_id = $request->room_type_id;
        return view('rooms_guest_details_ajax',['rooms'=>$rooms,'room_type_id'=>$room_type_id]);
    }

    public function Userlogin(Request $request)
    {
        $datas = '';
        return view('userdashboard.login');
        
    }
    public function generateotp(Request $request)
    {
        //$request->validate(['mobile_num' => 'required|mobile_num']);
        $mobileno_email = $request->mobile_num;
        $mobileregex = "/^[6-9][0-9]{9}$/" ;  
        if(preg_match($mobileregex, $mobileno_email)){
            $user_info=TdUser::where('mobile_no',$mobileno_email)->get();
        }else{
            $user_info=TdUser::where('email',$mobileno_email)->get();
        }
        
        $count = $user_info->count();
        //    GENERATING OTP
        $generator = "1358902647";
        $result = ''; 
        for ($i = 1; $i <= 5; $i++) { 
            $result .= substr($generator, (rand()%(strlen($generator))), 1); 
        } 
     
        if($count > 0){
            Tdotp::create(array(
                            'mobileno_email'=>$mobileno_email,
                            'otp'=>$result,
                            'status'=>1,
                            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                            'expires_at' => Carbon::now()->addMinutes(5)->format('Y-m-d H:i:s'),
                            'updated_at' => NULL
                        ));
                        
            $status = $this->sendotp($mobileno_email,$result);
            
            return 1;              
        }else{
            return 0;
        }
       // return redirect()->route('otp',['mobileno_email'=>$mobileno_email]);
       
    }
    public function otp(Request $request)
    {
        $mobileno_email = $request->mobileno_email;
        return view('userdashboard.otp',['mobileno_email'=>$mobileno_email]);
        
    }
    public function validateotp($request)
    {
        $mobileno=$request->mobile_num;
        $otp=$request->otp;
        $otpRecord = Tdotp::where('mobileno_email', $mobileno)->where('otp',$otp)
        ->where('expires_at', '>', Carbon::now()->format('Y-m-d H:i:s'))
        ->get();
        
        if(count($otpRecord) > 0) {
            //  ****  After Validating Otp Data is deleting    ***   //
            foreach ($otpRecord as $otpRecor) {
                $otpRecor->delete();
            }
            return 1;
        }else{
            return 0;
        }
    }
    public function userloginprocess(Request $request)
    {
        
        $mobileno_email = $request->mobile_num;
        $status = $this->validateotp($request);
       
        if($status == 1) {
            $mobileregex = "/^[6-9][0-9]{9}$/" ;
            if(preg_match($mobileregex, $mobileno_email)){
                $user = TdUser::where('mobile_no', $mobileno_email)->first();

                if ($user) {
                    // If user is found, log them in
                    Session::put('user_ft', $user->id);
                    Session::put('user_ftname', $user->name);
                }
            }
            session()->flash('success', 'Your action is successful!');
            return redirect()->route('Userdash');
        }else{
            session()->flash('error', 'OTP is not valid');
            return redirect()->route('Userlogin');
        }
       
    }
    public function userlogout(Request $request){

        Auth::logout(); // Log out the user
        // Optionally, you can invalidate the session (for additional security)
        Session::flush();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    
        // Redirect to the login page or any other page
        return redirect()->route('Userlogin');
      
    }
    public function sendotp($phone_no,$otp)
    {
            $otp = $otp; // Example OTP, replace with actual OTP generation logic
            $phone_no = $phone_no; // Replace with the actual phone number
            // Message text
            $text = 'OTP for login is '.$otp.'.This code is valid for 5 minutes. Please do not share this OTP with anyone. - ICMARD';
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

}

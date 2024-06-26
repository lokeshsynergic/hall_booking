@extends('common.master')
@section('content')


<div class="wrapper">
    <div class="col-md-12">
        <ul class="confirmation-step">
            <li><a href="#" class="active"><span>1</span> Hotel Details</a></li>
            <li><a href="#" class="active"><span>2</span> Guest Details</a></li>
            <li><a href="#" class="active"><span>3</span> Payment</a></li>
            <li><a href="#"><span>4</span> Confirm</a></li>
        </ul>
    </div>
</div>


<?php 
    $normal_rate =$room_rent[0]->normal_rate;
    $total_room_charage=$normal_rate * 1 * $interval;
    $cgst_rate_percent =$room_rent[0]->cgst_rate;
    $sgst_rate_percent =$room_rent[0]->sgst_rate;
    $cgst_rate= ($total_room_charage * $cgst_rate_percent)/100 ;
    $sgst_rate= ($total_room_charage * $sgst_rate_percent)/100 ;
    $tot_amt= ($total_room_charage + $cgst_rate + $sgst_rate) ;
?>
<div class="bookingInnerPage">
    <div class="wrapper">
        <div class="col-sm-8 float-left innerContentTxt">
            <div class="card">
                <h4 class="title222">Payment Details</h4>
                <hr>

                <div class="card-body border rounded set mb-3">

                    <div id="accordion" class="w-100 passanger-details">
                        <div class="card-body border rounded set mb-3">
                            <div class="card-header bg-primary-light font-weight-500 h6 border-0" id="headingOne">
                                <a href="javascript:void(0)" data-toggle="collapse" data-target="#collapse1"
                                    aria-expanded="false" aria-controls="collapseOne">
                                    Credit or Debit Card
                                </a>
                            </div>
                            <form id="credit_or_debit" name="credit_or_debit" method="post"
                                action="{{route('HallConfirmPayment')}}">
                                @csrf
                                <input type="text" hidden name="location_id" id="location_id"
                                    value="{{$searched->location_id}}">
                                <input type="text" hidden name="room_type_id" id="room_type_id"
                                    value="{{$searched->room_type_id}}">
                                <input type="text" hidden name="hall_no_id" id="hall_no_id"
                                    value="{{$searched->hall_no_id}}">
                                <input type="text" hidden name="hallbookingdate" id="hallbookingdate"
                                    value="{{$searched->hallbookingdate}}">
                                <input type="text" hidden name="hall_no_id" id="hall_no_id"
                                    value="{{$searched->hall_no_id}}">
                                <input type="text" hidden name="days" id="days" value="{{$searched->days}}">
                                <input type="text" hidden name="laptop_prajector" id="laptop_prajector"
                                    value="{{$searched->laptop_prajector}}">
                                <input type="text" hidden name="sound_system" id="sound_system"
                                    value="{{$searched->sound_system}}">
                                <input type="text" hidden name="catering_service" id="catering_service"
                                    value="{{$searched->catering_service}}">

                                <input type="text" hidden name="room_adult_first_name" id="room_adult_first_name"
                                    value="{{$searched->room_adult_first_name}}">
                                <input type="text" hidden name="room_adult_last_name" id="room_adult_last_name"
                                    value="{{$searched->room_adult_last_name}}">

                                <input type="text" hidden name="customer_type_flag" id="customer_type_flag"
                                    value="{{$searched->customer_type_flag}}">
                                <input type="text" hidden name="GSTIN" id="GSTIN" value="{{$searched->GSTIN}}">
                                <input type="text" hidden name="PAN" id="PAN" value="{{$searched->PAN}}">
                                <input type="text" hidden name="TAN" id="TAN" value="{{$searched->TAN}}">
                                <input type="text" hidden name="RegistrationNo" id="RegistrationNo"
                                    value="{{$searched->RegistrationNo}}">

                                <input type="text" hidden name="post_code" id="post_code"
                                    value="{{$searched->post_code}}">
                                <input type="text" hidden name="state" id="state" value="{{$searched->state}}">
                                <input type="text" hidden name="address" id="address" value="{{$searched->address}}">
                                <input type="text" hidden name="contact_no" id="contact_no"
                                    value="{{$searched->contact_no}}">

                                <input type="text" hidden name="amount" id="amount" value="{{$total_room_charage}}">
                                <input type="text" hidden name="total_cgst_amount" id="total_cgst_amount"
                                    value="{{$cgst_rate_percent}}">
                                <input type="text" hidden name="total_sgst_amount" id="total_sgst_amount"
                                    value="{{$sgst_rate_percent}}">
                                <input type="text" hidden name="total_amount" id="total_amount" value="{{$tot_amt}}">
                                <input type="text" hidden name="menus" id="menus"
                                    value="{{json_encode($searched->menus)}}">
                                <input type="text" hidden name="no_of_head" id="no_of_head"
                                    value="{{json_encode($searched->no_of_head)}}">

                                <input type="text" hidden name="email" id="email" value="{{$searched->email}}">
                                <div id="collapse1" class="mt-2 collapse show" aria-labelledby="headingOne"
                                    data-parent="#accordion" style="">
                                    <!-- <div class="alert alert-warning"><i class="las la-credit-card"></i> We also
                                        accept <b>International Cards</b> for payments on transaction. </div> -->
                                    <img src="{{ asset('/public/user/images/payment-cards.png') }}" alt="cards"
                                        class="img-fluid">
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Card Number</label>
                                                <input type="text" class="form-control" placeholder="Enter Number"
                                                    name="">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Name on Card</label>
                                                <input type="text" class="form-control" placeholder="Enter Name"
                                                    name="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Expiry Date</label>
                                                <input type="text" maxlength="4" class="form-control"
                                                    placeholder="MM/YY" name="">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>CVV</label>
                                                <input type="password" maxkength="4" class="form-control" placeholder=""
                                                    name="">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <!-- <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Advance Payment</label>
                                                <input type="radio" name="payment" id="payment_adv" checked
                                                    class="form-check"
                                                    value="{{($tot_amt * $advance_payment)/100}}">{{ ($tot_amt * $advance_payment)/100}}
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Full Payment</label>
                                                <input type="radio" name="payment" id="payment_full"
                                                    value="{{$tot_amt}}" class="form-check">{{$tot_amt}}
                                            </div>
                                        </div> -->
                                        <input type="text" hidden name="payment" id="payment_full" value="{{$tot_amt}}">
                                    </div>
                                    <button type="submit" class="btn btn-primary" id="PayBtn">Pay ₹
                                    {{$tot_amt}}</button>
                                </div>
                            </form>
                        </div>

                        <!-- <div class="card-body border rounded set mb-3">
                            <div class="card-header bg-primary-light font-weight-500 h6 border-0" id="headingTwo">
                                <a href="javascript:void(0)" class="collapsed" data-toggle="collapse"
                                    data-target="#collapse2" aria-expanded="false" aria-controls="collapseTwo">
                                    Net Banking
                                </a>
                            </div>
                            <div id="collapse2" class="mt-2 collapse" aria-labelledby="headingTwo"
                                data-parent="#accordion" style="">
                                <div class="row">
                                    <div class="col-md-6 col-6">
                                        <div class="card card-body">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" class="custom-control-input" id="SBI" name="bank"
                                                    value="">
                                                <span class="custom-control-label mr-2" for="SBI">SBI</span>
                                                <img src="{{ asset('/public/user/images/sbi.png') }}"
                                                    alt="SBI">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-6">
                                        <div class="card card-body">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" class="custom-control-input" id="ICICI" name="bank"
                                                    value="">
                                                <span class="custom-control-label mr-2" for="ICICI">ICICI</span>
                                                <img src="{{ asset('/public/user/images/icici.png') }}"
                                                    alt="ICICI">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-6">
                                        <div class="card card-body">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" class="custom-control-input" id="HDFC" name="bank"
                                                    value="">
                                                <span class="custom-control-label mr-2" for="HDFC">HDFC</span>
                                                <img src="{{ asset('/public/user/images/HDFC.png') }}"
                                                    alt="HDFC">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-6">
                                        <div class="card card-body">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" class="custom-control-input" id="AXIS" name="bank"
                                                    value="">
                                                <span class="custom-control-label mr-2" for="AXIS">AXIS</span>
                                                <img src="{{ asset('/public/user/images/AXIS.png') }}"
                                                    alt="AXIS">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="customCheck"
                                        name="example1">
                                    <label class="custom-control-label" for="customCheck">By clicking on Pay , I
                                        agree to accept the <a href="#">Booking Terms</a> &amp; Cloud Travels
                                        General <a href="#">Terms of use and services</a></label>
                                </div>
                                <a href="#" class="btn btn-primary">Pay <i class="las la-pound-sign"></i>88.00</a>
                            </div>
                        </div>

                        <div class="card-body border rounded set mb-3">
                            <div class="card-header bg-primary-light font-weight-500 h6" id="headingThree">
                                <a href="javascript:void(0)" class="collapsed" data-toggle="collapse"
                                    data-target="#collapse3" aria-expanded="false" aria-controls="collapseThree">
                                    Paypal
                                </a>
                            </div>
                            <div id="collapse3" class="collapse" aria-labelledby="headingThree" data-parent="#accordion"
                                style="">
                                <div class="card-body">
                                    <div class="custom-control custom-radio align-items-center d-flex">
                                        <input type="radio" class="custom-control-input" id="paypal" name="bank"
                                            value="">
                                        <span class="custom-control-label mr-2" for="paypal">Paypal</span>
                                        <img src="{{ asset('/public/user/images/paypal.png') }}"
                                            alt="paypal" class="ml-auto" style="width:150px;">
                                    </div>
                                    <a href="#" class="btn btn-primary">Pay <i class="las la-pound-sign"></i>88.00</a>
                                </div>
                            </div>
                        </div> -->
                    </div>

                </div>


            </div>
        </div>

        <div class="col-sm-4 float-left rightBookingPag">
            <div class="card">
                <h4 class="title">Fare Summary</h4>
                <div class="checkMain">
                    <div class="">Days : <?php 
                    $searched->hallbookingdate=json_decode($searched->hallbookingdate);
                    for ($i=0; $i < count($searched->hallbookingdate); $i++) { 
                        echo $searched->hallbookingdate[$i];
                        if ($i != (count($searched->hallbookingdate)-1)) {
                            echo ", ";
                        }
                    }
                    ?></div>
                    <!-- <div class="checkIn">Check In<br>{{$searched->checkInDate}}</div>
                    <div class="checkOut">Check Out<br>{{$searched->checkOutDate}}</div> -->
                </div>
                <div class="descrip">
                    <div class="descripLeft">
                        <!-- <p>0 Adults</p> -->
                        <p>in 1 Hall for {{$interval}} Days </p>
                    </div>
                    <!--<div class="descripRight"> <p class="bigTxtBold_16">₹ 833.27</p></div>-->
                </div>

                <div class="descrip2">

                    <p>Per Hall Charges X Per Night<span class="floatRight">₹ {{$normal_rate}} </span></p>
                    <p>Total Hall Charges <span class="floatRight">₹ {{$total_room_charage}} </span></p>
                    <p>CGST on Hall Charges ({{$cgst_rate_percent}}%)<span class="floatRight"> ₹ {{$cgst_rate}}</span>
                    </p>
                    <p>SGST on Hall Charges ({{$sgst_rate_percent}}%)<span class="floatRight"> ₹ {{$sgst_rate}}</span>
                    </p>
                </div>

                <div class="total">
                    <span class="title">Total </span>
                    <span class="value"> ₹ {{$tot_amt}} </span>
                </div>

                <!-- <div class="bookNowBtn"><button type="button">Book Now</button></div> -->

            </div>
        </div>



    </div>
</div>



@endsection

@section('script')


<script>
$(document).ready(function() {

    $('#credit_or_debit [name=payment]').on('change', function() {
        // alert($('input[name=payment]:checked', '#credit_or_debit').val());
        var amt = $('input[name=payment]:checked', '#credit_or_debit').val();
        tot_deatils = "Pay ₹ " + amt;
        $("#PayBtn").empty()
        $("#PayBtn").append(tot_deatils)
    });
});
</script>
@endsection
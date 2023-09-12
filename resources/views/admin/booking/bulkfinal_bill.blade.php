@extends('admin.common.master')
@section('content')

<div class="content-wrapper">
    <div class="card">
        <div class="card-body">
            <!-- <div class="card-body d-flex align-items-center justify-content-between">
                <h4 class="mt-1 mb-1">Bill Details for Booking Id #{{$booking_id}}</h4>
            </div> -->
            @if(count($datas)>0)
            <div id="sectionDiv">
                <div class="row invoice-info">
                    <div class="col-sm-12 invoice-col">
                        <address class="text-center">PH: 033-2356-5522(Principal) / 2356-6522 (EPBX)</address>
                        <address class="text-center">FAX: 033-2356-3633, Email : icmard.kol@gmail.com</address>
                        <address class="text-center">GSTIN: 19AAAJT0468K1Z0, PAN : AAAJT0468K</address>
                    </div>
                </div>
                <div class="row invoice-info">
                    <div class="col-sm-6 invoice-col">
                        Memo No: 304/ICMARD/355
                    </div>
                    <div class="col-sm-6 invoice-col text-center">
                        Date : {{date('d-m-Y')}}
                    </div>
                </div>
                <br />
                <div class="row invoice-info">
                    <div class="col-sm-4 invoice-col">
                        To
                        <address>
                            795 Folsom Ave, Suite 600<br>
                            San Francisco, CA 94107<br>
                            Phone: (555) 539-1037<br>
                            Email: john.doe@example.com
                        </address>
                    </div>
                </div>
                <div class="row invoice-info">
                    <div class="col-sm-8 invoice-col text-center">
                        Ref : Your letter Under Booking Id : {{$booking_id}}
                    </div>
                    <div class="col-sm-4 invoice-col">
                        Date : {{date('d-m-Y')}}
                    </div>
                </div>
                <div class="row invoice-info">
                    <div class="col-sm-12 invoice-col">
                        Dear Sir,
                        <address>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            We are sending here with a bill for the hall, Food provided to your room hold on {{$room_book->from_date}} 
                            to  {{$room_book->to_date}} at this institute. Details of the bill are given below :-
                        </address>
                    </div>
                    <?php $hall_total_amount=0;$hall_cal_total_amount=0;?>
                </div>
                <!-- <section class="content">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"> A) Service Charges :</h3>
                        </div>
                        <div class="card-body p-0 ">
                            <table class="table projects">
                                <thead>
                                    <tr>
                                        <th class="text-center">Date</th>
                                        <th class="text-center">To</th>
                                        <th class="text-center">Particulars</th>
                                        <th class="text-center">Rate per day</th>
                                        <th class="text-center">No of days</th>
                                        <th class="text-center">Amount</th>
                                        <th class="text-center">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </section> -->
                <br />
                <section class="content">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">A) Accommodation Charges :</h3>
                        </div>
                        <div class="card-body p-0">
                        <table class="table">
                                <thead>
                                     <tr>
                                        <th class="text-center">ROOM/HALL TYPE</th>
                                        <th class="text-center">Number</th>
                                        <th class="text-center">No of Days</th>
                                        <th class="text-center">Taxable</th>
                                        <th class="text-center">CGST</th>
                                        <th class="text-center">SGST</th>
                                        <th class="text-center">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php $total_amount=0;$cal_total_amount=0;$tot_taxable=0;?>
                                    <?php    $taxable =  0 ;$cgst =0; $sgst = 0;   $tot_cgst = 0 ; $cgst_rate = 0;?>
                                    @foreach($datas as $data)
                                    <?php 
                                  $interval = \Carbon\Carbon::parse($room_book->from_date)->diff(\Carbon\Carbon::parse($room_book->to_date))->days;
                                    // $interval = 2;
                                  foreach($acco_rent as $rent){
                                       if($data->room_type_id == $rent->room_type_id ){
                                        $taxable = $rent->normal_rate;
                                        $cgst_rate = $rent->cgst_rate;
                                       }
                                  }
                                  foreach($room_cnt as $cnt){
                                       if($data->room_type_id == $cnt->room_type_id ){
                                        $numroom = $cnt->numroom;
                                       }
                                  }
                                 
                                    $cgst=($taxable*$cgst_rate)/100;
                                    $sgst=($taxable*$cgst_rate)/100;
                                    $tot_cgst +=(($taxable*$cgst_rate)/100)*$data->noofroom;
                                
                                    ?>
                                    <tr class="text-center">
                                        <td>{{$data->room_name}}</td>
                                        <td>{{$numroom}}</td>
                                        <td>{{$interval}}</td>
                                        <td>{{$taxable}}</td>
                                        <td>{{$cgst_rate}}</td>
                                        <td>{{$cgst_rate}}</td>
                                        
                                        <td>{{round(($taxable+$cgst+$sgst)*$data->noofroom)}}</td>
                                        <?php $total_amount +=round(($taxable+$cgst+$sgst)*$data->noofroom);
                                        $tot_taxable +=round($taxable*$data->noofroom); ?>
                                    </tr>
                                   <?php    $taxable =  0 ;$cgst =0; $sgst = 0; ?>
                                    @endforeach
                                    <tr>
                                        
                                        <td></td>
                                        <td></td>
                                        <td><input type="text" id="amount" required="" class="form-control" value="{{$tot_taxable}}" readonly=""></td>
                                        <td><input type="text" id="cgst_rate" name="cgst_rate" required="" class="form-control" value="{{$tot_cgst}}" readonly="">
                                        <input type="hidden" id="crate" name="crate" class="form-control" value="5" readonly="">
                                        </td>
                                        <td><input type="text" id="cgst_rate" name="cgst_rate" required="" class="form-control" value="{{$tot_cgst}}" readonly=""></td>
                                        <td><input type="text" id="net_amount" name="net_amount" required="" class="form-control" value="{{$total_amount}}" readonly=""></td>
                                    </tr> 
                                    <tr>
                                        
                                        <td></td>
                                        <td>Discount Rate (%)</td>
                                        <td><input type="text" id="" name="discount" required="" class="form-control" value="{{$room_book->discount_amount}}" readonly=""></td>
                                        <td>Final Amount</td>
                                        <td><input type="text" id="" name="discount" required="" class="form-control" value="{{$room_book->total_amount}}" readonly=""></td>
                                    </tr>   
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
                <br />
                <section class="content">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">B) Food Charges :</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table projects">
                                <thead>
                                    <tr>
                                        <th class="text-center">Date</th>
                                        <th class="text-center">Item</th>
                                        <th class="text-center"></th>
                                        <th class="text-center"></th>
                                        <th class="text-center"></th>
                                        <th class="text-center">No of Head</th>
                                        <th class="text-center">Rate</th>
                                        <th class="text-center">Amount</th>
                                        <th class="text-center">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $food_total_amount=0;$food_cal_total_amount=0;
                                    $i=1;
                                    foreach ($room_menu as $key => $menu) {
                                        $food_total_amount +=$menu->amount;
                                    ?>
                                    <tr>
                                        <td>{{$room_book->from_date}}</td>
                                        <td>{{$menu->menu_id}}</td>
                                        <td> </td>
                                        <td> </td>
                                        <td> </td>
                                        <td>{{$menu->no_of_head}}</td>
                                        <td>{{$menu->rate}}</td>
                                        <td>{{$menu->amount}}</td>
                                        <td>
                                            @if(count($room_menu)==$i)
                                            {{$food_total_amount}}
                                            @endif
                                        </td>
                                    </tr>
                                    <?php  $i++; } 
                                    $food_cgst =($food_total_amount*2.5)/100;
                                    $food_sgst =($food_total_amount*2.5)/100;
                                    $food_cal_total_amount=$food_cgst +$food_sgst + $food_total_amount;
                                    ?>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>CGST</td>
                                        <td>(2.5 %)</td>
                                        <td>{{$food_cgst}}</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>SGST</td>
                                        <td>(2.5%)</td>
                                        <td>{{$food_sgst}}</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>Total</td>
                                        <td></td>
                                        <td>{{$food_cal_total_amount}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
                <br />
                <section class="content">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">C) Service Charges :</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table projects">
                                <thead>
                                    <tr>
                                        <th class="text-center">From</th>
                                        <th class="text-center">To</th>
                                        <th class="text-center">Item</th>
                                        <th class="text-center"></th>
                                        <th class="text-center"></th>
                                        <th class="text-center">Pieces</th>
                                        <th class="text-center">Days</th>
                                        <th class="text-center">Rate</th>
                                        <th class="text-center">Amount</th>
                                        <th class="text-center">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $projecter_total_amount=0;$projecter_cal_total_amount=0;?>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
                <div class="row">
                    <div class="col-6">
                        <!-- <p class="lead">Payment Methods:</p>
                        <img src="../../dist/img/credit/visa.png" alt="Visa">
                        <img src="../../dist/img/credit/mastercard.png" alt="Mastercard">
                        <img src="../../dist/img/credit/american-express.png" alt="American Express">
                        <img src="../../dist/img/credit/paypal2.png" alt="Paypal">

                        <p class="text-muted well well-sm shadow-none" style="margin-top: 10px;">
                            Etsy doostang zoodles disqus groupon greplin oooj voxy zoodles, weebly ning heekya handango
                            imeem
                            plugg
                            dopplr jibjab, movity jajah plickers sifteo edmodo ifttt zimbra.
                        </p> -->
                    </div>
                    <div class="col-6">
                        <!-- <p class="lead"></p> -->

                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th>A+B+C Total Bill Payable Amt:</th>
                                        <td><?php echo $total_bill_pay_amt=($room_book->total_amount) +$cal_total_amount+$food_cal_total_amount+$projecter_cal_total_amount; ?>
                                        </td>
                                    </tr>
                                    <!-- <tr>
                                        <th></th>
                                        <td></td>
                                    </tr> -->
                                    <tr>
                                        <th>Paid Amount</th>
                                        <td><?php
                                        $advance_amt=0;
                                        echo $room_book->total_amount;
                                        ?></td>
                                    </tr>
                                    <tr>
                                        <th>Net Payment</th>
                                        <td><?php echo $net_payment=$total_bill_pay_amt - $room_book->total_amount; ?></td>
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row invoice-info">
                    <div class="col-sm-12 invoice-col">
                        <address>
                            Please pay the net amount of
                            <strong>Rs. {{$net_payment}} [Rupees:
                                {{ ucfirst(app('App\Http\Controllers\UtilityController')->convert_number_to_words($net_payment))}}]
                            </strong>
                            only by an account payee cheque in favour of 'ICMARD' or through RTGS/NEFT payment on Punjab
                            National, VIP Road (Ultadanga) Branch, Kolkata to Account No:0 8200 5000 2951 and IFSC Code:
                            PUNB0082020 at your earliest.
                        </address>
                    </div>
                </div>
            </div>
            <div class="row no-print">
                <div class="col-12">
                    <!-- <a href="invoice-print.html" rel="noopener" target="_blank" class="btn btn-default"><i
                                class="fas fa-print"></i> Print</a> -->

                    <button type="button" class="btn btn-primary float-right" style="margin-right: 5px;"
                        onclick="printContent('sectionDiv');">
                        <i class="mdi mdi-printer"></i> Generate PDF
                    </button>
                </div>
            </div>

            @else
            <div class="card-body d-flex align-items-center justify-content-between">
                <h4 class="mt-1 mb-1">This Booking Id #{{$booking_id}} not found!</h4>
            </div>
            @endif
        </div>
    </div>
</div>


@endsection

@section('script')

<script>
function printContent(divName) {
    var printContents = document.getElementById(divName).innerHTML;
    var originalContents = document.body.innerHTML;

    document.body.innerHTML = printContents;

    window.print();

    document.body.innerHTML = originalContents;
}
</script>
@endsection
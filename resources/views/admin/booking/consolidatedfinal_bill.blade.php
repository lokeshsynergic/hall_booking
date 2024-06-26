@extends('admin.common.master')
@section('content')

<div class="content-wrapper">
    <div class="card">
        <div class="card-body">
            <!-- <div class="card-body d-flex align-items-center justify-content-between">
               
            </div> -->
            @if(count($total_booking)>0 || count($total_bookingh)>0)
            <div id="sectionDiv">
                <div class="row invoice-info">
                    <div class="col-sm-12 invoice-col">
                    <address class="text-center" style="margin-bottom: 5px">The institute of Cooperative Management for Agriculture & Rular Developement</address>
                    <address class="text-center" style="margin-bottom: 5px">(ICMARD Training Institute of the WBSCARDB BAnk Ltd)</address>
                      <address class="text-center" style="margin-bottom: 5px">Block No. 14/2 CTI Scheme VIII(M). Ultadanga , Kolkata - 700067</address>
                        <address class="text-center" style="margin-bottom: 5px">PH: 033-2356-5522(Principal) / 2356-6522 (EPBX)</address>
                        <address class="text-center" style="margin-bottom: 5px">FAX: 033-2356-3633, Email : icmard.kol@gmail.com</address>
                        <address class="text-center" style="margin-bottom: 5px">GSTIN: 19AAAJT0468K1Z0, PAN : AAAJT0468K</address>
                    </div>
                </div>
                <div class="row invoice-info">
                    <div class="col-sm-6 invoice-col">
                        Memo No dsadas: {{$memo_no}}
                    </div>
                    <div class="col-sm-6 invoice-col text-center">
                        Date : {{date('d-m-Y')}}
                    </div>
                </div>
                <br />
                <div class="row invoice-info">
                    <div class="col-sm-4 invoice-col">
                        To
                        <?php  $address_bar[0] ?>
                        <address>
                           {{$address_bar[0]->add_line1}}</br>
                           {{$address_bar[0]->add_line2}}</br>
                           {{$address_bar[0]->add_line3}}</br>
                           {{$address_bar[0]->add_line4}}
                          
                        </address>
                    </div>
                </div>
                <div class="row invoice-info">
                    <div class="col-sm-8 invoice-col text-center">
                        Ref : Your letter Under Booking Id : {{$memo_no}}
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
                            We are sending here with a bill for the Hall , Guest room and Food provided for your meeting held on  at this institute. Details of the bill are given below :-
                        </address>
                    </div>
                   
                </div>
                <?php $total_amounth=0; $cal_total_amount=0;$tot_taxable=0;?>
                <section class="content">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">A) Hall Rent:</h3>
                        </div>
                        <div class="card-body p-0">
                        <table class="table">
                                <thead>
                                     <tr>
                                       <th class="text-center">From date</th>
                                       <th class="text-center">To date</th>
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
                              
                                    <?php    $taxable =  0 ;$cgst =0; $sgst = 0;   $tot_cgst = 0 ; $cgst_rate = 0; $total_amounth= 0;?>
                                    <?php    $gross_taxable = 0;
                               foreach($total_bookingh as $booking) {
                                   $bkng = $booking->booking_id;
                                   $room_book=DB::select("Select * from td_room_book where booking_id = '$bkng' " );
                                   $room_cnt = DB::select("SELECT COUNT(*) as numroom,room_type_id  FROM td_room_lock where booking_id = '$bkng' group by room_type_id,date");
                                          $datas = DB::select("SELECT d.room_name,d.room_type_id,COUNT(*) as noofroom FROM td_room_lock b
                              join md_room d ON d.room_type_id = b.room_type_id where b.booking_id = '$bkng'
                                          and d.id = b.room_id group by d.room_type_id,d.room_name");
                                   if($room_book){
                                   $room_book = $room_book[0];
                                    ?>

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
                                        <td>{{date('d-m-Y',strtotime($room_book->from_date))}}</td>
                                        <td>{{date('d-m-Y',strtotime($room_book->to_date))}} </td>
                                        <td>{{$data->room_name}}</td>
                                        <td>{{$numroom}}</td>
                                        <td>{{$interval}}</td>
                                        <td>{{$taxable}}</td>
                                        <td>{{$cgst_rate}}</td>
                                        <td>{{$cgst_rate}}</td>
                                        <td>{{round(($taxable+$cgst+$sgst)*$data->noofroom)}}</td>
                                        <?php $total_amounth +=round(($taxable+$cgst+$sgst)*$data->noofroom);
                                        $tot_taxable +=round($taxable*$data->noofroom); ?>
                                    </tr>
                                   <?php    $taxable =  0 ;$cgst =0; $sgst = 0; ?>
                                    @endforeach

                                <?php } } 
                                 if($total_amounth > 0)   {
                                ?> 
                               
                                    <tr>
                                        <td colspan="3"></td>
                                        <td></td>
                                        <td></td>
                                        <td><input type="text" id="amount" required="" class="form-control" value="{{$tot_taxable}}" readonly=""></td>
                                        <td><input type="text" id="cgst_rate" name="cgst_rate" required="" class="form-control" value="{{$tot_cgst}}" readonly="">
                                        <input type="hidden" id="crate" name="crate" class="form-control" value="5" readonly="">
                                        </td>
                                        <td><input type="text" id="cgst_rate" name="cgst_rate" required="" class="form-control" value="{{$tot_cgst}}" readonly=""></td>
                                        <td><input type="text" id="net_amount" name="net_amount" required="" class="form-control" value="{{$total_amounth}}" readonly=""></td>
                                    </tr> 
                                <?php } ?>   
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
                <br />
                <section class="content">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">B) Guest Room Charges:</h3>
                        </div>
                        <div class="card-body p-0">
                        <table class="table">
                                <thead>
                                     <tr>
                                       <th class="text-center">From date</th>
                                       <th class="text-center">To date</th>
                                        <th class="text-center">HALL TYPE</th>
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
                                    <?php    $gross_taxable = 0;
                               foreach($total_booking as $booking) {
                                   $bkng = $booking->booking_id;
                                 $room_book=DB::select("Select * from td_room_book where booking_id = '$bkng' " );
                                 $room_cnt = DB::select("SELECT COUNT(*) as numroom,room_type_id  FROM td_room_lock where booking_id = '$bkng' group by room_type_id,date");
                                        $datas = DB::select("SELECT d.room_name,d.room_type_id,COUNT(*) as noofroom FROM td_room_lock b
                            join md_room d ON d.room_type_id = b.room_type_id where b.booking_id = '$bkng'
                                        and d.id = b.room_id group by d.room_type_id,d.room_name");
                                
                                   $room_book = $room_book[0];
                                    ?>

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
                                        <td>{{date('d-m-Y',strtotime($room_book->from_date))}}</td>
                                        <td>{{date('d-m-Y',strtotime($room_book->to_date))}} </td>
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

                                <?php } ?>    
                                    <tr>
                                        <td colspan="3"></td>
                                        <td></td>
                                        <td></td>
                                        <td><input type="text" id="amount" required="" class="form-control" value="{{$tot_taxable}}" readonly=""></td>
                                        <td><input type="text" id="cgst_rate" name="cgst_rate" required="" class="form-control" value="{{$tot_cgst}}" readonly="">
                                        <input type="hidden" id="crate" name="crate" class="form-control" value="5" readonly="">
                                        </td>
                                        <td><input type="text" id="cgst_rate" name="cgst_rate" required="" class="form-control" value="{{$tot_cgst}}" readonly=""></td>
                                        <td><input type="text" id="net_amount" name="net_amount" required="" class="form-control" value="{{$total_amount}}" readonly=""></td>
                                    </tr> 
                                    <!-- <tr>
                                        <td></td>
                                        <td>Discount Rate (%)</td>
                                        <td><input type="text" id="" name="discount" required="" class="form-control" value="{{$room_book->discount_amount}}" readonly=""></td>
                                        <td>Final Amount</td>
                                        <td><input type="text" id="" name="discount" required="" class="form-control" value="{{$room_book->total_amount}}" readonly=""></td>
                                    </tr>    -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
                <br />
                <section class="content">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">C) Food Charges :</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table projects">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width:6%">Date</th>
                                        <th class="text-center" style="width:54%">Item</th>
                                        <th class="text-center" style="width:10%" >No of Head</th>
                                        <th class="text-center" style="width:10%">Rate</th>
                                        <th class="text-center" style="width:10%">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php   $total_food_charge = 0;
                                $room_menu=DB::select("SELECT * from td_room_menu,td_consolidated_bills
                                where td_room_menu.booking_id = td_consolidated_bills.booking_id
                                AND td_consolidated_bills.memo_no = '$memo_no' ");
                             
                                    $food_total_amount=0;$food_cal_total_amount=0; 
                                    $i=1;
                                    foreach ($room_menu as $key => $menu) {
                                        $food_total_amount +=$menu->amount;
                                    ?>
                                    <tr class="text-center">
                                        <td style="width:6%">{{date('d-m-Y',strtotime($menu->tr_dt))}}</td>
                                        <?php $menuname = '';
                                        foreach($menus as $me){
                                       if($menu->menu_id == $me->id ){
                                        $menuname = $me->item_name;
                                       }
                                         }  ?>
                                        <td style="width:54%; word-wrap: break-word; text-wrap: wrap;">{{$menuname}}</td>
                                        <td style="width:10%">{{$menu->no_of_head}}</td>
                                        <td style="width:10%">{{$menu->rate}}</td>
                                        <td style="width:10%">{{$menu->amount}}</td>
                                        <!-- <td style="width:10%">
                                            @if(count($room_menu)==$i)
                                            {{$food_total_amount}}
                                            @endif
                                        </td> -->
                                    </tr>
                                    <?php  $i++; } 
                                    $food_cgst =($food_total_amount*2.5)/100;
                                    $food_sgst =($food_total_amount*2.5)/100;
                                    $food_cal_total_amount=$food_cgst +$food_sgst + $food_total_amount+$total_amounth;
                                    ?>
                                    <tr class="text-center">
                                        <td colspan="2"></td><td>Total Taxable</td><td></td><td> {{$food_total_amount}}</td>
                                    </tr>
                                    <tr class="text-center">
                                        <td></td>
                                        <td></td>
                                        <td>CGST</td>
                                        <td>(2.5 %)</td>
                                        <td>{{round($food_cgst)}}</td>
                                    </tr>
                                    <tr class="text-center">
                                        <td></td>
                                        <td></td>
                                        <td>SGST</td>
                                        <td>(2.5%)</td>
                                        <td>{{round($food_sgst)}}</td>
                                    </tr>
                                    <tr class="text-center">
                                        <td></td>
                                        <td></td>
                                        <td>Total</td>
                                        <td></td>
                                      
                                        <td>{{round($food_total_amount+round($food_sgst)+round($food_sgst))}}</td>
                                        <?php  $food_total_amount = round($food_total_amount+round($food_sgst)+round($food_sgst)); ?>
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
                            <h3 class="card-title"> Service Charges :</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table projects">
                                <thead>
                                    <tr>
                                        <!-- <th class="text-center">From</th>
                                        <th class="text-center">To</th> -->
                                        <th class="text-center">Item</th>
                                        <th class="text-center">Days</th>
                                        <th class="text-center">Rate</th>
                                        <th class="text-center">Amount</th>
                                      
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                     $miss_menu=DB::select("SELECT * from td_miscellaneous_item,td_consolidated_bills
                                     where td_miscellaneous_item.booking_id = td_consolidated_bills.booking_id
                                     AND td_consolidated_bills.memo_no = '$memo_no' ");
                                    
                                    $projecter_total_amount=0;$projecter_cal_total_amount=0;
                                    $totla_c = 0;
                                    foreach ($miss_menu as $key => $miss) {
                                    
                                    ?>
                                    <tr class="text-center">
                                        <td>{{$miss->item_name}}</td>
                                        <td>{{$miss->num_of_days}}</td>
                                        <td>{{$miss->rate}}</td>
                                        <td>{{$miss->amount}}</td>
                                    </tr>
                                    <?php   $totla_c += $miss->amount;
                                
                                
                                } ?>
                                  <tr>
                                    <td cols="3">Miscellaneous Total</td>
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
                                        <td><?php echo round($total_bill_pay_amt=$total_amounth +$total_amount + $food_total_amount + $totla_c); ?>
                                        </td>
                                    </tr>
                                    <!-- <tr>
                                        <th></th>
                                        <td></td>
                                    </tr> -->
                                    <!-- <tr>
                                        <th>Paid Amount</th>
                                        <td><?php
                                        $advance_amt=0;
                                     //   echo $room_book->total_amount;
                                        ?></td>
                                    </tr> -->
                                    <tr>
                                        <th>Net Payment</th>
                                        <td><?php echo $net_payment=$total_bill_pay_amt ; ?></td>
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
                <h4 class="mt-1 mb-1">This Booking Id #{{$memo_no}} not found!</h4>
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
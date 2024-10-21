@extends('userdashboard.common.master')
@section('content')

<div class="content-wrapper">
    <div class="card">
        <div class="card-body">
            <h2 class="card-title card_title_cus">
                @if($STA == 'A')
                Booked List
                @else
                For cancellation of Booking Please Contact to ICMARD office.Mobile : 6292311219
                @endif
            </h2>
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <table id="order-listing" class="table">
                            <thead>
                                <tr>
                                    <th> #</th>
                                    <th>Booking Id</th>
                                    <th>Name</th>
                                    <th>No of Guest</th>
                                    <th>Booking Time</th>
                                    <th>Booking from/To</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i=1;?>
                                @foreach($datas as $data)
                              <?php  if($data->from_date > date('Y-m-d')) {    ?>
                                <tr style="color:green;font-weight:bold">
                                <?php }else{   
                                   echo '<tr>';
                                } ?>      
                                    <td>{{$i++}}</td>
                                    <td>{{$data->booking_id}}</td>
                                    <?php if($data->customer_type_flag == 'I') { ?>
                                    <td style="width: 20%;text-wrap: balance;">
                                    {{$data->first_name}} {{$data->middle_name}} {{$data->last_name}} 
                                   </td>
                                    <?php }else {?>
                                    <td> {{$data->organisation_name}}</td>
                                    <?php } ?>
                                    <td>{{$data->no_adult}}</td>
                                    <td>{{date('d-m-Y H:i:s',strtotime($data->booking_time))}}</td>
                                    <td>{{date('d-m-Y',strtotime($data->from_date))}} / {{date('d-m-Y',strtotime($data->to_date))}}</td>
                                    <!-- <td></td> -->
                                    <td>
                                        <a href="{{route('receipt',['booking_id'=>$data->booking_id])}}"
                                            title="View Bill" class="edit_btn">
                                            <!-- <i class="" style="font-size: 18px;"> -->
                                            @if($data->booking_status =='C')
                 <span class="cancel">Canceled</span>
                                                 @else   
                 <span class="edit"> <i class="fa fa-pencil-square" aria-hidden="true"></i></span> 
                                                @endif
                                            <!-- </i> -->
                                        </a>
                                    </td>
                                </tr>
                              
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@section('script')


@endsection
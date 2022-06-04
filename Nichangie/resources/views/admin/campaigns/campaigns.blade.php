@extends('layouts.app_admin')

@section('page-styles')
<link rel="stylesheet" href="{{ asset('admin/assets/plugins/datatables/datatables.min.css')}}">
<link rel="stylesheet" href="{{ asset('admin/assets/plugins/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.0.3/css/buttons.dataTables.min.css">
@endsection

@section('content')
@include('layouts.admin_header')
<div class="content-wrapper">
    <!-- Container-fluid starts -->
    <div class="container-fluid">

        <!-- Header Starts -->
        <div class="row">
            <div class="col-sm-12 p-0">
                <div class="main-header">
                    <h4>{{ __('Campaigns')}}</h4>
                </div>
            </div>
        </div>
        <!-- Header end -->
        <div class="row">
            <div class="col-sm-12">
                <!-- Basic Table starts -->
                <div class="card">
                    <div class="card-block">
                    @include('flash-message')
                        <div class="row">
                            <div class="col-sm-12 table-responsive">
                                <table class="table" id="table-1">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('Title')}}</th>
                                            <th>{{ __('Story')}}</th>
                                            <th>{{ __('Raised')}}</th>
                                            <th>{{ __('Goal')}}</th>
                                            <th>{{ __('Deadline')}}</th>
                                            <th>{{ __('Type')}}</th>
                                            <th>{{ __('Status')}}</th>
                                            <th>{{ __('Action')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($campaigns as $key=>$rows)
                                        <tr>
                                            <td>{{ $key+1 }}</td>
                                            <td><a href="{{ route('campaign.show', $rows->id) }}">{{ $rows->title }}</a></td>
                                            <td>{{ substr($rows->description,0,10) }}...</td>
                                            <td>{{ ($rows->donationstatus == 1) ? number_format($rows->amount) : number_format(0) }}</td>
                                            <td>{{ number_format($rows->fundgoals) }}</td>
                                            <td>{{ $rows->deadline }}</td>
                                            <td>
                                                @if($rows->type == 1)
                                                <div class="label-main"><label class="label label-lg bg-warning">Featured</label></div>
                                                @else
                                                @if($rows->status != "Closed")
                                                <button type="button" data-id="{{ $rows->id }}" id="btn-upgrade" class="btn btn-primary waves-effect btn-upgrade">Upgrade</a>
                                                @else
                                                <button type="button" class="btn btn-primary waves-effect btn-upgrade" disabled>Upgrade</a>
                                                @endif
                                                @endif
                                            </td>
                                            <td>
                                                @if($rows->status == "In progress")
                                                <div class="label-main"><label class="label label-lg bg-default">In progress</label></div>
                                                @elseif($rows->status == "Finished")
                                                <div class="label-main"><label class="label label-lg bg-success">Finished</label></div>
                                                @else
                                                <div class="label-main"><label class="label label-lg bg-danger">Closed</label></div>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('campaign.close')}}" class="btn btn-danger waves-effect" data-toggle="tooltip" data-placement="top" onclick="event.preventDefault(); document.getElementById('close-form{{$rows->id}}').submit();" title="Close Campaign"><i class="ti-close"></i></a>
                                                <a href="{{ route('transaction.withdraw', Auth::user()->id)}}" class="btn btn-success waves-effect" data-toggle="tooltip" data-placement="top" onclick="event.preventDefault(); document.getElementById('withdraw-form{{$rows->id}}').submit();" title="Request Withdraw"><i class="ti-wallet"></i></a>
                                                <a href="{{ route('campaign.export', $rows->id)}}" class="btn btn-info waves-effect" data-toggle="tooltip" data-placement="top" title="Export Data"><i class="ti-download"></i></a>
                                                <form id="withdraw-form{{$rows->id}}" action="{{ route('transaction.withdraw', Auth::user()->id)}}" method="POST" style="display: none;">
                                                    @csrf
                                                    <input type="text" value="{{$rows->id}}" name="id">
                                                </form>
                                                <form id="close-form{{$rows->id}}" action="{{ route('campaign.close')}}" method="POST" style="display: none;">
                                                    @csrf
                                                    <input type="hidden" value="{{$rows->id}}" name="campaign_id">
                                                </form>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Basic Table ends -->
                <div class="modal fade modal-flex" id="make-featured-Modal" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-md" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h5 class="modal-title">{{ __('Make this campaign featured')}}</h5>
                            </div>
                            <!-- end of modal-header -->
                            <div class="modal-body">
                                <h6>{{ __('By making this campaign featured, 1% of the platform fee will be added.')}}</h6>
                                <form action="{{ route('campaign.upgrade') }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="story_id" id="story_id">
                                    <button type="submit" class="btn btn-success btn-md waves-effect">Confirm</button>
                                </form>
                            </div>
                            <!-- end of modal-body -->
                        </div>
                        <!-- end of modal-content -->
                    </div>
                    <!-- end of modal-dialog -->
                </div>
            </div>
        </div>
    </div>
</div>
@section('page-scripts')
<script src="{{ asset('admin/assets/plugins/datatables/datatables.min.js')}}"></script>
<script src="{{ asset('admin/assets/plugins/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js')}}"></script>
<script>
    $(document).ready(function(){
        $('.btn-upgrade').on('click', function(){
            var id = $(this).data('id');
            $('#story_id').val(id);
            $('#make-featured-Modal').modal('show');
        });
    });

    $("#table-1").dataTable({
        dom: 'Bfrtip',
        buttons: [{
                extend: "excelHtml5",
                text: 'Export to Excel',
                title: "ALL CAMPAIGNS",
                sheetName: "CAMPAIGNS",
                className: "btn btn-info mb-0",
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7]
                },
                customize: function(xlsx) {
                    var sheet = xlsx.xl.worksheets['sheet1.xml'];

                    // jQuery selector to add a border
                    $('row c[r*="2"]', sheet).attr('s', '22');
                }
            },
            {
                extend: "pdfHtml5",
                text: 'Download PDF',
                title: "ALL CAMPAIGNS",
                className: "btn btn-success mb-0",
            }
        ],
    });
</script>
@endsection
@endsection
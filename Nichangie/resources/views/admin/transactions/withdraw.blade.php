@extends('layouts.app_admin')

@section('page-styles')
<link rel="stylesheet" href="{{ asset('assets/js/intl-tel-input/css/intlTelInput.css')}}">
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
                    <h4>{{ __('Withdraw ')}} {{ (!empty($campaign->title)) ? 'from '.$campaign->title : "" }}</h4>
                </div>
            </div>
        </div>
        <!-- Header end -->
        <div class="row m-b-30">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            @include('flash-message')
                            <div class="card-block">
                                <!-- <h6 class="section-title"><strong>Note:</strong> Multiple campaigns withdraw will not involve featured campaigns.</h6> -->
                                <form action="{{ route('transaction.request') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group">
                                        <label for="amount" class="form-control-label">{{ __('Amount to withdraw')}}</label>
                                        <input type="number" name="amount" class="form-control" id="amount" aria-describedby="emailHelp" placeholder="Enter amount">
                                        <input type="hidden" name="campaign_id" value="{{ (!empty($campaign->id)) ? $campaign->id : '' }}" class="form-control">
                                        <input type="hidden" name="user_id" value="{{ $user->id }}" class="form-control">
                                        <small id="emailHelp" class="form-text text-muted">{{ __('Maximum amount to withdraw: TZS ')}} {{ $balance ?? 0}}</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="deposit" class="form-control-label">{{ __('Amount to receive')}}</label>
                                        <input type="number" name="debit" class="form-control" id="deposit" aria-describedby="emailHelp" placeholder="Debit amount" readonly>
                                        <small id="emailHelp" class="form-text text-muted">Platform Fee: {{ (!empty($campaign->title)) ? $campaign->fee_percent : $campaign->fee_percent }}% of the amount to withdraw</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="payment_method" class="form-control-label">{{ __('Payment method')}}</label>
                                        <select class="form-control " id="payment_method" name="payment_method">
                                            <option value="mobile">{{ __('Mobile Number')}}</option>
                                            <option value="bank">{{ __('Bank Transfer')}}</option>
                                            <option value="asset">{{ __('Asset Purchase')}}</option>
                                        </select>
                                    </div>
                                    <div class="form-group" id="phoneInput">
                                        <label for="phone" class="form-control-label">{{ __('Phone number')}}</label>
                                        <input type="tel" id="phone" name="phone" class="form-control" value="{{ Auth::user()->phonenumber }}" placeholder="Enter mobile number" required>
                                    </div>
                                    <div class="row bank">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="accountname" class="form-control-label">{{ __('Account name')}}</label>
                                                <input type="text" class="form-control" name="account_name" id="accountname" placeholder="Enter account name">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="accountnumber" class="form-control-label">{{ __('Account number')}}</label>
                                                <input type="text" class="form-control" name="account_number" id="accountnumber" placeholder="Enter account number">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="bankname" class="form-control-label">{{ __('Bank name')}}</label>
                                                <input type="text" class="form-control" name="bank_name" id="bankname" placeholder="Enter bank name">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="branch" class="form-control-label">{{ __('Branch name')}}</label>
                                                <input type="text" class="form-control" name="branch_name" id="branch" placeholder="Enter bank's branch name">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row asset">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="file" class="form-control-label">{{ __('Attach invoice')}}</label>
                                                <input type="file" id="file" name="invoice" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="contacts" class="form-control-label">{{ __('Supplier Contacts')}}</label>
                                                <input type="tel" class="form-control" name="supplier_contact" id="contacts" placeholder="Enter mobile number">
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-success waves-effect waves-light m-r-30">{{ __('Submit')}}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@section('page-scripts')
<script src="{{ asset('assets/js/intl-tel-input/js/intlTelInput.min.js')}}"></script>
<script type="text/javascript">
    var utilUrl = "{{ asset('assets/js/intl-tel-input/js/utils.js?1638200991544')}}"
</script>
<script src="{{ asset('assets/js/auth-login.js')}}"></script>
<script type="text/javascript">
    var fee_percent = "{{ (!empty($campaign->title)) ? $campaign->fee_percent : $campaign->fee_percent }}";
</script>
<script>
    $(document).ready(function() {
        $('#payment_method').on('change', function() {
            var option = $(this).val();
            if (option == "bank") {
                $('.bank').css('display', 'block');
                $('#phoneInput').css('display', 'none');
                $('.asset').css('display', 'none');
                $('#accountname').attr('required', true);
                $('#accountnumber').attr('required', true);
                $('#bankname').attr('required', true);
                $('#branch').attr('required', true);
                $('#file').removeAttr('required', true);
                $('#contacts').removeAttr('required', true);
                $('#phone').removeAttr('required', true);
            } else if (option == "asset") {
                $('.asset').css('display', 'block');
                $('.bank').css('display', 'none');
                $('#file').attr('required', true);
                $('#contacts').attr('required', true);
                $('#phoneInput').css('display', 'none');
                $('#accountname').removeAttr('required', true);
                $('#accountnumber').removeAttr('required', true);
                $('#bankname').removeAttr('required', true);
                $('#branch').removeAttr('required', true);
                $('#phone').removeAttr('required', true);
            } else {
                $('#phoneInput').css('display', 'block');
                $('#phone').attr('required', true);
                $('.bank').css('display', 'none');
                $('.asset').css('display', 'none');
                $('#accountname').removeAttr('required', true);
                $('#accountnumber').removeAttr('required', true);
                $('#bankname').removeAttr('required', true);
                $('#branch').removeAttr('required', true);
                $('#file').removeAttr('required', true);
                $('#contacts').removeAttr('required', true);
            }
        });

        $('#amount').on('blur keyup', () => {
            var amount = $('#amount').val(),
                deposit = 0,
                fee = fee_percent/100;

            deposit = amount - (amount * fee);
            $('#deposit').val(deposit);
        });
    })
</script>
@endsection
@endsection
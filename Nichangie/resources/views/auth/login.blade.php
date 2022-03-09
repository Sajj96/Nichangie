@extends('layouts.app')

@section('page-styles')
<link href="{{ asset('assets/css/shop.css')}}" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/js/intl-tel-input/css/intlTelInput.css')}}">
@endsection

@section('content')
@include('layouts.header')
<!-- Page Title -->
<section class="page-title">
    <div class="auto-container">
        <div class="content-box">
            <h1>My Account</h1>
            <ul class="bread-crumb">
                <li><a class="home" href="index-2.html"><span class="fa fa-home"></span></a></li>
                <li>Login</li>
            </ul>
        </div>
    </div>
</section>
<section class="login-register-area">
    <div class="auto-container">
        <div class="row">
            <div class="col-md-12">
                <div class="exisitng-customer">
                    <h5>{{ __('Don\'t have account?')}}<a href="{{ route('register') }}">Register as a Donee </a> <a href="{{ route('register.doner') }}">Register as a Doner</a></h5>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-3"></div>
            <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 mt-2">
                @error('phone')
                <div class="alert alert-danger">
                    <strong>{{ $message }}</strong>
                </div>
                @enderror
                <div class="form">
                    <div class="shop-page-title">
                        <div class="title">Login </div>
                    </div>
                    <div class="row">
                        <form method="post" action="{{ route('login') }}">
                            @csrf
                            <div class="col-xl-12 mb-3">
                                <div class="field-label">Phone Number</div>
                                <div class="input-field">
                                    <input type="tel" id="phone" name="phone" placeholder="Enter phone number" autofocus>
                                    <div class="icon-holder">
                                        <i class="fa fa-phone" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-12">
                                <div class="field-label">Password</div>
                                <div class="input-field">
                                    <input type="password" name="password" placeholder="Enter password">
                                    <div class="icon-holder">
                                        <i class="fa fa-unlock-alt" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-12 register-btn">
                                <button class="theme-btn btn-style-one" type="submit"><span>Login</span></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-xl-3"></div>
        </div>
    </div>
</section>
@include('layouts.footer')
@section('page-scripts')
<script src="{{ asset('assets/js/jquery.bootstrap-touchspin.js')}}"></script>
<script src="{{ asset('assets/js/intl-tel-input/js/intlTelInput.min.js')}}"></script>
<script type="text/javascript">
    var utilUrl = "{{ asset('assets/js/intl-tel-input/js/utils.js?1638200991544')}}"
</script>
<script src="{{ asset('assets/js/auth-register.js')}}"></script>
@endsection
@endsection
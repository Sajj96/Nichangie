@extends('layouts.app')

@section('page-styles')
<link href="{{ asset('assets/css/shop.css')}}" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/js/intl-tel-input/css/intlTelInput.css')}}">
@endsection

@section('content')
@include('layouts.header')
<!-- Page Title -->
<section class="contact-form-section">
    <div class="auto-container">
        <div class="row">
            <div class="col-md-12">
                <div class="exisitng-customer">
                    <h5>{{ __('Don\'t have account?')}}<a href="{{ route('register') }}">{{ __('Register')}} </a></h5>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="default-form-area">
                    <div class="sec-title">
                        <h1>{{ __('Login')}}</h1>
                    </div>
                    @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                        <p>{{ $message }}</p>
                    </div>
                    @endif
                    @error('login')
                    <div class="alert alert-danger">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                    <form id="contact-form" name="contact_form" class="contact-form" action="{{ route('login') }}" method="post">
                        @csrf
                        <div class="row clearfix">
                            <div class="col-lg-3"></div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 column">
                                        <div class="form-group">
                                            <label for="phone">{{ __('Phone Number*')}}</label>
                                            <input type="tel" id="phone" name="login" class="form-control @error('phone') is-invalid @enderror" required>
                                            @error('phone')
                                            <span class="text-danger" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-12 col-md-12 column">
                                        <div class="form-group">
                                            <label for="password">{{ __('Password *')}}</label>
                                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Enter password" required>
                                            @error('password')
                                            <span class="text-danger" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-12 col-md-12 column">
                                        <div class="form-group flex-box">
                                            <div class="submit-btn">
                                                <button class="theme-btn btn-style-one" type="submit" data-loading-text="Please wait..."><span>{{ __('Login')}}</span></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3"></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@section('page-scripts')
<script src="{{ asset('assets/js/jquery.bootstrap-touchspin.js')}}"></script>
<script src="{{ asset('assets/js/intl-tel-input/js/intlTelInput.min.js')}}"></script>
<script type="text/javascript">
    var utilUrl = "{{ asset('assets/js/intl-tel-input/js/utils.js')}}"
</script>
<script src="{{ asset('assets/js/auth-login.js')}}"></script>
@endsection
@endsection
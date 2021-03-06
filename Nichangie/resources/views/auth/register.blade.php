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
                    <h5>{{ __('Already have an account?')}}<a href="{{ route('login') }}">{{ __('Login')}} </a></h5>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="default-form-area">
                    <div class="sec-title">
                        <h1>{{ __('Registration')}}</h1>
                    </div>
                    @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                        <p>{{ $message }}</p>
                    </div>
                    @endif
                    @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <strong>Whoops!</strong> There were some problems with your input.<br><br>
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <form id="contact-form" name="contact_form" class="contact-form" action="{{ route('register') }}" enctype="multipart/form-data" method="post">
                        @csrf
                        <div class="row clearfix">
                            <div class="col-lg-6 col-md-6 column">
                                <div class="form-group">
                                    <label for="name">{{ __('First Name*')}}</label>
                                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="" placeholder="Enter first name" required>
                                    @error('name')
                                    <span class="text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 column">
                                <div class="form-group">
                                    <label for="lname">{{ __('Last Name*')}}</label>
                                    <input type="text" id="lname" name="lastname" class="form-control @error('lastname') is-invalid @enderror" value="" placeholder="Enter last name" required>
                                    @error('lastname')
                                    <span class="text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 column">
                                <div class="form-group">
                                    <label for="email">{{ __('Email (optional)')}}</label>
                                    <input type="email" id="email" name="email" class="form-control" placeholder="Email">
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 column">
                                <div class="form-group">
                                    <label for="phone">{{ __('Phone *')}}</label>
                                    <input type="tel" id="phone" name="phonenumber" class="form-control @error('phone') is-invalid @enderror" required>
                                    @error('phone')
                                    <span class="text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 column">
                                <div class="form-group">
                                    <label for="idtype">{{ __('ID Type *')}}</label>
                                    <select class="filters-select form-control selectmenu" name="idtype">
                                        <option value="National ID">National ID</option>
                                        <option value="Driver Licence ID">Driver Licence ID</option>
                                        <option value="Passport">Passport</option>
                                    </select>
                                    @error('idtype')
                                    <span class="text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 column">
                                <div class="form-group">
                                    <label for="idlink">{{ __('ID Photo *')}}</label>
                                    <input type="file" id="idlink" name="idlink" class="form-control" required>
                                    @error('idlink')
                                    <span class="text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 column">
                                <div class="form-group">
                                    <label for="address">Address Location *</label>
                                    <input type="text" name="address" class="form-control @error('address') is-invalid @enderror" placeholder="Enter location" required>
                                    @error('address')
                                    <span class="text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 column">
                                <div class="form-group">
                                    <label for="district">{{ __('District Location')}}</label>
                                    <input type="text" name="district" class="form-control @error('district') is-invalid @enderror" placeholder="Enter district">
                                    @error('district')
                                    <span class="text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 column">
                                <div class="form-group">
                                    <label for="town_city">{{ __('City')}}</label>
                                    <select class="filters-select form-control selectmenu" name="town_city">
                                        <option value="">Please select</option>
                                        <option value="Shinyanga">Shinyanga</option>
                                        <option value="Simiyu">Simiyu</option>
                                        <option value="Kagera">Kagera</option>
                                        <option value="Dodoma">Dodoma</option>
                                        <option value="Kilimanjaro">Kilimanjaro</option>
                                        <option value="Mara">Mara</option>
                                        <option value="Tabora">Tabora</option>
                                        <option value="Morogoro">Morogoro</option>
                                        <option value="Zanzibar South">Zanzibar South</option>
                                        <option value="Pemba South">Pemba South</option>
                                        <option value="Zanzibar North">Zanzibar North</option>
                                        <option value="Singida">Singida</option>
                                        <option value="Zanzibar West">Zanzibar West</option>
                                        <option value="Mtwara">Mtwara</option>
                                        <option value="Rukwa">Rukwa</option>
                                        <option value="Kigoma">Kigoma</option>
                                        <option value="Mwanza">Mwanza</option>
                                        <option value="Njombe">Njombe</option>
                                        <option value="Geita">Geita</option>
                                        <option value="Katavi">Katavi</option>
                                        <option value="Lindi">Lindi</option>
                                        <option value="Manyara">Manyara</option>
                                        <option value="Pwani">Pwani</option>
                                        <option value="Ruvuma">Ruvuma</option>
                                        <option value="Tanga">Tanga</option>
                                        <option value="Pemba North">Pemba North</option>
                                        <option value="Iringa">Iringa</option>
                                        <option value="Dar es Salaam">Dar es Salaam</option>
                                        <option value="Arusha">Arusha</option>
                                        <option value="Mbeya">Mbeya</option>
                                        <option value="Songwe">Songwe</option>
                                    </select>
                                    @error('town_city')
                                    <span class="text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 column">
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
                            <div class="col-lg-6 col-md-6 column">
                                <div class="form-group">
                                    <label for="password_confirmation">{{ __('Confirm Password *')}}</label>
                                    <input type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" placeholder="Confirm password" required>
                                    @error('password_confirmation')
                                    <span class="text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 column">
                                <div class="row">
                                    <div class="form-group flex-box col-lg-2 col-md-2 col-sm-12">
                                        <div class="submit-btn">
                                            <button class="theme-btn btn-style-one" type="submit" data-loading-text="Please wait..."><span>{{ __('Register')}}</span></button>
                                        </div>
                                    </div>
                                    <div class="col-lg-10 col-md-10 col-sm-12">
                                        <div class="right mt-2">
                                            <h6 class="text">By creating an account, you are agreeing to our terms and conditions.</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
    var utilUrl = "{{ asset('assets/js/intl-tel-input/js/utils.js?1638200991544')}}"
</script>
<script src="{{ asset('assets/js/auth-register.js')}}"></script>
@endsection
@endsection
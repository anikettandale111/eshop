@extends('frontend.layouts.master')
@section('meta_title', 'Sign In | ' . get_settings('site_title'))
@section('content')

<main class="main-content">
    <!-- Login Area -->
    <section class="login-area">
        <div class="container">
            <div class="form-content">
                <div class="form-title d-flex">
                    <a href="javascript:;">
                        <h2 class="mr-2 {{(session()->has('form_type') && session()->get('form_type') == 'register') ? 'text-muted':'font-weight-bold' }} " id="login-form-link">Sign In</h2>
                    </a> | <a href="javascript:;">
                        <h2 class="ml-2 {{(session()->has('form_type') && session()->get('form_type') == 'register') ? 'font-weight-bold':'text-muted' }}" id="register-form-link">Sign Up</h2>
                    </a>
                </div>
                <form class="form-wrapper" action="{{ route('login') }}" id="login_form" style="display: {{(session()->has('form_type') && session()->get('form_type') == 'register') ? 'none':'block' }}" method="post">
                    @csrf
                    <div class="form-group">
                        <label>Mobile</label>
                        <input type="text" pattern="[0-9]{10}" class="form-control" name="phone" placeholder="9999999999" value="{{ old('phone') }}">
                        @error('phone')
                        <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                    <!-- <div class="form-group">
                        <label>Password</label>
                        <input type="password" class="form-control" name="password" placeholder="******">
                        @error('password')
                        <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div> -->
                    <!-- <a href="{{ route('password.request') }}" class="forgot-password float-right">Forgot password?</a> -->
                    <button id="login_btn" type="submit" class="default-btn secondary-btn mt-5">Sign In <span class="bx bx-right-arrow-alt float-right ml-3"></span></button>
                </form>
                <form class="form-wrapper" action="{{ route('register') }}" id="register_form" style="display: {{(session()->has('form_type') && session()->get('form_type') == 'register') ? 'block':'none' }}" method="post">
                    @csrf
                    <input type="hidden" name="form_type" value="register">
                    <p class="text-danger user_exists"></p>
                    <p class="text-success otp_sent"></p>
                    <div class="form-group">
                        <label>Name</label>
                        <p class="text-danger name_error"></p>
                        <input type="text" class="form-control" name="full_name" id="full_name" value="{{ old('full_name') }}" placeholder="Alex Smith">
                        @error('name')
                        <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Mobile Number</label>
                        <p class="text-danger phone_error"></p>
                        <input type="phone" pattern="[0-9]{10}" class="form-control" name="phone" id="phone" value="{{ old('phone') }}" placeholder="9999999999">
                        @error('phone')
                        <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Your E-mail</label>
                        <p class="text-danger email_error"></p>
                        <input type="email" class="form-control" name="email" id="email" value="{{ old('email') }}" placeholder="example@gmail.com">
                        @error('email')
                        <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                    <!-- <div class="form-group">
                        <label>Password</label>
                        <input type="password" class="form-control" placeholder="******" name="password">
                        @error('password')
                        <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input type="password" class="form-control" placeholder="******" name="password_confirmation">
                    </div> -->
                    <div class="form-group d-none otpdiv">
                        <label>Mobile OTP</label>
                        <p class="text-danger otp_check"></p>
                        <input type="number " class="form-control" placeholder="******" name="mobile_otp" id="mobile_otp">
                        @error('password')
                        <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="button" id="get_otp_btn" class="default-btn secondary-btn mt-5">Get OTP <span class="bx bx-right-arrow-alt float-right ml-3"></span></button>
                    <button type="submit" id="register_btn" class="default-btn secondary-btn mt-5 d-none">Sign Up <span class="bx bx-right-arrow-alt float-right ml-3"></span></button>
                </form>
            </div>
        </div>
    </section>
    <!-- Login Area Ends-->

</main>
@endsection

@push('scripts')
<script>
    $('#login_btn').click(function() {
        $('#login_btn').html('<i class="fas fa-spinner fa-spin"></i>');
        $('#login_form').submit();
    });
    $('#get_otp_btn').click(function() {
        if ($('#full_name').val() === '') {
            $('.name_error').text('Please Enter Your Name');
            $('.name_error').removeClass('d-none');
            return false;
        }
        $('.text-danger').addClass('d-none');
        if ($('#phone').val().length !== 10 || !$.isNumeric($('#phone').val())) {
            $('.phone_error').text('Please Enter Your Phone');
            $('.phone_error').removeClass('d-none');
            return false;
        }
        $('.text-danger').addClass('d-none');
        var emailPattern = /^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i;
        if (!emailPattern.test($('#email').val())) {
            $('.email_error').text('Please Enter a Valid Email');
            $('.email_error').removeClass('d-none');
            return false;
        }
        $('.text-danger').addClass('d-none');
        $('.otpdiv').css('display', 'block');
        $('#mobile_otp').removeClass('d-none');
        sendOTP();
        // $('#login_form').submit();
    });
    $('#register_btn').click(function() {
        if(!$.isNumeric($('#mobile_otp').val()) && $('#mobile_otp').val().length !== 4){
            $('.otp_check').removeClass('d-none');
            $('.otp_check').text('Please enter 4 Digit OTP');
            return false;
        }
        verifyOTP(otp);
    });
    $(function() {
        $('#login-form-link').click(function(e) {
            $("#login_form").delay(100).fadeIn(100);
            $("#register_form").fadeOut(100);
            $('#register-form-link').addClass('text-muted');
            $('#register-form-link').removeClass('font-weight-bold');
            $(this).removeClass('text-muted');
            $(this).addClass('font-weight-bold');
            e.preventDefault();
        });
        $('#register-form-link').click(function(e) {
            $("#register_form").delay(100).fadeIn(100);
            $("#login_form").fadeOut(100);
            $('#login-form-link').addClass('text-muted');
            $('#login-form-link').removeClass('font-weight-bold');
            $(this).removeClass('text-muted');
            $(this).addClass('font-weight-bold');
            e.preventDefault();
        });
    });

    function sendOTP() {
        $('.text-danger').text('');
        $('.text-success').text('');
        var phone = $('#phone').val();
        $.ajax({
            url: '{{url("send-otp")}}',
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                phone:phone,
                full_name:$('#full_name').val(),
                email:$('#email').val(),
            },
            beforeSend: function() {
                $("#loading").show();
            },
            complete: function() {
                $("#loading").hide();
            },
            success: function(response) {
                if (response.status == 'success') {
                    $('.otp_sent').text(response.message);
                    $('.otpdiv').removeClass('d-none');
                    $("#loading").hide();
                    $("#get_otp_btn").addClass('d-none');
                    $("#register_btn").removeClass('d-none');
                } else {
                    console.log(response);
                    $('.user_exists').text(response.message);
                    $('.otpdiv').addClass('d-none');
                    $("#loading").hide();
                    $('.text-danger').removeClass('d-none');
                }
            }
        });
    }
    function verifyOTP() {
        var phone = $('#phone').val();
        $.ajax({
            url: '{{url("verify-otp")}}',
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                phone:phone,
                otp:mobile_otp,
                full_name:$('#full_name').val(),
                email:$('#email').val(),
            },
            beforeSend: function() {
                $("#loading").show();
            },
            complete: function() {
                $("#loading").hide();
            },
            success: function(response) {
                if (response.status == 'success') {
                    $('#register_btn').html('<i class="fas fa-spinner fa-spin"></i>');
                    $('#register_form').submit();
                } else {
                    console.log(response);
                    $('.user_exists').text(response.message);
                    $('.otpdiv').removeClass('d-none');
                    $("#loading").hide();
                }
            }
        });
    }
</script>
@endpush
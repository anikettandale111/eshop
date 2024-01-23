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
                        <label for="">Email</label>
                        <input type="email" class="form-control" name="email" placeholder="example@gmail.com" value="{{ old('email') }}">
                        @error('email')
                        <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="">Password</label>
                        <input type="password" class="form-control" name="password" placeholder="******">
                        @error('password')
                        <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                    <a href="{{ route('password.request') }}" class="forgot-password float-right">Forgot password?</a>
                    <button id="login_btn" type="submit" class="default-btn secondary-btn mt-5">Sign In <span class="bx bx-right-arrow-alt float-right ml-3"></span></button>
                </form>
                <form class="form-wrapper" action="{{ route('register') }}" id="register_form" style="display: {{(session()->has('form_type') && session()->get('form_type') == 'register') ? 'block':'none' }};" method="post">
                    @csrf
                    <input type="hidden" name="form_type" value="register">
                    <div class="form-group">
                        <label for="">Name</label>
                        <input type="text" class="form-control" name="full_name" value="{{ old('full_name') }}" placeholder="Alex Smith">
                        @error('name')
                        <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="">Mobile Number</label>
                        <input type="phone" pattern="[0-9]{10}" class="form-control" name="phone" value="{{ old('phone') }}" placeholder="9999999999">
                        @error('phone')
                        <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="">Your E-mail</label>
                        <input type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="example@gmail.com">
                        @error('email')
                        <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="">Password</label>
                        <input type="password" class="form-control" placeholder="******" name="password">
                        @error('password')
                        <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="">Confirm Password</label>
                        <input type="password" class="form-control" placeholder="******" name="password_confirmation">

                    </div>


                    <button type="submit" id="register_btn" class="default-btn secondary-btn mt-5">Sign Up <span class="bx bx-right-arrow-alt float-right ml-3"></span></button>


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
    $('#register_btn').click(function() {
        $('#register_btn').html('<i class="fas fa-spinner fa-spin"></i>');
        $('#register_form').submit();
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
</script>
@endpush
@extends('layouts.app')

<!-- Main Content -->
@section('content')
    <div class="col-md-6 login-container bs-reset">
        <img class="login-logo login-6" src="{{secure_asset('assets/pages/img/login/login-invert.png') }}" />
        <div class="login-content">
            <h1>Forgot Password ?</h1>
            {{--<p> Lorem ipsum dolor sit amet, coectetuer adipiscing elit sed diam nonummy et nibh euismod aliquam erat volutpat. Lorem ipsum dolor sit amet, coectetuer adipiscing. </p>--}}
            <!-- BEGIN FORGOT PASSWORD FORM -->
            <form class="login-form" action="javascript:;" method="post">
                {{--<h3>Forgot Password ?</h3>--}}
                <p> Enter your e-mail address below to reset your password. </p>
                <div class="form-group">
                    <input class="form-control placeholder-no-fix" type="email" autocomplete="off" placeholder="Email" name="email" value="{{ old('email') }}" /> </div>
                    @if ($errors->has('email'))
                        <span class="help-block">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                    @endif
                <div class="form-actions">
                    <a href="{{ secure_url('/') }}" id="back-btn" class="btn blue btn-outline">Back</a>
                    <button type="submit" class="btn blue uppercase pull-right">Submit</button>
                </div>
            </form>
            <!-- END FORGOT PASSWORD FORM -->

        </div>
        <div class="login-footer">
            <div class="row bs-reset">
                <div class="col-xs-5 bs-reset">
                    <ul class="login-social">
                        <li>
                            <a href="javascript:;">
                                <i class="icon-social-facebook"></i>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:;">
                                <i class="icon-social-twitter"></i>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:;">
                                <i class="icon-social-dribbble"></i>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="col-xs-7 bs-reset">
                    <div class="login-copyright text-right">
                        <p>Copyright &copy; SSL Wireless <?php echo date('Y'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

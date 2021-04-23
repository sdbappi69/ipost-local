@extends('layouts.app')

@section('content')
        <div class="col-md-6 login-container bs-reset">
            <img class="login-logo login-6" style="height:100px;" src="{{asset('assets/pages/img/login/login-invert.png') }}" />
            <div class="login-content">
                <h1>Login to System</h1>
                <!-- <p> BIDDYUT TAKES THE COMPLICATION OUT OF LOCAL DELIVERY </p> -->
                <form action="{{ secure_url('/login') }}" class="login-form" method="post">
                    {{ csrf_field() }}
                    <div class="alert alert-danger display-hide">
                        <button class="close" data-close="alert"></button>
                        <span>Enter any username and password. </span>
                    </div>
                    @if ($errors->has('email'))
                        <div class="alert alert-danger">
                            <button class="close" data-close="alert"></button>
                            <span>{{ $errors->first('email') }}</span>
                        </div>
                    @endif
                    @if ($errors->has('password'))
                        <div class="alert alert-danger">
                            <button class="close" data-close="alert"></button>
                            <span>{{ $errors->first('password') }}</span>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-xs-6">
                            <input class="form-control form-control-solid placeholder-no-fix form-group" type="email" autocomplete="off" placeholder="Email" name="email" value="{{ old('email') }}" required/> </div>
                        <div class="col-xs-6">
                            <input class="form-control form-control-solid placeholder-no-fix form-group" type="password" autocomplete="off" placeholder="Password" name="password" value="{{ old('password') }}" required/> </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <label class="rememberme mt-checkbox mt-checkbox-outline">
                                <input type="checkbox" name="remember" /> Remember me
                                <span></span>
                            </label>
                        </div>
                        <div class="col-sm-8 text-right">
                            <div class="forgot-password">
                                <a href="{{ secure_url('/password/reset') }}" class="forget-password">Forgot Password?</a>
                            </div>
                            <button class="btn blue" type="submit">Sign In</button>
                        </div>
                    </div>
                </form>
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
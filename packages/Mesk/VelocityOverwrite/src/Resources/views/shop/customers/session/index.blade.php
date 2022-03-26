@extends('shop::layouts.master')

@section('page_title')
    {{ __('shop::app.customer.login-form.page-title') }}
@endsection

@section('content-wrapper')
    <div class="auth-content form-container">

        {!! view_render_event('bagisto.shop.customers.login.before') !!}

            <div class="container">
                <div class="col-lg-10 col-md-12 offset-lg-1">
                    <div class="heading">
                        <h2 class="fs24 fw6">
                            {{ __('velocity::app.customer.login-form.customer-login')}} <img loading="lazy" alt="wave" src="{{ asset('/themes/velocity/assets/images/wave-1.png') }}">
                        </h2>

                        <a href="{{ route('customer.register.index') }}" class="btn-new-customer">
                            <button type="button" class="theme-btn light forgot-login-btn">
                                {{ __('velocity::app.customer.login-form.sign-up')}}
                            </button>
                        </a>
                    </div>

                    <div class="body col-12 forgot-input">
                        

                        <form
                            method="POST"
                            action="{{ route('customer.session.create') }}"
                            @submit.prevent="onSubmit">

                            {{ csrf_field() }}

                            <div class="form-group" :class="[errors.has('email') ? 'has-error' : '']">
                                <label for="email" class="mandatory label-style">
                                    {{ __('shop::app.customer.login-form.email') }}
                                </label>

                                <input
                                    type="text"
                                    class="form-style"
                                    name="email"
                                    v-validate="'required|email'"
                                    value="{{ old('email') }}"
                                    data-vv-as="&quot;{{ __('shop::app.customer.login-form.email') }}&quot;" />

                                <span class="control-error" v-if="errors.has('email')" v-text="errors.first('email')"></span>
                            </div>

                            <div class="form-group" :class="[errors.has('password') ? 'has-error' : '']">
                                <label for="password" class="mandatory label-style">
                                    {{ __('shop::app.customer.login-form.password') }}
                                </label>

                                <input
                                    type="password"
                                    class="form-style"
                                    name="password"
                                    v-validate="'required'"
                                    value="{{ old('password') }}"
                                    data-vv-as="&quot;{{ __('shop::app.customer.login-form.password') }}&quot;" />

                                <span class="control-error" v-if="errors.has('password')" v-text="errors.first('password')"></span>

                                <a href="{{ route('customer.forgot-password.create') }}" class="float-right forgot-password-line">
                                    {{ __('shop::app.customer.login-form.forgot_pass') }}
                                </a>

                                <div class="mt10">
                                    @if (Cookie::has('enable-resend'))
                                        @if (Cookie::get('enable-resend') == true)
                                            <a href="{{ route('customer.resend.verification-email', Cookie::get('email-for-resend')) }}">{{ __('shop::app.customer.login-form.resend-verification') }}</a>
                                        @endif
                                    @endif
                                </div>
                            </div>
			
                            <div class="form-group">

                                {!! Captcha::render() !!}

                            </div>

                            {!! view_render_event('bagisto.shop.customers.login_form_controls.after') !!}

                            <input class="theme-btn forgot-btn-login" type="submit" value="{{ __('shop::app.customer.login-form.button_title') }}">
							
							<div class="form-header mt-50 text-center">
							<p class="fs16">
                                {{ __('velocity::app.customer.login-form.form-login-text')}}
                            </p>
							</div>
							
							{!! view_render_event('bagisto.shop.customers.login_form_controls.before') !!}

                        </form>
                    </div>
                </div>
            </div>

        {!! view_render_event('bagisto.shop.customers.login.after') !!}
    </div>
@endsection

@push('scripts')

{!! Captcha::renderJS() !!}

@endpush
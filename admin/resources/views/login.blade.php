<!doctype html>
<html lang="en">
<head>
<title>Quickbit-Admin Ver1.0.0 :: Login</title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<meta name="description" content="Quickbit Admin Website">
<meta name="author" content="Garry Tayler">

<link rel="icon" href="{{URL('/assets/img/favicon.ico')}}" type="image/x-icon">
<!-- VENDOR CSS -->
<link rel="stylesheet" href="{{URL('/assets/vendor/bootstrap/css/bootstrap.min.css')}}">
<link rel="stylesheet" href="{{URL('/assets/vendor/font-awesome/css/font-awesome.min.css')}}">
<link rel="stylesheet" href="{{URL('/assets/vendor/toastr/toastr.min.css')}}">
<!-- MAIN CSS -->
<link rel="stylesheet" href="{{URL('/assets/css/main.css')}}">
<link rel="stylesheet" href="{{URL('/assets/css/color_skins.css')}}">
<style>
.auth-main:after {
    content: '';
    position: absolute;
    right: 0;
    top: 0;
    width: 100%;
    height: 100%;
    z-index: -2;
    background: url({{URL('/assets/img/auth_bg.jpg')}}) repeat;
    background-size: cover;
}
.help-block {
    color: rgb(185, 74, 72);
}
</style>
</head>
<body class="theme-blush">
	<!-- WRAPPER -->
	<div id="wrapper">
		<div class="vertical-align-wrap">
			<div class="vertical-align-middle auth-main">
				<div class="auth-box">
                    <div class="top">
                        <img src="{{URL('/assets/img/brand.png')}}" alt="Logo">
                    </div>
					<div class="card">
                        <div class="header">
                            <p class="lead">アカウントにログインする</p>
                        </div>
                        <div class="body">
                            <form class="form-auth-small" method="POST" action="{{url('/login')}}">
                                @csrf
                                <div class="form-group">
                                    <label for="signin-email" class="control-label sr-only">ログイン ID</label>
                                    <input type="text" name="login_id" class="form-control" id="signin-email" value="" placeholder="ログイン ID" data-validation="required">
                                </div>
                                <div class="form-group">
                                    <label for="signin-password" class="control-label sr-only">パスワード</label>
                                    <input type="password" name="password" class="form-control" id="signin-password" value="" placeholder="パスワード" data-validation="required">
                                </div>
                                <div class="form-group clearfix">
                                    <label class="fancy-checkbox element-left">
                                        <input type="checkbox">
                                        <span>私を忘れないで</span>
                                    </label>
                                </div>
                                <button type="submit" class="btn btn-primary btn-lg btn-block">ログイン</button>
                            </form>
                        </div>
                    </div>
				</div>
			</div>
		</div>
	</div>
    <!-- END WRAPPER -->
    <script src="{{URL('/assets/bundles/libscripts.bundle.js')}}"></script>
    <script src="{{URL('/assets/bundles/vendorscripts.bundle.js')}}"></script>
    <script src="{{URL('/assets/vendor/toastr/toastr.js')}}"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.3.26/jquery.form-validator.min.js"></script>
    <script>
        $.formUtils.addValidator({
                name : 'required',
                validatorFunction : function(value, $el, config, language, $form) {
                    return value !== '';
                },
                errorMessage : 'このフィールドは必須です。',
                errorMessageKey: 'badField'
        });
        $.validate();
        @if(session()->has('errorCode'))
            toastr.options.timeOut = "3000";
            toastr.options.closeButton = true;
            toastr.options.positionClass = 'toast-top-right';
            toastr['error']('{{ __("message.failed") }}');
        @endif
    </script>
</body>
</html>

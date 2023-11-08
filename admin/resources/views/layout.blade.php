<!doctype html>
<html lang="en">
<head>
<title>Quickbit-Admin Ver1.0.0 :: {{ $page_title }}</title>
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
<!-- WaitMe -->
<link rel="stylesheet" href="{{URL('/assets/css/waitMe.css')}}">
<!-- MAIN CSS -->
<link rel="stylesheet" href="{{URL('/assets/css/main.css')}}">
<link rel="stylesheet" href="{{URL('/assets/css/color_skins.css')}}">
<link rel="stylesheet" href="{{URL('/css/app.css')}}">
<style>
    body {
        font-family: 'Yu Gothic'
    }
</style>
<script src="{{URL('/assets/bundles/libscripts.bundle.js')}}"></script>
<script src="{{URL('/assets/bundles/vendorscripts.bundle.js')}}"></script>
<script src="{{URL('/assets/js/waitMe.js')}}"></script>
<script src="{{URL('/assets/vendor/toastr/toastr.js')}}"></script>
<script src="{{URL('/assets/bundles/mainscripts.bundle.js')}}"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.3.26/jquery.form-validator.min.js"></script>
@include('global')
</head>
<body class="theme-blush">
<!-- Page Loader -->
<div class="page-loader-wrapper">
    <div class="loader">
        <div class="m-t-30"><img src="{{URL('/assets/img/brand_logo.svg')}}" width="36" height="47" alt="Logo"></div>
        <p>お待ちください...</p>
    </div>
</div>
<!-- Overlay For Sidebars -->
<div id="wrapper">
    <nav class="navbar navbar-fixed-top">
        <div class="container-fluid">
            <div class="navbar-btn">
                <button type="button" class="btn-toggle-offcanvas"><i class="lnr lnr-menu fa fa-bars"></i></button>
            </div>
            <div class="navbar-brand">
                <a href="{{URL('/')}}"><img src="{{URL('/assets/img/brand_black.png')}}" alt="Quickbit Admin Logo" class="img-responsive logo"></a>
            </div>
            <div class="navbar-right">
                <div id="navbar-menu">
                    <ul class="nav navbar-nav">
                        <li>
                            <a href="{{url('logout')}}" class="icon-menu"><i class="icon-login"></i></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    <!-- side bar menu -->
    <div id="left-sidebar" class="sidebar">
        <div class="">
            <div class="user-account">
                <img src="{{URL('/assets/img/user_com.jpg')}}" class="rounded-circle user-photo" alt="User Profile Picture">
                <div class="dropdown">
                    <span>ようこそ,</span>
                    <a href="javascript:void(0);" class="dropdown-toggle user-name" data-toggle="dropdown"><strong>{{ session()->get('admin_id') }}</strong></a>
                    <ul class="dropdown-menu dropdown-menu-right account">
                        <li><a href="{{URL('/admin/profile')}}"><i class="icon-user"></i>暗号変更</a></li>
                        <li class="divider"></li>
                        <li><a href="{{URL('/logout')}}"><i class="icon-power"></i>ログアウト</a></li>
                    </ul>
                </div>
                @if(session()->get('type') == 'admin')
                <hr>
                <ul class="row list-unstyled">
                    <li class="col-4">
                        <small>Bets</small>
                        <div>{{ $bets }}</div>
                    </li>
                    <li class="col-8">
                        <small>Profit</small>
                        <div>{{ $profit }} btc</div>
                    </li>
                </ul>
                @endif
            </div>
            <!-- Nav tabs -->
            @if(session()->get('type') == 'admin')
            <ul class="nav nav-tabs">
                <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#menu">メニュー</a></li>
                <li class="nav-item" data-toggle="tooltip" data-placement="top" title="設定"><a class="nav-link" data-toggle="tab" href="#setting"><i class="icon-settings"></i></a></li>
            </ul>
            @endif
            <!-- Tab panes -->
            <div class="tab-content p-l-0 p-r-0">
                <div class="tab-pane active" id="menu">
                    <nav id="left-sidebar-nav" class="sidebar-nav">
                        <ul id="main-menu" class="metismenu">
                            @if(session()->get('type') == 'admin')
                                    <li class="{{ $page_title == 'ダッシュボード' ? 'active' : '' }}">
                                        <a href="{{URL('/dashboard')}}"><i class="icon-home"></i> <span>ダッシュボード</span></a>
                                    </li>
                                    <li class="{{ ($page_title == 'ユーザー管理' || $page_title == 'ユーザー取引履歴') ? 'active' : '' }}">
                                        <a href="{{URL('/users')}}"><i class="icon-user"></i> <span>ユーザー管理</span></a>
                                    </li>
                                    <li class="{{ ($page_title == 'Bot管理') ? 'active' : '' }}">
                                        <a href="{{URL('/bots')}}"><i class="icon-ghost"></i> <span>Bot管理</span></a>
                                    </li>
                                    <li class="{{ ($parent_title == 'ウォレット管理') ? 'active' : '' }}">
                                        <a href="#" class="has-arrow"><i class="icon-wallet"></i> <span>ウォレット管理</span></a>
                                        <ul>
                                            <li class="{{ ($page_title == '月次統計') ? 'active' : '' }}"><a href="{{URL('/wallets')}}">月次統計</a></li>
                                            <li class="{{ ($page_title == '詳細履歴') ? 'active' : '' }}"><a href="{{URL('/wallets/history')}}">詳細履歴</a></li>
                                        </ul>
                                    </li>
                                    <li class="{{ ($parent_title == '入出金管理') ? 'active' : '' }}">
                                        <a href="#" class="has-arrow"><i class="icon-basket"></i> <span>入出金管理</span></a>
                                        <ul>
                                            <li class="{{ ($page_title == '入金履歴') ? 'active' : '' }}"><a href="{{URL('/deposits')}}">入金履歴</a></li>
                                            <li class="{{ ($page_title == '出金申請') ? 'active' : '' }}"><a href="{{URL('/withdraws')}}">出金申請</a></li>
                                        </ul>
                                    </li>
                                    <li class="{{ ($page_title == 'ゲーム履歴') ? 'active' : '' }}">
                                        <a href="{{URL('/game_history')}}"><i class="icon-film"></i> <span>ゲーム履歴</span></a>
                                    </li>
                                    <li class="{{ ($page_title == 'アフィリエイト' || $page_title == 'アフィリエイト月別通計' || $page_title == '報酬支払い履歴') ? 'active' : '' }}">
                                        <a href="{{URL('/affiliates')}}"><i class="icon-user-follow"></i> <span>アフィリエイト</span></a>
                                    </li>
                                    <li class="{{ ($parent_title == 'ユーザーサポート') ? 'active' : '' }}">
                                        <a href="#" class="has-arrow"><i class="icon-support"></i> <span>ユーザーサポート</span></a>
                                        <ul>
                                            <li class="{{ ($page_title == 'Faq カテゴリ') ? 'active' : '' }}"><a href="{{URL('/faqs/category')}}">Faq カテゴリ</a> </li>
                                            <li class="{{ ($page_title == 'FAQ') ? 'active' : '' }}"><a href="{{URL('/faqs')}}">FAQ</a> </li>
                                            <li class="{{ ($page_title == 'terms of service') ? 'active' : '' }}"><a href="{{URL('/terms_service')}}">terms of service</a> </li>
                                        </ul>
                                    </li>
                            @else
                            <li class="{{ ($parent_title == 'アフィリエイト') ? 'active' : '' }}">
                                <a href="#" class="has-arrow"><i class="icon-user-follow"></i> <span>アフィリエイト</span></a>
                                <ul>
                                    <li class="{{ ($page_title == 'アフィリエイトユーザー') ? 'active' : '' }}"><a href="{{URL('/user_affiliates/user')}}">アフィリエイトユーザー</a> </li>
                                    <li class="{{ ($page_title == '報酬支払い履歴') ? 'active' : '' }}"><a href="{{URL('/user_affiliates')}}">報酬支払い履歴</a> </li>
                                    <li class="{{ ($page_title == 'アフィリエイト月別通計') ? 'active' : '' }}"><a href="{{URL('/user_affiliates/monthly_list')}}">アフィリエイト月別通計</a> </li>
                                    <li class="{{ ($page_title == '紹介URL') ? 'active' : '' }}"><a href="{{URL('/user_affiliates/affiliate_link')}}">紹介URL</a> </li>
                                </ul>
                            </li>
                            @endif
                        </ul>
                    </nav>
                </div>
                @if(session()->get('type') == 'admin')
                @include('settings')
                @endif
            </div>
        </div>
    </div>
    <div id="main-content">
        <div class="container-fluid" id="main_content_page">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-5 col-md-8 col-sm-12">
                        <h2><a href="javascript:void(0);" class="btn btn-xs btn-link btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a> {{ $page_title }}</h2>
                        @if(!$is_child)
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{URL('/')}}"><i class="icon-home"></i></a></li>
                            <li class="breadcrumb-item active">{{ $page_title }}</li>
                        </ul>
                        @else
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{URL('/')}}"><i class="icon-home"></i></a></li>
                            <li class="breadcrumb-item">{{ $parent_title }}</li>
                            <li class="breadcrumb-item active">{{ $page_title }}</li>
                        </ul>
                        @endif
                    </div>
                    @yield('content')
                </div>
            </div>
    </div>
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
</script>
</body>

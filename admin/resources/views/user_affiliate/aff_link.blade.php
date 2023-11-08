@extends('layout')
@section('content')
    </div>
</div>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="{{ URL('/assets/vendor/sweetalert/sweetalert.css') }}" />
<script src="{{url('/assets/vendor/sweetalert/sweetalert.min.js')}}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<style>
    .search-box .body {
        padding-top: 5px;
    }
    .aff_link {
        color: #007bff;
        font-size: 16px;
        font-weight: bold;
        margin-left: 20px;
        margin-right: 20px;
    }
    #copy_button {
        margin-left: 20px;
        margin-top: 20px;
    }
</style>
<div class="row clearfix">
    <div class="col-md-12">
        <div class="card search-box">
            <div class="header">
                <h2>　紹介URL </h2>
            </div>
            <div class="body">
                <div class="row clearfix items-center">
                    <input type="text" class="aff_link form-control" readonly id="myInput" value="{{ $data['aff_link'] }}" />
                    <button type="button" id="copy_button" class="btn btn-danger"><i class="fa fa-copy"></i> <span>紹介URLコピー</span></button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $('#copy_button').on('click', function(e) {
        var copyText = document.getElementById("myInput");
        copyText.select();
        copyText.setSelectionRange(0, 99999)
        document.execCommand("copy");
        showToast('success', '紹介URLをclipboardにコピーしました。');
    });
</script>
@endsection

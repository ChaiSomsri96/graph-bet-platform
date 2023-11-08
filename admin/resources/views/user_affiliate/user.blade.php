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
    .badge-danger {
        background: #de4848;
        border-color: #de4848;
        color: white;
        font-size: 14px;
    }
    .badge-success {
        background: #22af46;
        border-color: #22af46;
        color: white;
        font-size: 14px;
    }
    .btn-default {
        background-color: #777;
        color: #fff;
        border-color: #777;
        padding: 6px 15px;
    }
    .badge-warning {
        background: #f3ad06;
        color: white;
    }
    .search_section {
        margin-left: 20px;
        margin-top: 15px;
    }
    .search-box .body {
        padding-top: 5px;
    }
    .mx-1 {
        margin-left: 2px;
        margin-right: 2px;
    }
    .kyc-span {
        cursor: pointer;
    }
    .kyc-span:hover {
        opacity: 0.9;
        transform: scale(1.1);
    }
    .help-block {
        font-size: 13px;
        color: #b94a48;
    }
    label {
        margin-bottom: 0px;
    }
    @media only screen and (max-width: 767px) {
        .tool-bar {
            flex-direction: column;
            align-items: baseline;
        }
        .tool-bar button:not(:first-child),
        .tool-bar label,
        .tool-bar input {
            margin-top: 15px;
        }
    }
</style>
<div class="row clearfix">
    <div class="col-md-12">
        <div class="card search-box">
            <div class="header">
                <h2>検索条件</h2>
            </div>
            <div class="body">
                <div class="row clearfix items-end">
                    <div class="search_section">
                        <div class="mb-micro">アカウント名</div>
                        <input class="form-control w200" placeholder="ex: nVGLT5as" id="username" />
                    </div>
                    <div class="search_section">
                        <div class="mb-micro">メールアドレス</div>
                        <input class="form-control w200" placeholder="ex: www@gmail.com" id="email" />
                    </div>
                    <div class="search_section">
                        <div class="mb-micro">アフィリエイト·コード</div>
                        <input class="form-control w200" placeholder="ex: Qb_12b32053" id="aff_code" />
                    </div>
                    <button type="button" id="search_button" class="btn btn-primary search_section"><i class="fa fa-search"></i> <span>上記内容で検索</span></button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row clearfix">
    <div class="col-md-12">
        <div class="card">
            <div class="header" style="padding-bottom: 10px;">
                <h2>ユーザーリスト</h2>
                <div class="flex mt-4 items-center tool-bar">
                    <button type="button" class="btn btn-primary mr-4" id="add-new-user-btn">アフィリエイトユーザー登録</button>
                    <label class="mr-2">紹介コード</label>
                    <input class="form-control w200 mr-4" value="{{$data['aff_code']}}" id="myInput" />
                    <button type="button" class="btn btn-success" id="copy-aff-code">コピー</button>
                </div>
            </div>
            <div class="body">
                <div class="table-responsive">
                    <table class="table center-aligned-table text-xs" id="user_list">
                        <thead>
                            <tr>
                                <th>ユーザーID</th>
                                <th>アカウント名</th>
                                <th>メールアドレス</th>
                                <th>登録日</th>
                                <th>総取引金額</th>
                                <th>アフィリエイトコード</th>
                            </tr>
                        </thead>
                        <tbody class="table-content">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="add-new-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="" id="user_form">
                @csrf
                <div class="modal-header">
                    <h4 class="title" id="defaultModalLabel">ユーザー情報</h4>
                </div>
                <div class="modal-body">
                        <input type="hidden" id="data_id" value="" />
                        <div class="row clearfix">
                            <div class="col-md-12">
                                <table class="text-xs w-full add-new-table" id="bot_form_data">
                                    <tbody>
                                        <tr>
                                            <th>
                                                アカウント名
                                                <span class="required-field">
                                                    必須
                                                </span>
                                            </th>
                                            <td>
                                                <input class="form-control w-full" name="USERNAME" id="USERNAME" data-validation="required" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                メールアドレス
                                            </th>
                                            <td>
                                                <input class="form-control w-full" name="EMAIL" id="EMAIL" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                パスワード
                                                <span class="required-field">
                                                    必須
                                                </span>
                                            </th>
                                            <td>
                                                <div class="flex items-baseline">
                                                    <div>
                                                        <input class="form-control w-full" name="PASSWORD" id="PASSWORD" data-validation="required" />
                                                    </div>
                                                    <button onclick="resetPassword()" type="button" class="btn btn-success ml-2" title="リセットパスワード">
                                                        <span class="sr-only">リセットパスワード</span> <i class="fa fa-refresh"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                アフィリエイトコード
                                                <span class="required-field">
                                                    必須
                                                </span>
                                            </th>
                                            <td>
                                                <div class="flex items-baseline">
                                                    <div>
                                                        <input class="form-control w-full" name="AFF_CODE" id="AFF_CODE" data-validation="required" />
                                                    </div>
                                                    <button onclick="generateAffiliateCode()" type="button" class="btn btn-success ml-2" title="アフィリエトコード生成">
                                                        <span class="sr-only">アフィリエトコード生成</span> <i class="fa fa-refresh"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">保存する</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">閉じる</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let noNecessaryServerPost = false;
let username = '', email = '', aff_code = '';

$('#copy-aff-code').on('click', function(e) {
        var copyText = document.getElementById("myInput");
        copyText.select();
        copyText.setSelectionRange(0, 99999)
        document.execCommand("copy");
        showToast('success', '紹介コードをclipboardにコピーしました。');
});

function resetPassword() {
    var str = makeid(10);
    $('#PASSWORD').val(str);
}

function generateAffiliateCode() {
    var str = 'Qb_' + makeid(8);
    $('#AFF_CODE').val(str);
}
$('#search_button').on('click', function(e) {
    username = $('#username').val();
    email = $('#email').val();
    aff_code = $('#aff_code').val();
    getData(0);
})

$('#add-new-user-btn').on('click', function(e) {
    $('#USERNAME').val('');
    $('#EMAIL').val('');
    $('#PASSWORD').val('');
    $('#AFF_CODE').val('');
    resetPassword();
    generateAffiliateCode();
    $('#add-new-modal').modal('show');
});

$('#user_form').on('submit', function(e) {
    e.preventDefault();
    if($('#USERNAME').val() == '' || $('#PASSWORD').val() == '' || $('#AFF_CODE').val() == '')
        return;
    let params = {};
    params['_token'] = "{{ csrf_token() }}";
    params['USERNAME'] = $('#USERNAME').val();
    params['PASSWORD'] = $('#PASSWORD').val();
    params['AFF_CODE'] = $('#AFF_CODE').val();
    params['EMAIL'] = $('#EMAIL').val();
    sendRequest("{{ url('/user_affiliates/user/create') }}", params, $('#add-new-modal'), 2)
    .then((result) => {
        $('#add-new-modal').modal('hide');
        swal("操作成功", "アフィリエットのユーザーが追加されました。", "success");
        getData(0);
        $('#USERNAME').val('');
        $('#EMAIL').val('');
        $('#PASSWORD').val('');
        $('#AFF_CODE').val('');
    })
    .catch(() => {
    });
});

function getData(lastID) {
    let params = {
        "_token": "{{ csrf_token() }}"
    };
    if(lastID == 0) {
        noNecessaryServerPost = false;
        $('.table-content').html('<tr class="load-more" lastID="0">' +
                '<td class="loading_content" colspan="14" style="text-align: center;">' +
                    '<img src="{{url('assets/img/loading.gif')}}"/>' +
                '</td>' +
            '</tr>');
    }
    params['id'] = lastID;
    params['username'] = username;
    params['email'] = email;
    params['aff_code'] = aff_code;
    $.ajax(  { url: "{{ url('') }}/user_affiliates/user/get_list",
            type: 'POST',
            data: params,
            beforeSend: function() {
                $('.load-more').show();
            },
            success: function(result){
                if(result.status != 'success') {
                    showToast('error', '{{ __("message.server_error") }}');
                    return;
                }
                let html = '';
                if(result.data.length < 1) {
                    noNecessaryServerPost = true;
                    $('.load-more').remove();
                    if(lastID == 0) {
                        html = '<tr class="tr_row"><td class="loading_content" style="text-align:center;" colspan="14">{{ __("message.table-no-content") }}</td></tr>';
                        $('.table-content').append(html);
                    }
                    return;
                }
                if(!result.is_tail_data) {
                    noNecessaryServerPost = true;
                }
                if(result.data.length > 0) { //block_yn
                    for(let i = 0; i < result.data.length; i ++) {
                        html += '<tr> \
                                <td>' + result.data[i]['id'] + '</td> \
                                <td>' + result.data[i]['username'] + '</td> \
                                <td>' + result.data[i]['email'] + '</td> \
                                <td>' + getDateString(result.data[i]['regdate']) + '</td> \
                                <td class="font-bold">' + result.data[i]['total_trans_sum'] + ' btc</td> \
                                <td>' + result.data[i]['affiliate_code'] + '</td> \
                            </tr>'
                    }
                    lastID = parseInt(lastID) + parseInt(result.data.length);
                    html += '<tr class="load-more" lastID="' + lastID  + '" style="display:none;"><td class="loading_content" style="text-align: center;" colspan="14">' +
                '<img src=\'{{url("assets/img/loading.gif")}}\'/> ' +
                '</td></tr>';
                }
                else {
                    if(lastID == 0)
                        html = '<tr class="tr_row"><td class="loading_content" style="text-align:center;" colspan="14">{{ __("message.table-no-content") }}</td></tr>';
                }
                $('.load-more').remove();
                $('.table-content').append(html);
                if( !($('body')[0].scrollHeight > $(window).height()) ){
                    if(noNecessaryServerPost) {
                        return;
                    }
                    var lastIDAgain = $('.load-more').attr('lastID');
                    getData(lastIDAgain)
                }
            },
            error(xhr, status, error) {
                showToast('error', '{{ __("message.network_error") }}');
                $('.load-more').remove();
            }
    });
}
getData(0);

$(document).ready(function(){
    $(window).scroll(function(){
        if(noNecessaryServerPost)
            return;
        var lastID = $('.load-more').attr('lastID');
        if(($(window).scrollTop() >= ($(document).height() - $(window).height() - 1)) && (lastID != 0)){
            getData(lastID);
        }
    });
});
</script>
@endsection

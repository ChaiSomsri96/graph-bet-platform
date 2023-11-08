@extends('layout')
@section('content')
    </div>
</div>
<style>
    .search_section {
        margin-left: 20px;
        margin-top: 15px;
    }
    .search-box .body {
        padding-top: 5px;
    }
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
</style>
<link rel="stylesheet" href="{{ URL('/assets/vendor/sweetalert/sweetalert.css') }}" />
<script src="{{url('/assets/vendor/sweetalert/sweetalert.min.js')}}"></script>
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
                        <div class="mb-micro">その他の情報</div>
                        <input class="form-control w200" placeholder="詳細またはトランザクション情報" id="extra" />
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
            <div class="header">
                <h2>出金申請内訳</h2>
            </div>
            <div class="body">
                <div class="table-responsive">
                    <table class="table center-aligned-table text-xs" id="bitcoin_list">
                        <thead>
                            <tr>
                                <th>ユーザーID</th>
                                <th>アカウント名</th>
                                <th>入金額</th>
                                <th>取引状況</th>
                                <th>出金申請額</th>
                                <th>出金申請walletアドレス</th>
                                <th>残高</th>
                                <th>KYC</th>
                                <th>出金承認/非承認</th>
                            </tr>
                        </thead>
                        <tbody class="table-content">
                            <tr>
                                <td colspan="8" style="text-align: center;">
                                    内容がありません。
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="kyc-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <form method="POST" action="" id="user_form">
                @csrf
                <div class="modal-header">
                    <h4 class="title" id="defaultModalLabel">本当ですか?</h4>
                </div>
                <div class="modal-body">
                        <input type="hidden" id="data_kyc_id" value="" />
                        <div class="row clearfix">
                            <div class="col-md-12">
                                <div>
                                    承認、未承認どちらかを選択してください。
                                </div>
                            </div>
                        </div>
                </div>
                <div class="modal-footer flex justify-center">
                    <button type="button" id="make_agree" class="btn btn-success mr-4" style="width: 70px;">承認</button>
                    <button type="button" id="make_disagree" class="btn btn-danger" style="width: 70px;">未承認</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
let noNecessaryServerPost = false;
let username = '', email = '', extra = '';
$('#search_button').on('click', function(e) {
    username = $('#username').val();
    email = $('#email').val();
    extra = $('#extra').val();
    getData(0);
})
$('#make_agree').on('click', function(e) {
    let params = {};
    params['_token'] = "{{ csrf_token() }}";
    params['type'] = 6;
    params['id'] = $('#data_kyc_id').val();
    params['kyc'] = 'Y';
    sendRequest("{{ url('/users/change') }}", params, $('#kyc-modal'), 2)
    .then((result) => {
        $('#kyc-modal').modal('hide');
        swal("変更!", "KYC承認されました。", "success");
        getData(0);
    })
    .catch(() => {
    });
})
$('#make_disagree').on('click', function(e) {
    let params = {};
    params['_token'] = "{{ csrf_token() }}";
    params['type'] = 6;
    params['id'] = $('#data_kyc_id').val();
    params['kyc'] = 'N';
    sendRequest("{{ url('/users/change') }}", params, $('#kyc-modal'), 2)
    .then((result) => {
        $('#kyc-modal').modal('hide');
        swal("変更!", "KYCは承認されていません。", "success");
        getData(0);
    })
    .catch(() => {
    });
})


function agree(id) {
    swal({
        title: "本当ですか?",
        text: "出金申請が承認されます。",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#dc3545",
        confirmButtonText: "はい、承認します!",
        closeOnConfirm: false,
        cancelButtonText: "キャンセル"
    }, function () {
        let params = {};
        params['_token'] = "{{ csrf_token() }}";
        params['id'] = id;
        sendRequest("{{ url('/withdraws/agree_withdraw') }}", params, $('#bitcoin_list'), 2)
        .then((result) => {
            swal("承認!", "出金申請が承認されました。", "success");
            getData(0);
        })
        .catch(() => {
        });
    });
}

function disagree(id) {
    swal({
        title: "本当ですか?",
        text: "出金申請を拒否します。",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#dc3545",
        confirmButtonText: "はい、拒否します!",
        closeOnConfirm: false,
        cancelButtonText: "キャンセル"
    }, function () {
        let params = {};
        params['_token'] = "{{ csrf_token() }}";
        params['id'] = id;
        sendRequest("{{ url('/withdraws/disagree_withdraw') }}", params, $('#bitcoin_list'), 2)
        .then((result) => {
            swal("未承認!", "出国禁止申請が未承認のなりました。", "success");
            getData(0);
        })
        .catch(() => {
        });
    });
}

function kyc_change(id) {
    $('#data_kyc_id').val(id);
    $('#kyc-modal').modal("show");
}

function getData(lastID) {
    let params = {
        "_token": "{{ csrf_token() }}"
    };
    if(lastID == 0) {
        noNecessaryServerPost = false;
        $('.table-content').html('<tr class="load-more" lastID="0">' +
                '<td class="loading_content" colspan="8" style="text-align: center;">' +
                    '<img src="{{url('assets/img/loading.gif')}}"/>' +
                '</td>' +
            '</tr>');
    }
    params['id'] = lastID;
    params['username'] = username;
    params['email'] = email;
    params['extra'] = extra;

    $.ajax(  { url: "{{ url('') }}/withdraws/get_list",
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
                        html = '<tr class="tr_row"><td class="loading_content" style="text-align:center;" colspan="8">{{ __("message.table-no-content") }}</td></tr>';
                        $('.table-content').append(html);
                    }
                    return;
                }
                if(!result.is_tail_data) {
                    noNecessaryServerPost = true;
                }
                if(result.data.length > 0) {
                    for(let i = 0; i < result.data.length; i ++) {
                        html += '<tr> \
                                    <td>' + result.data[i]['user_id'] + '</td> \
                                    <td>' + result.data[i]['user_name'] + '</td> \
                                    <td>' + result.data[i]['total_deposit'] + '</td> \
                                    <td>' + result.data[i]['total_trans'] + '</td> \
                                    <td class="text-danger font-bold">' + result.data[i]['withdraw_request'] + '</td> \
                                    <td class="text-info font-bold">' + result.data[i]['detail'] + '</td>\
                                    <td>' + result.data[i]['wallet'] + '</td> \
                                    <td>' + result.data[i]['kyc'] + '</td> \
                                    <td>' + result.data[i]['action'] + '</td> \
                                </tr>'
                    }
                    lastID = parseInt(lastID) + parseInt(result.data.length);
                    html += '<tr class="load-more" lastID="' + lastID  + '" style="display:none;"><td class="loading_content" style="text-align: center;" colspan="8">' +
                '<img src=\'{{url("assets/img/loading.gif")}}\'/> ' +
                '</td></tr>';
                }
                else {
                    if(lastID == 0)
                        html = '<tr class="tr_row"><td class="loading_content" style="text-align:center;" colspan="8">{{ __("message.table-no-content") }}</td></tr>';
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

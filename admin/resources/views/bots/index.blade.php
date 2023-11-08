@extends('layout')
@section('content')
    </div>
</div>
<link rel="stylesheet" href="{{ URL('/assets/vendor/sweetalert/sweetalert.css') }}" />
<script src="{{url('/assets/vendor/sweetalert/sweetalert.min.js')}}"></script>
<style>
    .badge-success {
        background: #22af46;
        border-color: #22af46;
        color: white;
        font-size: 14px;
    }
    .badge-default {
        background: #9A9A9A;
        border-color: #9A9A9A;
        color: white;
        font-size: 14px;
    }
    .btn-default {
        background-color: #777;
        color: #fff;
        border-color: #777;
        padding: 6px 15px;
    }
    .mx-1 {
        margin-left: 2px;
        margin-right: 2px;
    }
    .search_section {
        margin-left: 20px;
        margin-top: 15px;
    }
    .search-box .body {
        padding-top: 5px;
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
                        <div class="mb-micro">bot名</div>
                        <input class="form-control w200" placeholder="ex: Lisa" id="username" />
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
                <h2>bot リスト</h2>
                <button type="button" class="btn btn-primary mt-4" id="add-new-bot-btn">ボット新規登録</button>
            </div>
            <div class="body">
                <div class="table-responsive">
                    <table class="table center-aligned-table text-xs" id="bot_list">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>bot名</th>
                                <th>登録時間</th>
                                <th>勝率</th> <!-- win rate -->
                                <th>参加率</th>  <!-- take rate -->
                                <th>最小ベッティング</th>
                                <th>最大バッティング</th>
                                <th>bot 状態</th>
                                <th>操作</th>
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
            <form method="POST" action="" id="bot_form">
                @csrf
                <div class="modal-header">
                    <h4 class="title" id="defaultModalLabel">ボット情報</h4>
                </div>
                <div class="modal-body">
                        <input type="hidden" id="data_id" value="" />
                        <div class="row clearfix">
                            <div class="col-md-12">
                                <table class="text-xs w-full add-new-table" id="bot_form_data">
                                    <tbody>
                                        <tr>
                                            <th>
                                                bot名
                                                <span class="required-field">
                                                    必須
                                                </span>
                                            </th>
                                            <td>
                                                <input class="form-control w-full" name="NAME" id="NAME" data-validation="required" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                勝率
                                                <span class="required-field">
                                                    必須
                                                </span>
                                            </th>
                                            <td>
                                                <div class="flex">
                                                    <div>
                                                        <input class="form-control w-full" name="WIN_RATE" id="WIN_RATE" data-validation="required" />
                                                    </div>
                                                    <div class="ml-2 mt-2">%</div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                参加率
                                                <span class="required-field">
                                                    必須
                                                </span>
                                            </th>
                                            <td>
                                                <div class="flex">
                                                    <div>
                                                        <input class="form-control w-full" name="TAKE_RATE" id="TAKE_RATE" data-validation="required" />
                                                    </div>
                                                    <div class="ml-2 mt-2">%</div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                最小ベッティング
                                                <span class="required-field">
                                                    必須
                                                </span>
                                            </th>
                                            <td>
                                                <div class="flex">
                                                    <div>
                                                        <input class="form-control w-full" name="MIN_BET" id="MIN_BET" data-validation="required" />
                                                    </div>
                                                    <div class="ml-2 mt-2">btc</div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                最大バッティング
                                                <span class="required-field">
                                                    必須
                                                </span>
                                            </th>
                                            <td>
                                                <div class="flex">
                                                    <div>
                                                        <input class="form-control w-full" name="MAX_BET" id="MAX_BET" data-validation="required" />
                                                    </div>
                                                    <div class="ml-2 mt-2">btc</div>
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
let username = '';
setInputFilter(document.getElementById('WIN_RATE'), function(value) {
            return /^\d*\.?\d*$/.test(value); // Allow digits and '.' only, using a RegExp
});
setInputFilter(document.getElementById('TAKE_RATE'), function(value) {
            return /^\d*\.?\d*$/.test(value); // Allow digits and '.' only, using a RegExp
});
setInputFilter(document.getElementById('MIN_BET'), function(value) {
            return /^\d*\.?\d*$/.test(value); // Allow digits and '.' only, using a RegExp
});
setInputFilter(document.getElementById('MAX_BET'), function(value) {
            return /^\d*\.?\d*$/.test(value); // Allow digits and '.' only, using a RegExp
});
$('#search_button').on('click', function(e) {
    username = $('#username').val();
    getData(0);
})
$('#bot_form').on('submit', function(e) {
    e.preventDefault();
    if($('#NAME').val() == '' || $('#MAX_BET').val() == '' || $('#MIN_BET').val() == '' || $('#WIN_RATE').val() == '' || $('#TAKE_RATE').val() == '')
        return;
    let params = {};
    params['_token'] = "{{ csrf_token() }}";
    params['WIN_RATE'] = $('#WIN_RATE').val();
    params['TAKE_RATE'] = $('#TAKE_RATE').val();
    params['MIN_BET'] = $('#MIN_BET').val();
    params['MAX_BET'] = $('#MAX_BET').val();
    params['NAME'] = $('#NAME').val();
    params['ID'] = $('#data_id').val();
    sendRequest("{{ url('/bots/create') }}", params, $('#add-new-modal'), 2)
    .then((result) => {
        $('#add-new-modal').modal('hide');
        swal("変更されました。", "ボット情報が変更されました。", "success");
        getData(0);
        $('#WIN_RATE').val('');
        $('#TAKE_RATE').val('');
        $('#MIN_BET').val('');
        $('#MAX_BET').val('');
        $('#NAME').val('');
        $('#data_id').val('');
    })
    .catch(() => {
    });
});
function remove(id) {
    swal({
        title: "本当ですか?",
        text: "このボットは削除され、現在のゲームには適用されません。",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#dc3545",
        confirmButtonText: "はい、削除してください!",
        closeOnConfirm: false,
        cancelButtonText: "キャンセル"
    }, function () {
        let params = {};
        params['_token'] = "{{ csrf_token() }}";
        params['type'] = 1;
        params['id'] = id;
        sendRequest("{{ url('/bots/change') }}", params, $('#bot_list'), 2)
        .then((result) => {
            swal("削除!", "Botが削除されました。", "success");
            getData(0);
        })
        .catch(() => {
        });
    });
}
function enable(id) {
    let params = {};
    params['_token'] = "{{ csrf_token() }}";
    params['type'] = 2;
    params['id'] = id;
    sendRequest("{{ url('/bots/change') }}", params, $('#bot_list'), 2)
    .then((result) => {
        swal("変更されました。", "Bot がイネーブルになっています。", "success");
        getData(0);
    })
    .catch(() => {
    });
}
function disable(id) {
    let params = {};
    params['_token'] = "{{ csrf_token() }}";
    params['type'] = 3;
    params['id'] = id;
    sendRequest("{{ url('/bots/change') }}", params, $('#bot_list'), 2)
    .then((result) => {
        swal("変更されました。", "Bot はディセーブルです。", "success");
        getData(0);
    })
    .catch(() => {
    });
}
function edit(id, name, min_bet, max_bet, win_rate, take_rate) {
    $('#data_id').val(id);
    $('#NAME').val(name);
    $('#MIN_BET').val(min_bet);
    $('#MAX_BET').val(max_bet);
    $('#WIN_RATE').val(win_rate);
    $('#TAKE_RATE').val(take_rate);
    $('#add-new-modal').modal('show');
}
$('#add-new-bot-btn').on('click', function(e) {
    $('#WIN_RATE').val('');
    $('#TAKE_RATE').val('');
    $('#MIN_BET').val('');
    $('#MAX_BET').val('');
    $('#NAME').val('');
    $('#data_id').val('');
    $('#add-new-modal').modal('show');
});
function getData(lastID) {
    let params = {
        "_token": "{{ csrf_token() }}"
    };
    if(lastID == 0) {
        noNecessaryServerPost = false;
        $('.table-content').html('<tr class="load-more" lastID="0">' +
                '<td class="loading_content" colspan="9" style="text-align: center;">' +
                    '<img src="{{url('assets/img/loading.gif')}}"/>' +
                '</td>' +
            '</tr>');
    }
    params['id'] = lastID;
    params['username'] = username;
    $.ajax(  { url: "{{ url('') }}/bots/get_list",
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
                        html = '<tr class="tr_row"><td class="loading_content" style="text-align:center;" colspan="9">{{ __("message.table-no-content") }}</td></tr>';
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
                                <td>' + result.data[i]['id'] + '</td> \
                                <td>' + result.data[i]['name'] + '</td> \
                                <td>' + getDateString(result.data[i]['regdate']) + '</td> \
                                <td>' + result.data[i]['win_rate'] + ' %</td> \
                                <td>' + result.data[i]['take_rate'] + ' %</td> \
                                <td style="font-weight: bold;">' + result.data[i]['min_bet'] + ' btc</td> \
                                <td style="font-weight: bold;">' + result.data[i]['max_bet'] + ' btc</td> \
                                <td>' + result.data[i]['status_html'] + '</td> \
                                <td> \
                                    <div class="flex"> \
                                    <button type="button" class="btn btn-info" title="Edit" onclick="edit(' + result.data[i]['id'] + ' , \'' + result.data[i]['name'] + '\' , ' + result.data[i]['min_bet'] + ' ,  ' + result.data[i]['max_bet'] + ' , ' + result.data[i]['win_rate'] + ' , ' + result.data[i]['take_rate'] + ')"><i class="fa fa-edit"></i></button> \
                                    <button type="button" style="width: 40px;" data-type="confirm" class="btn btn-danger mx-1" title="Delete" onclick="remove(' + result.data[i]['id'] + ')"><i class="fa fa-trash-o"></i></button>';
                        if(result.data[i]['status'] == '1')
                            html += '<button type="button" style="width: 40px;" class="btn btn-default" title="Disable" onclick="disable(' + result.data[i]['id'] + ')"><i class="fa fa-lock"></i></button>';
                        else
                            html += '<button type="button" style="width: 40px;" class="btn btn-primary" title="Enable" onclick="enable(' + result.data[i]['id'] + ')"><i class="fa fa-unlock"></i></button>';
                        html +=   '</div></td> \
                            </tr>'
                    }
                    lastID = parseInt(lastID) + parseInt(result.data.length);
                    html += '<tr class="load-more" lastID="' + lastID  + '" style="display:none;"><td class="loading_content" style="text-align: center;" colspan="9">' +
                '<img src=\'{{url("assets/img/loading.gif")}}\'/> ' +
                '</td></tr>';
                }
                else {
                    if(lastID == 0)
                        html = '<tr class="tr_row"><td class="loading_content" style="text-align:center;" colspan="9">{{ __("message.table-no-content") }}</td></tr>';
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

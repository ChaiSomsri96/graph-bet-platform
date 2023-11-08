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
                        <div class="mb-micro">アフィリエイトコード</div>
                        <input class="form-control w200" placeholder="ex: Qb_lbagc001" id="aff_code" />
                    </div>
                    <div class="search_section">
                        <div class="mb-micro">コード発行アカウント</div>
                        <input class="form-control w200" placeholder="ex: nVGLT5as" id="username" />
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
                <h2>アフィリエイトコードリスト</h2>
            </div>
            <div class="body">
                <div class="table-responsive">
                    <table class="table center-aligned-table text-xs">
                        <thead>
                            <tr>
                                <th>アフィリエイトコード</th>
                                <th>総入金額</th>
                                <th>総出金額</th>
                                <th>決定された報酬金額</th>
                                <th>払い出し済み金額</th>
                                <th>コード発行アカウント</th>
                                <th>報酬%</th>
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
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <form method="POST" action="" id="affiliate_form">
                @csrf
                <div class="modal-header">
                    <h6 class="title" id="defaultModalLabel">
                        アフィリエトボーナス設定
                    </h6>
                </div>
                <div class="modal-body">
                        <input type="hidden" id="data_id" value="" />
                        <div class="row clearfix">
                            <div class="col-md-12">
                                <div class="mb-2">
                                    報酬%設定
                                </div>
                                <div class="flex">
                                    <div>
                                        <input class="form-control wfull" name="aff_pros" id="aff_pros" data-validation="required" />
                                    </div>
                                    <div class="mt-2 ml-2"> % </div>
                                </div>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">設定</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">閉じる</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let noNecessaryServerPost = false;
let aff_code = '', username = '';
setInputFilter(document.getElementById('aff_pros'), function(value) {
            return /^\d*\.?\d*$/.test(value); // Allow digits and '.' only, using a RegExp
})
$('#affiliate_form').on('submit', function(e) {
    e.preventDefault();
    if($('#aff_pros').val() == '')
        return;
    if( !($('#aff_pros').val() > 0 && $('#aff_pros').val() <= 100)) {
        showToast('error', 'ボーナス%値の範囲は1から100の間です。');
        return;
    }
    let params = {};
    params['_token'] = "{{ csrf_token() }}";
    params['AFF_PROS'] = $('#aff_pros').val();
    params['ID'] = $('#data_id').val();
    sendRequest("{{ url('/affiliates/set_bonus') }}", params, $('#add-new-modal'), 2)
    .then((result) => {
        $('#add-new-modal').modal('hide');
        swal("設定済み!", "ボーナス%数が設定されました。", "success");
        getData(0);
        $('#aff_pros').val('');
    })
    .catch(() => {
    });
});
$('#search_button').on('click', function(e) {
    aff_code = $('#aff_code').val();
    username = $('#username').val();
    getData(0);
})
function detail(id) {
    location.href="{{url('/')}}/affiliates/history/" + id;
}
function changePros(id , pros) {
    $('#data_id').val(id);
    $('#aff_pros').val(pros);
    $('#add-new-modal').modal('show');
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
    params['aff_code'] = aff_code;
    $.ajax(  { url: "{{ url('') }}/affiliates/get_list",
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
                                    <td class="font-bold text-info">' + result.data[i]['aff_code'] + '</td> \
                                    <td>' + result.data[i]['total_deposit'] + ' btc</td> \
                                    <td>' + result.data[i]['total_withdraw'] + ' btc</td> \
                                    <td>' + result.data[i]['bonus_sum'] + ' btc</td> \
                                    <td><a class="font-bold text-info" href="{{url('/')}}/affiliates/history/' + result.data[i]['id'] + '">' + result.data[i]['bonus_withdraw'] + ' btc</a></td> \
                                    <td>' + result.data[i]['username'] + '</td> \
                                    <td>' + result.data[i]['aff_pros'] + '%</td> \
                                    <td> \
                                        <button type="button" class="btn btn-primary text-xs" onclick="changePros(' + result.data[i]['id'] + ' , ' + result.data[i]['aff_pros'] + ')">変更</button> \
                                        <button type="button" class="btn btn-danger text-xs" onclick="detail(' + result.data[i]['id'] + ')">詳細</button> \
                                    </td> \
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

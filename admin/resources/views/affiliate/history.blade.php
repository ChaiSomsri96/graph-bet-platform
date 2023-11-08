@extends('layout')
@section('content')
    </div>
</div>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<style>
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
                        <div class="mb-micro">アカウント名</div>
                        <input class="form-control w200" placeholder="ex: nVGLT5as" id="username" />
                    </div>
                    <div class="search_section">
                        <div class="mb-micro">日付</div>
                        <input class="form-control w200" name="daterange" />
                    </div>
                    <button type="button" id="search_button" class="btn btn-primary search_section"><i class="fa fa-search"></i> <span>上記内容で検索</span></button>
                    <button type="button" id="goto_button" class="btn btn-danger search_section"><i class="fa fa-calendar"></i> <span>月別通計</span></button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row clearfix">
    <div class="col-md-12">
        <div class="card">
            <div class="header flex justify-between">
                <h2>{{$data['aff_code']}} 報酬支払履歴</h2>
                <div class="font-bold text-danger">合計 <span id="bonus_sum">0</span> btc</div>
            </div>
            <div class="body">
                <div class="table-responsive">
                    <table class="table center-aligned-table text-xs" id="game_history_table">
                        <thead>
                            <tr>
                                <th>ユーザーID</th>
                                <th>アカウント名</th>
                                <th>日付</th>
                                <th>初回入金額</th>
                                <th>支払い済み報酬額</th>
                                <th>報酬%</th>
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
<script>
let noNecessaryServerPost = false;
let startDate = '', endDate = '', username = '';
let userid = '{{$data['user_id']}}';
startDate = moment().startOf('year').format('YYYY-MM-DD');
endDate = moment().endOf('year').format('YYYY-MM-DD');
$(function() {
    "use strict";
    $('input[name="daterange"]').daterangepicker({
        opens: 'right',
        startDate: moment().startOf('year'),
        endDate: moment().endOf('year'),
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            'This Year': [moment().startOf('year'), moment().endOf('year')],
        }
    }, function(start, end, label) {
        startDate = start.format('YYYY-MM-DD');
        endDate = end.format('YYYY-MM-DD');
    });
});
$('#search_button').on('click', function(e) {
    username = $('#username').val();
    getData(0);
})
$('#goto_button').on('click', function(e) {
    location.href = "{{url('')}}/affiliates/monthly_statistics/{{$data['user_id']}}";
})
function getData(lastID) {
    let params = {
        "_token": "{{ csrf_token() }}"
    };
    if(lastID == 0) {
        noNecessaryServerPost = false;
        $('.table-content').html('<tr class="load-more" lastID="0">' +
                '<td class="loading_content" colspan="6" style="text-align: center;">' +
                    '<img src="{{url('assets/img/loading.gif')}}"/>' +
                '</td>' +
            '</tr>');
    }
    params['id'] = lastID;
    params['userid'] = userid;
    params['startDate'] = startDate;
    params['endDate'] = endDate;
    params['username'] = username;
    $.ajax(  { url: "{{ url('') }}/affiliates/history/get_list",
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
                $('#bonus_sum').html(result.sum);
                if(result.data.length < 1) {
                    noNecessaryServerPost = true;
                    $('.load-more').remove();
                    if(lastID == 0) {
                        html = '<tr class="tr_row"><td class="loading_content" style="text-align:center;" colspan="6">{{ __("message.table-no-content") }}</td></tr>';
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
                                    <td>' + result.data[i]['userid'] + '</td> \
                                    <td class="text-info font-bold">' + result.data[i]['username'] + '</td> \
                                    <td>' + getTimeString(result.data[i]['time']) + '</td> \
                                    <td>' + result.data[i]['first_deposit'] + ' btc</td> \
                                    <td class="font-bold">' + result.data[i]['bonus'] + ' btc</td> \
                                    <td>' + result.data[i]['pros'] + ' %</td> \
                                </tr>';
                    }
                    lastID = parseInt(lastID) + parseInt(result.data.length);
                    html += '<tr class="load-more" lastID="' + lastID  + '" style="display:none;"><td class="loading_content" style="text-align: center;" colspan="6">' +
                            '<img src=\'{{url("assets/img/loading.gif")}}\'/> ' +
                            '</td></tr>';
                }
                else {
                    if(lastID == 0)
                        html = '<tr class="tr_row"><td class="loading_content" style="text-align:center;" colspan="6">{{ __("message.table-no-content") }}</td></tr>';
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

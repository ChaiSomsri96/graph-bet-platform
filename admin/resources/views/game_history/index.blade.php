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
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<div class="row clearfix">
    <div class="col-md-12">
        <div class="card search-box">
            <div class="header">
                <h2>検索条件</h2>
            </div>
            <div class="body">
                <div class="row clearfix items-end">
                    <div class="search_section">
                        <div class="mb-micro">game ID</div>
                        <input class="form-control w200" placeholder="ex: 32493" id="gameid" />
                    </div>
                    <div class="search_section">
                        <div class="mb-micro">game プレイ時間</div>
                        <input class="form-control w200" name="daterange" />
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
                <h2>ユーザーリスト</h2>
            </div>
            <div class="body">
                <div class="table-responsive">
                    <table class="table center-aligned-table text-xs" id="game_history_table">
                        <thead>
                            <tr>
                                <th>game ID</th>
                                <th>game プレイ時間</th>
                                <th>Bust</th>
                                <th>Wagered</th>
                                <th>Profit</th>
                                <th>プレイしたユーザー数</th>
                                <th>ボット数</th>
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

<!-- Large Size -->
<div class="modal fade" id="largeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="title" id="largeModalLabel">Modal title</h4>
            </div>
            <div class="modal-body">
                <div class="row clearfix">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table center-aligned-table text-xs">
                                <thead>
                                    <tr>
                                        <th>ユーザ名</th>
                                        <th>時間</th>
                                        <th>Wagered</th>
                                        <th>Cashout Rate</th>
                                        <th>Profit</th>
                                    </tr>
                                </thead>
                                <tbody class="game-table-content">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">CLOSE</button>
            </div>
        </div>
    </div>
</div>

<script>
let noNecessaryServerPost = false;
let startDate = '', endDate = '', gameid = '';
startDate = moment().startOf('month').format('YYYY-MM-DD');
endDate = moment().endOf('month').format('YYYY-MM-DD');
$(function() {
    "use strict";
    $('input[name="daterange"]').daterangepicker({
        opens: 'right',
        startDate: moment().startOf('month'),
        endDate: moment().endOf('month'),
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, function(start, end, label) {
        startDate = start.format('YYYY-MM-DD');
        endDate = end.format('YYYY-MM-DD');
    });
});
$('#search_button').on('click', function(e) {
    gameid = $('#gameid').val();
    getData(0);
})
function getData(lastID) {
    let params = {
        "_token": "{{ csrf_token() }}"
    };
    if(lastID == 0) {
        noNecessaryServerPost = false;
        $('.table-content').html('<tr class="load-more" lastID="0">' +
                '<td class="loading_content" colspan="7" style="text-align: center;">' +
                    '<img src="{{url('assets/img/loading.gif')}}"/>' +
                '</td>' +
            '</tr>');
    }
    params['id'] = lastID;
    params['startDate'] = startDate;
    params['endDate'] = endDate;
    params['gameid'] = gameid;
    $.ajax(  { url: "{{ url('') }}/game_history/get_list",
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
                        html = '<tr class="tr_row"><td class="loading_content" style="text-align:center;" colspan="7">{{ __("message.table-no-content") }}</td></tr>';
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
                                    <td class="font-bold"><a class="text-info" href="javascript:void(0);" onclick="detailGame(' + result.data[i]['game_id'] + ')">GAME #' + result.data[i]['game_id'] + '</a></td>\
                                    <td>' + getTimeString(result.data[i]['play_time']) + '</td>\
                                    <td>' + result.data[i]['bust'] + '</td>\
                                    <td>' + result.data[i]['wagered'] + ' btc</td>\
                                    <td>' + result.data[i]['profit'] + ' btc</td>\
                                    <td>' + result.data[i]['users'] + ' 名</td>\
                                    <td>' + result.data[i]['bots'] + ' 名</td>\
                                </tr>'
                    }
                    lastID = parseInt(lastID) + parseInt(result.data.length);
                    html += '<tr class="load-more" lastID="' + lastID  + '" style="display:none;"><td class="loading_content" style="text-align: center;" colspan="7">' +
                            '<img src=\'{{url("assets/img/loading.gif")}}\'/> ' +
                            '</td></tr>';
                }
                else {
                    if(lastID == 0)
                        html = '<tr class="tr_row"><td class="loading_content" style="text-align:center;" colspan="7">{{ __("message.table-no-content") }}</td></tr>';
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
function detailGame(game_id) {
    let params = {};
    params['_token'] = "{{ csrf_token() }}";
    params['game_id'] = game_id;
    sendRequest("{{ url('/game_history/detail') }}", params, $('#game_history_table'), 2)
    .then((result) => {
        $('#largeModalLabel').html('GAME #' + game_id);
        let html = '';
        if(result.data.length > 0) {
            for(let i = 0; i < result.data.length; i ++) {
                html += '<tr> \
                                    <td class="font-bold text-info">' + result.data[i]['username'] + '</td> \
                                    <td>' + getTimeString(result.data[i]['time']) + '</td>\
                                    <td>' + result.data[i]['wagered'] + ' btc</td> \
                                    <td class="font-bold">' + result.data[i]['cashout'] + '</td> \
                                    <td class="font-bold">' + result.data[i]['profit'] + '</td> \
                        </tr>';
            }
        }
        else {
            html = '<tr class="tr_row"><td class="loading_content" style="text-align:center;" colspan="5">{{ __("message.table-no-content") }}</td></tr>';
        }
        $('.game-table-content').html(html);
        $('#largeModal').modal('show');
    })
    .catch(() => {
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

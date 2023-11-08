@extends('layout')
@section('content')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
        <div class="col-lg-7 col-md-12 col-sm-12 flex items-center justify-end">
            <div class="mr-2">
                日付範囲選択
            </div>
            <input type="text" name="daterange" class="form-control w200" />
        </div>
    </div>
</div>
<link rel="stylesheet" href="{{URL('/assets/vendor/morrisjs/morris.min.css')}}" />
<style>
    .statistics .card {
        margin-bottom: 15px;
    }
    .card .body:nth-child(1) {
        padding-bottom: 10px;
    }
    .card .body:nth-child(2) {
        padding-top: 10px;
    }
    .number span, .margin-0 span{
        font-size: 14px;
    }
</style>
<div class="row clearfix">
    <div class="col-lg-3 col-md-12 col-sm-12">
        <div class="row clearfix statistics">
            <div class="col-lg-12 col-md-6">
                <div class="card top_counter">
                    <div class="body">
                        <div class="icon"><i class="fa fa-user"></i> </div>
                        <div class="content">
                            <div class="text">プレイ中のユーザー</div>
                            <h5 class="number" id="total_playing_count">10</h5>
                        </div>
                    </div>
                    <div class="body">
                        <div class="icon"><i class="fa fa-user-md"></i> </div>
                        <div class="content">
                            <div class="text">新規登録ユーザー</div>
                            <h5 class="number" id="new_user_cnt">0</h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 col-md-6">
                <div class="card top_counter">
                    <div class="body">
                        <div class="icon"><i class="fa fa-money"></i> </div>
                        <div class="content">
                            <div class="text">入金通計</div>
                            <h5 class="number" id="total_input_sum">0.00000000 <span>btc</span></h5>
                        </div>
                    </div>
                    <div class="body">
                        <div class="icon"><i class="fa fa-external-link"></i> </div>
                        <div class="content">
                            <div class="text">出金通計</div>
                            <h5 class="number" id="total_output_sum">0.00000000 <span>btc</span></h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 col-md-12">
                <div class="card top_counter">
                    <div class="body">
                        <div class="icon"><i class="fa fa-university"></i> </div>
                        <div class="content">
                            <div class="text">Profit 通計</div>
                            <h5 class="number" id="total_profit_sum">0.00000000 <span>btc</span></h5>
                        </div>
                    </div>
                    <div class="body">
                        <div class="icon"><i class="fa fa-suitcase"></i> </div>
                        <a href="{{url('/withdraws')}}">
                            <div class="content">
                                <div class="text">出金申請数</div>
                                <h5 class="number" id="output_request_count">0</h5>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-9 col-md-12 col-sm-12">
        <div class="card">
            <div class="header">
                <h2>入金/出金/利潤統計</h2>
            </div>
            <div class="body">
                <div id="area_chart" class="graph m-b-20"></div>
                <div class="row text-center">
                    <div class="col-sm-3 col-6">
                        <h5 class="margin-0" id="total_today_profit_sum">0.00000000 <span>btc</span></h5>
                        <p class="text-muted margin-0"> 今日の利益 </p>
                    </div>
                    <div class="col-sm-3 col-6">
                        <h5 class="margin-0" id="total_this_week_profit_sum">0.00000000 <span>btc</span></h5>
                        <p class="text-muted margin-0">今週の利益</p>
                    </div>
                    <div class="col-sm-3 col-6">
                        <h5 class="margin-0" id="total_this_month_profit_sum">0.00000000 <span>btc</span></h5>
                        <p class="text-muted margin-0">今月の利益</p>
                    </div>
                    <div class="col-sm-3 col-6">
                        <h5 class="margin-0" id="total_this_year_profit_sum">0.00000000 <span>btc</span></h5>
                        <p class="text-muted margin-0">今年の利益</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row clearfix">
    <div class="col-lg-6 col-md-12 col-sm-12">
        <div class="card">
            <div class="header">
                <h2>アフィリエイト統計</h2>
            </div>
            <div class="body">
                <div class="table-responsive table_middel">
                    <table class="table m-b-0">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>アフィエイトコード 成果</th>
                                <th>登録数</th>
                                <th>入金数</th>
                                <th>入金額</th>
                            </tr>
                        </thead>
                        <tbody id="table_content">
                            <tr>
                                <td colspan="5" style="text-align: center;">
                                    {{ __("message.table-no-content") }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{{URL('/assets/bundles/morrisscripts.bundle.js')}}"></script><!-- Morris Plugin Js -->
<script>
let startDate = '', endDate = '';
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
            'This year': [moment().startOf('year'), moment().endOf('year')]
        }
    }, function(start, end, label) {
        startDate = start.format('YYYY-MM-DD');
        endDate = end.format('YYYY-MM-DD');
        getData();
    });
});
function getData() {
    let params = {};
    params['_token'] = "{{ csrf_token() }}";
    params['start_date'] = startDate;
    params['end_date'] = endDate;
    sendRequest("{{ url('/get_dashboard_data') }}", params, $('#main_content_page'), 2)
    .then((result) => {
        // console.log(result);
        $('#new_user_cnt').html(result.data.new_user_cnt);
        $('#total_input_sum').html(result.data.total_input_sum + ' <span>btc</span>');
        $('#total_output_sum').html(result.data.total_output_sum + ' <span>btc</span>');
        $('#total_profit_sum').html(result.data.total_profit_sum + ' <span>btc</span>');
        $('#output_request_count').html(result.data.output_request_count);
        $('#total_today_profit_sum').html(result.data.total_today_profit_sum + ' <span>btc</span>');
        $('#total_this_week_profit_sum').html(result.data.total_this_week_profit_sum + ' <span>btc</span>');
        $('#total_this_month_profit_sum').html(result.data.total_this_month_profit_sum + ' <span>btc</span>');
        $('#total_this_year_profit_sum').html(result.data.total_this_year_profit_sum + ' <span>btc</span>');
        $('#total_playing_count').html(result.data.total_playing_count);
        if(result.data.table_data.length > 0) {
            let html = '';
            for(let i = 0; i < result.data.table_data.length; i ++) {
                html += ' \
                <tr> \
                    <td>' + result.data.table_data[i]['no'] + '</td> \
                    <td><span class="text-info">' + result.data.table_data[i]['affiliate_code'] + '</span></td> \
                    <td>' + result.data.table_data[i]['cnt'] + '</td> \
                    <td>' + result.data.table_data[i]['a_cnt'] + '</td> \
                    <td><span class="badge badge-success">' + result.data.table_data[i]['a_sum'] + ' btc</span></td> \
                </tr> \
                ';
            }
            $('#table_content').html(html);
        }
        MorrisArea(result);
    })
    .catch(() => {
    });
}
getData();
//======
function MorrisArea(result) {
    $('#area_chart').html("");
    let data = [];
    if(result.data.hasOwnProperty('graph_data')) {
        for(let i = 0; i < result.data.graph_data.length; i ++) {
            data.push({ 'period': result.data.graph_data[i]['DATE_FIELD'], '入金': parseFloat(result.data.graph_data[i]['sum1']) / Math.pow(10, 6), '出金': parseFloat(result.data.graph_data[i]['sum2']) / Math.pow(10, 6), '利潤統計': parseFloat(result.data.graph_data[i]['sum3']) / Math.pow(10, 6)})
        }
    }
    else {
        data = [{
                'period': '2019-11',
                '入金': 5.23,
                '出金': 4.32,
                '利潤統計': 2.45
            }, {
                'period': '2019-12',
                '入金': 4.32,
                '出金': 6.59,
                '利潤統計': 2.25
            }, {
                'period': '2020-01',
                '入金': 3.68,
                '出金': 3.19,
                '利潤統計': 7.4895
            }, {
                'period': '2020-02',
                '入金': 9.49,
                '出金': 3.03,
                '利潤統計': 7.384
            }, {
                'period': '2020-03',
                '入金': 3.21,
                '出金': 4.08,
                '利潤統計': 2.106
            }, {
                'period': '2020-04',
                '入金': 2.08,
                '出金': 3.93,
                '利潤統計': 6.82
            }, {
                'period': '2020-05',
                '入金': 6,
                '出金': 3.506,
                '利潤統計': 2.998
            }
        ];
    }
    console.log(data);
    Morris.Area({
        element: 'area_chart',
        data: data,
    lineColors: ['#f56582', '#02b5b2', '#445771'],
    xkey: 'period',
    ykeys: ['入金', '出金', '利潤統計'],
    labels: ['入金', '出金', '利潤統計'],
    pointSize: 2,
    lineWidth: 1,
    resize: true,
    fillOpacity: 0.8,
    behaveLikeLine: true,
    gridLineColor: '#e0e0e0',
    hideHover: 'auto'
    });
}
</script>
@endsection

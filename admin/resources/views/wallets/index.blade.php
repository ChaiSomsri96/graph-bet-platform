@extends('layout')
@section('content')
    </div>
</div>
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<div class="row clearfix">
    <div class="col-md-12">
        <div class="card">
            <div class="header">
                <h2>対象年月に従うWallet状態</h2>
            </div>
            <div class="body">
                <form method="POST" action="" id="date_form">
                    @csrf
                    <input type="hidden" name="yearmonth" id="yearmonth" value="" />
                </form>
                <div class="flex items-center mb-4 text-base">
                    <div class="mr-2">
                        対象年月
                    </div>
                    <select class="form-control max-w-200" id="date" name="date">
                        <option value="">全期間</option>
                        @foreach ($data['date_list'] as $item)
                            <option value="{{ $item['value'] }}">{{ $item['text'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="table-responsive table_middel">
                    <table class="table m-b-0">
                        <thead class="thead-dark">
                            <tr>
                                <th>総入金額</th>
                                <th>総出金額</th>
                                <th>払い出し報酬金額</th>
                                <th>払い出し予定報酬金額</th>
                                <th>利益合計</th>
                                <th>残高</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="font-bold">
                                <td>{{ $data['total_input_sum'] }} btc</td>
                                <td>{{ $data['total_output_sum'] }} btc </td>
                                <td style="color: #c72837;">{{ $data['total_bonus_sum'] }} btc </td>
                                <td>2.48675 btc </td>
                                <td>{{ $data['total_profit_sum'] }} btc</td>
                                <td>{{ $data['total_wallet_sum'] }} btc </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script>
    $('#date').val('{{ $data["yearmonth"] }}');
    $('#date').select2({
    });
    $('#date').on('change', function(e) {
        $('#yearmonth').val($('#date').val());
        $('#date_form').submit();
    });
</script>
@endsection


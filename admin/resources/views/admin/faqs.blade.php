@extends('layout')
@section('content')
    </div>
</div>
<link rel="stylesheet" href="{{ URL('/assets/vendor/sweetalert/sweetalert.css') }}" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<script src="{{url('/assets/vendor/sweetalert/sweetalert.min.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<div class="row clearfix">
    <div class="col-md-12">
        <div class="card">
            <div class="header" style="padding-bottom: 10px;">
                <h2>FAQ 一覧</h2>
                <button type="button" class="btn btn-primary mt-4" id="add-new-faq">新たに追加</button>
            </div>
            <div class="body">
                <div class="table-responsive">
                    <table class="table center-aligned-table text-xs" id="faq_list">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>カテゴリー名</th>
                                <th>質問</th>
                                <th>現時順</th>
                                <th>最終変更時間</th>
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
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" action="" id="faq_form">
                @csrf
                <div class="modal-header">
                    <h4 class="title" id="defaultModalLabel">FAQ</h4>
                </div>
                <div class="modal-body">
                        <input type="hidden" id="data_id" value="" />
                        <div class="row clearfix">
                            <div class="col-md-12">
                                <table class="text-xs w-full add-new-table" id="bot_form_data">
                                    <tbody>
                                        <tr>
                                            <th>
                                                カテゴリー名
                                                <span class="required-field">
                                                    必須
                                                </span>
                                            </th>
                                            <td>
                                                <select class="form-control w300 text-xs" id="TYPE" name="TYPE">
                                                    @foreach($data['category_list'] as $item)
                                                        <option value="{{$item->ID}}">{{$item->NAME}}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                質問
                                                <span class="required-field">
                                                    必須
                                                </span>
                                            </th>
                                            <td>
                                                <input class="form-control w-full text-xs" name="QUESTION" id="QUESTION" data-validation="required" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                回答
                                                <span class="required-field">
                                                    必須
                                                </span>
                                            </th>
                                            <td>
                                                <textarea class="form-control w-full text-xs" rows="6" id="ANSWER" name="ANSWER" data-validation="required"></textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                現時順
                                                <span class="required-field">
                                                    必須
                                                </span>
                                            </th>
                                            <td>
                                                <input class="form-control w-full text-xs" name="ORDER" id="ORDER" data-validation="required" />
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
$('#add-new-faq').on('click', function(e) {
    $('#QUESTION').val('');
    $('#ORDER').val('');
    $('#ANSWER').val('');
    $('#data_id').val('');
    $('#add-new-modal').modal('show');
});
$('#TYPE').select2({
});
setInputFilter(document.getElementById('ORDER'), function(value) {
            return /^\d*$/.test(value);
});

function remove(id) {
    swal({
        title: "本当ですか?",
        text: "この faq は削除されます。",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#dc3545",
        confirmButtonText: "はい、削除してください!",
        closeOnConfirm: false,
        cancelButtonText: "キャンセル"
    }, function () {
        let params = {};
        params['_token'] = "{{ csrf_token() }}";
        params['id'] = id;
        sendRequest("{{ url('/faqs/remove') }}", params, $('#faq_list'), 2)
        .then((result) => {
            swal("削除!", "Faq が削除されました。", "success");
            getData(0);
        })
        .catch(() => {
        });
    });
}

function edit(id) {
    let params = {};
    params['_token'] = "{{ csrf_token() }}";
    params['id'] = id;
    sendRequest("{{ url('/faqs/detail') }}", params, $('#add-new-modal'), 2)
    .then((result) => {
        $('#QUESTION').val(result.data.QUESTION);
        $('#ORDER').val(result.data.ORDER);
        $('#ANSWER').val(result.data.ANSWER);
        $('#TYPE').val(result.data.TYPE);
        $('#data_id').val(id);
        $('#add-new-modal').modal('show');
    })
    .catch(() => {
    });
}

$('#faq_form').on('submit', function(e) {
    e.preventDefault();
    if($('#QUESTION').val() == '' || $('#ORDER').val() == '' || $('#ANSWER').val() == '')
        return;
    let params = {};
    params['_token'] = "{{ csrf_token() }}";
    params['ID'] = $('#data_id').val();
    params['QUESTION'] = $('#QUESTION').val();
    params['TYPE'] = $('#TYPE').val();
    params['ORDER'] = $('#ORDER').val();
    params['ANSWER'] = $('#ANSWER').val();
    sendRequest("{{ url('/faqs/create') }}", params, $('#add-new-modal'), 2)
    .then((result) => {
        $('#add-new-modal').modal('hide');
        swal("成功!", "Faq が正常に保存されました。", "success");
        getData(0);
        $('#QUESTION').val('');
        $('#ORDER').val('');
        $('#ANSWER').val('');
        $('#data_id').val('');
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
                '<td class="loading_content" colspan="6" style="text-align: center;">' +
                    '<img src="{{url('assets/img/loading.gif')}}"/>' +
                '</td>' +
            '</tr>');
    }
    params['id'] = lastID;
    $.ajax(  { url: "{{ url('') }}/faqs/get_list",
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
                                    <td>' + result.data[i]['id'] + '</td> \
                                    <td>' + result.data[i]['category'] +'</td> \
                                    <td>' + result.data[i]['question'] +'</td> \
                                    <td>' + result.data[i]['order'] +'</td> \
                                    <td>' + getTimeString(result.data[i]['time']) +'</td> \
                                    <td><button type="button" class="btn btn-info" title="Edit" onclick="edit(' + result.data[i]['id'] + ')"><i class="fa fa-edit"></i></button> \
                                    <button type="button" style="width: 40px;" data-type="confirm" class="btn btn-danger mx-1" title="Delete" onclick="remove(' + result.data[i]['id'] + ')"><i class="fa fa-trash-o"></i></button></td> \
                                </tr>'
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

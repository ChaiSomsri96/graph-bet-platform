@extends('layout')
@section('content')
    </div>
</div>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/photoswipe/4.1.0/photoswipe.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/photoswipe/4.1.0/default-skin/default-skin.css">
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
                        <div class="mb-micro">登録日時</div>
                        <input class="form-control w200" name="daterange" />
                    </div>
                    <div class="search_section">
                        <div class="mb-micro">メールアドレス</div>
                        <input class="form-control w200" placeholder="ex: www@gmail.com" id="email" />
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
                <h2>ユーザーリスト<span style="font-size: 12px; color:#dc3545; font-weight: bold;">(Action: 削除/ブロック/リセットパスワード/KYCを見る。)</span></h2>
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
                                <th>初回入金額</th>
                                <th>総入金額</th>
                                <th>出金額</th>
                                <th>達成総量</th>
                                <th>総取引金額</th>
                                <th>残高</th>
                                <th>KYC</th>
                                <th>Action</th>
                                <th>アフィリエイトコード</th>
                                <th>アフィリエイト権限</th>
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
                    <h4 class="title" id="defaultModalLabel">ボット情報</h4>
                </div>
                <div class="modal-body">
                        <input type="hidden" id="data_id" value="" />
                        <div class="row clearfix">
                            <div class="col-md-12">
                                <div class="mb-2">
                                    新しいパスワード
                                </div>
                                <input type="password" class="form-control w-full" name="NEW_PASSWORD" id="NEW_PASSWORD" data-validation="required" />
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">パスワードのリセット</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">閉じる</button>
                </div>
            </form>
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

<div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">
    <!-- Background of PhotoSwipe.
         It's a separate element as animating opacity is faster than rgba(). -->
    <div class="pswp__bg"></div>
    <!-- Slides wrapper with overflow:hidden. -->
    <div class="pswp__scroll-wrap">
        <!-- Container that holds slides.
            PhotoSwipe keeps only 3 of them in the DOM to save memory.
            Don't modify these 3 pswp__item elements, data is added later on. -->
        <div class="pswp__container">
            <div class="pswp__item"></div>
            <div class="pswp__item"></div>
            <div class="pswp__item"></div>
        </div>
        <!-- Default (PhotoSwipeUI_Default) interface on top of sliding area. Can be changed. -->
        <div class="pswp__ui pswp__ui--hidden">
            <div class="pswp__top-bar">
                <!--  Controls are self-explanatory. Order can be changed. -->
                <div class="pswp__counter"></div>
                <button class="pswp__button pswp__button--close" title="Close (Esc)"></button>
                <button class="pswp__button pswp__button--share" title="Share"></button>
                <button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>
                <button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>
                <!-- Preloader demo https://codepen.io/dimsemenov/pen/yyBWoR -->
                <!-- element will get class pswp__preloader--active when preloader is running -->
                <div class="pswp__preloader">
                    <div class="pswp__preloader__icn">
                      <div class="pswp__preloader__cut">
                        <div class="pswp__preloader__donut"></div>
                      </div>
                    </div>
                </div>
            </div>
            <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
                <div class="pswp__share-tooltip"></div>
            </div>
            <button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)">
            </button>
            <button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)">
            </button>
            <div class="pswp__caption">
                <div class="pswp__caption__center"></div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/photoswipe/4.1.0/photoswipe.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/photoswipe/4.1.0/photoswipe-ui-default.min.js"></script>
<script>
'use strict';
var container = [];
/* global jQuery, PhotoSwipe, PhotoSwipeUI_Default, console */
function getImages(id) {
    container = [];
    sendRequest("{{url('/users/get_kyc_images')}}", {"_token": "{{csrf_token()}}", "id": id}, $('#user_list'), 2)
    .then((result) => {
        console.log(result);
        if(result.data.length == 0)
            return;
        for(let i = 0; i < result.data.length; i ++) {
            var item = {
                src: result.data[i],
                w: 1200,
                h: 900,
                title: 'Image Gallery'
            }
            container.push(item);
        }
        var $pswp = $('.pswp')[0],
        options = {
        index: $(this).parent('figure').index(),
        bgOpacity: 0.85,
        showHideOpacity: true
        };
        // Initialize PhotoSwipe
        var gallery = new PhotoSwipe($pswp, PhotoSwipeUI_Default, container, options);
        var flag = [];
        for(let i = 0; i < container.length; i ++) {
            flag.push('0');
        }
        gallery.listen('gettingData', function(index, item) {
                if (flag[index] == '0') { // unknown size
                    flag[index] = '1';
                    var img = new Image();
                    img.onload = function() { // will get size after load
                        item.w = this.width; // set image width
                        item.h = this.height; // set image height
                        gallery.invalidateCurrItems(); // reinit Items
                        gallery.updateSize(true); // reinit Items
                    }
                    img.src = item.src; // let's download image
                }
        });
        gallery.init();
    })
    .catch(() => {
    });
}
</script>
<script>
let noNecessaryServerPost = false;
let startDate = '', endDate = '', username = '', email = '';
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
    email = $('#email').val();
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
function kyc_change(id) {
    $('#data_kyc_id').val(id);
    $('#kyc-modal').modal("show");
}
function remove(id) {
    swal({
        title: "本当ですか?",
        text: "ユーザは永久に削除されます。",
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
        sendRequest("{{ url('/users/change') }}", params, $('#user_list'), 2)
        .then((result) => {
            swal("削除!", "ユーザが削除されました。", "success");
            getData(0);
        })
        .catch(() => {
        });
    });
}
function block(id) {
    swal({
        title: "本当ですか?",
        text: "ユーザーはブロックされ、アカウントが使えなくなります。",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#dc3545",
        confirmButtonText: "はい、ブロックしてください!",
        closeOnConfirm: false,
        cancelButtonText: "キャンセル"
    }, function () {
        let params = {};
        params['_token'] = "{{ csrf_token() }}";
        params['type'] = 2;
        params['id'] = id;
        sendRequest("{{ url('/users/change') }}", params, $('#user_list'), 2)
        .then((result) => {
            swal("変更されました。", "ユーザがブロックされました。", "success");
            getData(0);
        })
        .catch(() => {
        });
    });
}
function unblock(id) {
    let params = {};
    params['_token'] = "{{ csrf_token() }}";
    params['type'] = 3;
    params['id'] = id;
    sendRequest("{{ url('/users/change') }}", params, $('#user_list'), 2)
    .then((result) => {
        swal("変更されました。", "ユーザがブロック解除されました。", "success");
        getData(0);
    })
    .catch(() => {
    });
}
function affiliate_on(id) {
    let params = {};
    params['_token'] = "{{ csrf_token() }}";
    params['type'] = 4;
    params['id'] = id;
    sendRequest("{{ url('/users/change') }}", params, $('#user_list'), 2)
    .then((result) => {
        swal("変更されました。", "アフィリエイトはオンです。", "success");
        getData(0);
    })
    .catch(() => {
    });
}
function affiliate_off(id) {
    let params = {};
    params['_token'] = "{{ csrf_token() }}";
    params['type'] = 5;
    params['id'] = id;
    sendRequest("{{ url('/users/change') }}", params, $('#user_list'), 2)
    .then((result) => {
        swal("変更されました。", "アフィリエイトがオフです。", "success");
        getData(0);
    })
    .catch(() => {
    });
}
function reset_password(id) {
    $('#NEW_PASSWORD').val('');
    $('#data_id').val(id);
    $('#add-new-modal').modal('show');
}
$('#user_form').on('submit', function(e) {
    e.preventDefault();
    if($('#NEW_PASSWORD').val() == '')
        return;
    let params = {};
    params['_token'] = "{{ csrf_token() }}";
    params['NEW_PASSWORD'] = $('#NEW_PASSWORD').val();
    params['ID'] = $('#data_id').val();
    sendRequest("{{ url('/users/reset_password') }}", params, $('#add-new-modal'), 2)
    .then((result) => {
        $('#add-new-modal').modal('hide');
        swal("変更されました。", "ユーザ パスワードがリセットされました。", "success");
        getData(0);
        $('#NEW_PASSWORD').val('');
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
    params['startDate'] = startDate;
    params['endDate'] = endDate;
    params['username'] = username;
    params['email'] = email;
    $.ajax(  { url: "{{ url('') }}/users/get_list",
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
                                <td>' + result.data[i]['first_deposit'] + ' btc</td> \
                                <td>' + result.data[i]['total_deposit'] + ' btc</td> \
                                <td>' + result.data[i]['total_withdraw'] + ' btc</td> \
                                <td>' + result.data[i]['total_affiliate'] + ' btc</td> \
                                <td class="font-bold"><a class="text-info" href="{{url('/users/history/')}}/' + result.data[i]['id'] + '">' + result.data[i]['total_trans_sum'] + ' btc</a></td> \
                                <td>' + result.data[i]['wallet'] + ' btc</td> \
                                <td>' + result.data[i]['kyc'] + '</td> \
                                <td> \
                                    <button onclick="remove(' + result.data[i]['id'] + ')" type="button" class="btn btn-danger" title="削除"><span class="sr-only">削除</span> <i class="fa fa-trash-o"></i></button>';

                        if(result.data[i]['block_yn'] == 'N')
                            html += '<button onclick="block(' + result.data[i]['id'] + ')" type="button" class="btn btn-default mx-1" title="ブロック"><span class="sr-only">ブロック</span> <i class="fa fa-lock"></i></button>';
                        else
                            html += '<button onclick="unblock(' + result.data[i]['id'] + ')" type="button" class="btn btn-primary mx-1" title="ブロックを解除する"><span class="sr-only">ブロックを解除する</span> <i class="fa fa-unlock"></i></button>';

                        html += '<button onclick="reset_password(' + result.data[i]['id'] + ')" type="button" class="btn btn-warning" title="リセットパスワード"><span class="sr-only">リセットパスワード</span> <i class="fa fa-refresh"></i></button> \
                                <button onclick="getImages(' + result.data[i]['id'] + ')" type="button" class="btn btn-success" title="KYCを見る。"><span class="sr-only">KYCを見る。</span> <i class="fa fa-eye"></i></button> \
                                </td> \
                                <td>' + result.data[i]['affiliate_code'] + '</td> \
                                <td>' + result.data[i]['aff_button'] + '</td> \
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

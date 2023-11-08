<style>
.help-block {
    color: rgb(185, 74, 72);
}
</style>
<div class="tab-pane p-l-15 p-r-15" id="setting">
    <form method="POST" action="{{url('/admin/save_setting')}}" id="setting_form">
        <h6>紹介ボーナス%</h6>
        <div>
            <input class="form-control" placeholder="紹介ボーナス%" id="affiliate_fee" data-validation="required" value="{{ isset($setting['affiliate_fee'])?$setting['affiliate_fee']:'' }}"/>
        </div>
        <hr>
        <h6>報酬条件</h6>
        <div class="flex">
            <div>
                <input class="form-control" placeholder="報酬条件" id="transaction_limit" data-validation="required" value="{{ isset($setting['transaction_limit'])?$setting['transaction_limit']:'' }}" />
            </div>
            <div class="mt-2 ml-2">倍</div>
        </div>
        <hr>
        <h6>Tips Fee</h6>
        <div class="flex">
            <div>
                <input class="form-control" placeholder="Tips Fee" id="fee" data-validation="required" value="{{ isset($setting['fee'])?$setting['fee']:'' }}" />
            </div>
            <div class="mt-2 ml-2">bits</div>
        </div>
        <button type="submit" class="btn btn-success mt-4" id="save_setting"><i class="fa fa-check-circle"></i> <span>保存</span></button>
    </form>
</div>
<script>
    setInputFilter(document.getElementById('affiliate_fee'), function(value) {
            return /^\d*\.?\d*$/.test(value); // Allow digits and '.' only, using a RegExp
    })
    setInputFilter(document.getElementById('transaction_limit'), function(value) {
            return /^\d*\.?\d*$/.test(value); // Allow digits and '.' only, using a RegExp
    })
    $('#setting_form').on('submit', function(e) {
        e.preventDefault();
        let params = {};
        params['_token'] = "{{ csrf_token() }}";
        params['affiliate_fee'] = $("#affiliate_fee").val();
        params['transaction_limit'] = $("#transaction_limit").val();
        params['fee'] = $("#fee").val();
        sendRequest("{{ url('/admin/save_setting') }}", params, $('#setting'), 2)
        .then((result) => {
            showToast('success', '{{ __("message.setting-save-success") }}');
        })
        .catch(() => {
        });
    })
</script>

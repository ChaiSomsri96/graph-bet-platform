@extends('layout')
@section('content')
    </div>
</div>
<div class="row clearfix">
    <div class="col-md-12">
        <div class="card">
            <div class="header">
                <h2>ここであなたのパスワードを変更することができます。</h2>
            </div>
            <div class="body">
                <form id="profile-form" method="post" action="{{url('/admin/change_password')}}">
                    <div class="form-group">
                        <label>移転暗号</label>
                        <input type="password" class="form-control" name="old_password" id="old_password" data-validation="required">
                    </div>
                    <div class="form-group">
                        <label>新しい暗号</label>
                        <input type="password" class="form-control" name="new_password" id="new_password" data-validation="required">
                    </div>
                    <div class="form-group">
                        <label>暗号確認</label>
                        <input type="password" class="form-control" name="confirm_password" id="confirm_password" data-validation="required">
                    </div>
                    <br>
                    <button type="submit" class="btn btn-primary" id="change_password">暗号変更</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $('#profile-form').on('submit', function(e) {
        e.preventDefault();
        if($('#old_password').val() == '' || $('#new_password').val() == '' || $('#confirm_password').val() == '') {
            return;
        }
        if($('#new_password').val() != $('#confirm_password').val()) {
            showToast('error', '{{ __("message.correct_new_password") }}');
            return;
        }
        let params = {};
        params['_token'] = "{{ csrf_token() }}";
        params['old_password'] = $('#old_password').val();
        params['new_password'] = $('#new_password').val();
        sendRequest("{{ url('/admin/change_password') }}", params, $('#profile-form'), 2)
        .then((result) => {
            showToast('success', '{{ __("message.success_change_password") }}');
        })
        .catch(() => {
        });
    })
</script>
@endsection

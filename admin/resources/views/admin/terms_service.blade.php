@extends('layout')
@section('content')
    </div>
</div>
<script src="{{URL('/assets/vendor/ckeditor/ckeditor.js')}}"></script> <!-- Ckeditor -->
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <form method="POST" action="{{url('/terms_service/create')}}">
            @csrf
            <div class="card">
                <div class="header" style="padding-bottom: 10px;">
                    <h2> Terms Of Service 作成 </h2>
                    <button type="submit" class="btn btn-primary mt-4" id="edit-btn">保存</button>
                </div>
                <div class="body">
                        <textarea id="ckeditor" name="content">
                            {{ $data['terms_service'] }}
                        </textarea>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
$(function () {
    //CKEditor
    CKEDITOR.replace('ckeditor');
    CKEDITOR.config.height = 300;

});
</script>
@endsection

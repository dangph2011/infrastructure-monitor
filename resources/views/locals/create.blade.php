@extends('layouts.main')
@section('page-header') Tạo máy chủ khu vực
@endsection

@section('contents')
<div class="container-fluid">
    <div class="row">
        <form class="form-horizontal" method="POST" action="/local">
            {{csrf_field()}}
            <div class="container-fluid">
                <div class='col-md-2'>
                </div>
                <div class='col-md-8'>
                    <div class="form-group">
                        <label class='control-label col-sm-4' for="host">IP máy chủ khu vực</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="host" id="host">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class='control-label col-sm-4' for="name">Tên</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="name" id="name">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class='control-label col-sm-4' for="description">Mô tả ngắn gọn</label>
                        <div class="col-sm-8">
                            <textarea class="form-control" name="description" id="description" rows="3"></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class='control-label col-sm-4' for="note">Ghi chú</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="note" id="note">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class='control-label col-sm-4' for="database">Cơ sở dữ liệu</label>
                        <div class="col-sm-8">
                            <select class="form-control" name="database" id="database">
                            <option value=0></option>
                            @foreach($schemas as $schema)
                                <option value="{{ $schema->SCHEMA_NAME }}"> {{ $schema->SCHEMA_NAME }}</option>
                            @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            <br>
            <div style="text-align: center;"><button type="submit" class="btn btn-primary">Lưu</button></div>
        </form>
    </div>
    <br>
    @include('layouts.error')

</div>
@endsection



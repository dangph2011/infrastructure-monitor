@extends('layouts.main')
@section('page-header') Tạo bản sao CSLD
@endsection

@section('contents')
<div class="container-fluid">
    <div class="row">
        <form class="form-horizontal" method="POST" action="/dump">
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
                        <label class='control-label col-sm-4' for="port">Cổng</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="port" id="port" value=3306>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class='control-label col-sm-4' for="user">Tài khoản sao lưu</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="user" id="user">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class='control-label col-sm-4' for="password">Mật khẩu tài khoản sao lưu</label>
                        <div class="col-sm-8">
                            <input type="password" class="form-control" name="password" id="password">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class='control-label col-sm-4' for="database">Tên cơ sở dữ liệu khu vực</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="database" id="database">
                        </div>
                    </div>
                    <div style="text-align: center;"><button type="submit" class="btn btn-primary">Lưu CSDL</button></div>
                </div>
        </form>

    </div>
    <br>
    @include('layouts.error')

</div>
@endsection

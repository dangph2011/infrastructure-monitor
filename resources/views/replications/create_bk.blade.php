@extends('layouts.main')
@section('page-header') Replication
@endsection

@section('contents')
<div class="container-fluid">
    <div class="row">
        <form class="form-horizontal" method="POST" action="/replication">
            {{csrf_field()}}
            <div class="container-fluid">
                <div class='col-md-2'>
                </div>
                <div class='col-md-8'>
                    <div class="form-group">
                        <label class='control-label col-sm-3' for="create_type">Severity</label>
                        <div class="col-sm-9">
                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                <label class="btn btn-default active">
                                        <input type="radio" name="priority_0" id="priority_0">Not Classified
                                    </label>
                                <label class="btn btn-default">
                                        <input type="radio" name="priority_1" id="priority_1">Information
                                    </label>
                                <label class="btn btn-default">
                                        <input type="radio" name="priority_2" id="priority_2">Warning
                                    </label>
                                <label class="btn btn-default">
                                        <input type="radio" name="priority_3" id="priority_3">Average
                                    </label>
                                <label class="btn btn-default">
                                        <input type="radio" name="priority_4" id="priority_4">High
                                    </label>
                                <label class="btn btn-default">
                                        <input type="radio" name="priority_5" id="priority_5">Disaster
                                    </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class='control-label col-sm-4' for="host">IP máy chủ khu vực</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="host" id="host">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class='control-label col-sm-4' for="user">Tài khoản người dùng</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="user" id="user">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class='control-label col-sm-4' for="password">Mật khẩu</label>
                        <div class="col-sm-8">
                            <input type="password" class="form-control" name="password" id="password">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class='control-label col-sm-4' for="client_name">Tên cơ sở dữ liệu khu vực</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="client_name" id="client_name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class='control-label col-sm-4' for="server_name">Tên cơ sở dữ liệu trung tâm</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="server_name" id="server_name">
                        </div>
                    </div>
                    <br>
                    <div style="text-align: center;"><button type="submit" class="btn btn-primary">Save</button></div>
                </div>
        </form>

        </div>
        <br>
    @include('layouts.error')
    </div>
@endsection

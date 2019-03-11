@extends('layouts.main')
@section('page-header') {{__('Create Replication')}}
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
                        <label class='control-label col-sm-3' for="type">{{__('Type')}}</label>
                        <div class="col-sm-9">
                            <div class="btn-group btn-group-toggle" data-toggle="buttons" id="create">
                                <label class="btn btn-default active">
                                    <input type="radio" name="create_0" id="create_0" value="0">{{__('Create New')}}
                                </label>
                                <label class="btn btn-default">
                                    <input type="radio" name="create_1" id="create_1" value="1">{{__('Existed Back up')}}
                                </label>
                            </div>
                        </div>
                    </div>

                    <hr style="border: 2px solid gray;" />
                    <hr>

                    <div class="form-group">
                        <label class='control-label col-sm-4' for="host">{{__('Local Server IP')}}</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="host" id="host">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class='control-label col-sm-4' for="port">{{__('Port')}}</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="port" id="port" value=3306>
                        </div>
                    </div>

                    <div id="create_hidden">
                    <div class="form-group">
                        <label class='control-label col-sm-4' for="user">{{__('Backup User')}}</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="user" id="user">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class='control-label col-sm-4' for="password">{{__('Backup Password')}}</label>
                        <div class="col-sm-8">
                            <input type="password" class="form-control" name="password" id="password">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class='control-label col-sm-4' for="database">{{__('Local Database')}}</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="database" id="database">
                        </div>
                    </div>
                </div>

                    <div class="form-group">
                        <label class='control-label col-sm-4' for="slave_account">{{__('Slave Account')}}</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="slave_account" id="slave_account">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class='control-label col-sm-4' for="slave_password">{{__('Slave Password')}}</label>
                        <div class="col-sm-8">
                            <input type="password" class="form-control" name="slave_password" id="slave_password">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class='control-label col-sm-4' for="channel">{{__('Channel Name')}}</label>
                        <div class="col-sm-8">
                            <input type="input" class="form-control" name="channel" id="channel">
                        </div>
                    </div>

                    <div id="file_name_hidden" style="display: none;">
                    <div class="form-group">
                        <label class='control-label col-sm-4' for="file_name">{{__('Database')}}</label>
                        <div class="col-sm-8">
                            <select class="form-control" name="file_name" id="file_name">
                            @foreach($dumps as $dump)
                                <option value="{{ $dump->id }}"> {{ $dump->filename }}</option>
                            @endforeach
                            </select>
                        </div>
                    </div>
                    </div>

                    <div style="text-align: center;"><button type="submit" class="btn btn-primary">{{__('Save')}}</button></div>
                </div>
        </form>

        </div>
        <br>
    @include('layouts.error')

    </div>

<script>
    $(document).ready(function(){
        $("#create .btn").click(function(){
            if ($(this).find('input').val() == 0) {
                $("#create_hidden").show();
                $("#file_name_hidden").hide();
            } else {
                $("#create_hidden").hide();
                $("#file_name_hidden").show();
            }
        });
    });
</script>
@endsection



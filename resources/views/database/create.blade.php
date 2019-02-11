@extends('layouts.main')
@section('page-header') Database
@endsection

@section('contents')
<div class="container-fluid">
    <div class="row">
        <form class="form-horizontal" method="GET" action="database/create">
            {{csrf_field()}}
            <div class="container-fluid">
                <div class='col-md-2'>
                </div>
                <div class='col-md-8'>
                    <div class="form-group">
                        <label class='control-label col-sm-3' for="description">Host</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="description" id="description">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class='control-label col-sm-3' for="description">User</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="description" id="description">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class='control-label col-sm-3' for="description">Password</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="description" id="description">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class='control-label col-sm-3' for="description">Client Database Name</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="description" id="description">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class='control-label col-sm-3' for="description">Server Database Name</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="description" id="description">
                        </div>
                    </div>
                </div>
        </form>
    </div>
</div>
@endsection

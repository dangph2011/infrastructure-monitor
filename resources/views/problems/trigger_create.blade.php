@extends('layouts.main')
@section('page-header') Trigger Page
@endsection
 {{--
@section('scripts')
<script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
@endsection
 --}}
@section('contents')

<div class="container-fluid">
    <div class="row">
        <form class="form-horizontal" method="GET" action="problems/trigger/create">
            {{csrf_field()}}

            <div class='col-md-2'>
            </div>
            <div class='col-md-8'>
                <div class="form-group">
                    <label class='control-label col-sm-3' for="description">Name</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="description" id="description">
                    </div>
                </div>

                <div class="form-group">
                    <label class='control-label col-sm-3' for="severity">Severity</label>
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
                    <label class='control-label col-sm-3' for="expression">Expression</label>
                    <div class="col-sm-9">
                        <textarea class="form-control" name="expression" id="expression" rows="3"></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label class='control-label col-sm-3' for="recovery_mode">OK event generation
                        </label>
                    <div class="col-sm-9">
                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                            <label class="btn btn-default active">
                                <input type="radio" name="recovery_mode_0" id="recovery_mode_1">Expression
                            </label>
                            <label class="btn btn-default">
                                <input type="radio" name="recovery_mode_1" id="recovery_mode_1">Recovery expression
                            </label>
                            <label class="btn btn-default">
                                <input type="radio" name="recovery_mode_2" id="recovery_mode_2">None
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class='control-label col-sm-3' for="recovery_mode">PROBLEM event generation mode
                            </label>
                    <div class="col-sm-9">
                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                            <label class="btn btn-default active">
                                <input type="radio" name="type_0" id="type_0">Single
                            </label>
                            <label class="btn btn-default">
                                <input type="radio" name="type_1" id="type_1">Multiple
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class='control-label col-sm-3' for="recovery_mode">OK event Closes
                                </label>
                    <div class="col-sm-9">
                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                            <label class="btn btn-default active">
                                <input type="radio" name="correlation_mode_0" id="correlation_mode_0">All problem
                            </label>
                            <label class="btn btn-default">
                                <input type="radio" name="correlation_mode_1" id="correlation_mode_1">All problem if tag values match
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-check-label col-sm-3 text-right">Allow manual close</label>
                    <div class="col-sm-9">
                        <input type="checkbox" class="form-check-input" name="manual_close" id="manual_close" value="1">
                    </div>
                </div>

                <div class="form-group">
                    <label class='control-label col-sm-3' for="url">URL</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="url" id="url">
                    </div>
                </div>

                <div class="form-group">
                    <label class='control-label col-sm-3' for="comments">Description</label>
                    <div class="col-sm-9">
                        <textarea class="form-control" name="comments" id="comments" rows="3"></textarea>
                    </div>
                </div>
                {{-- <div class="col-sm-3"> --}}
                <button type="button" id="add" class="btn btn-primary">Add</button> </span>
                <button type="button" id="cancel" class="btn btn-default">Cancel</button>
                {{-- </div> --}}

                <div class='col-md-2'>
                </div>
        </form>
        </div>
    </div>
@endsection

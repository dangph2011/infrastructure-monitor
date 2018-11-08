@extends('layouts.main')
@section('page-header') Create Graph
@endsection
 {{--
@section('scripts')
<script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
@endsection
 --}}
@section('contents')

<div class="container-fluid">
    <div class="row">
        <form class="form-horizontal" method="GET" action="graphs/create">
            {{csrf_field()}}

            <div class='col-md-3'>
            </div>
            <div class='col-md-6'>
                <div class="form-group">
                    <label class='control-label col-sm-3' for="description">Name</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="description" id="description">
                    </div>
                </div>

                <div class="form-group">
                    <label class='control-label col-sm-3' for="width">Width</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="width" id="width" maxlength="5" style="text-align: right;width: 75px;">
                    </div>
                </div>

                <div class="form-group">
                    <label class='control-label col-sm-3' for="height">Height</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="height" id="height" maxlength="5" style="text-align: right;width: 75px;">
                    </div>
                </div>

                <div class="form-group">
                    <label class='control-label col-sm-3 text-right' for="graph_type">Graph type</label>
                    <div class="col-sm-9">
                        <select class="form-control" name="graph_type" id="graph_type" style="text-align: right;width: 75px;">
                            <option>Line</option>
                            <option>Stacked</option>
                            <option>Pie</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-check-label col-sm-3 text-right">Show Legend</label>
                    <div class="col-sm-9">
                        <input type="checkbox" class="form-check-input" name="show_legend" id="show_legend" value="1">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-check-label col-sm-3 text-right">Show Triggers</label>
                    <div class="col-sm-9">
                        <input type="checkbox" class="form-check-input" name="show_trigger" id="show_trigger" value="1">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-check-label col-sm-3 text-right">Percentile line (left)</label>
                    <div class="col-sm-9">
                        <input type="checkbox" class="form-check-input" name="percent_left" id="percent_left" value="1">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-check-label col-sm-3 text-right">Percentile line (right)</label>
                    <div class="col-sm-9">
                        <input type="checkbox" class="form-check-input" name="percent_right" id="percent_right" value="1">
                    </div>
                </div>


                <div class="form-group">
                    <label class='control-label col-sm-3' for="item">Item</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="item" id="item">
                    </div>
                </div>

                <button type="button" id="add" class="btn btn-primary">Add</button> </span>
                <button type="button" id="cancel" class="btn btn-default">Cancel</button> {{-- </div> --}}

            <div class='col-md-3'>
            </div>
        </form>
    </div>
</div>
@endsection

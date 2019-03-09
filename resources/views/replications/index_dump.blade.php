@extends('layouts.main')
@section('page-header') Replication
@endsection

@section('contents')



<div class="container-fluid">
    <div class="row">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Channel</th>
                    <th>Filename</th>
                    <th>Log file</th>
                    <th>Log pos</th>
                    <th>Log text</th>
                    <th>Created at</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reports as $key => $value)
                <tr>
                    <td class="cell-click" scope="row">{{$key+1}}</td>
                    <td class="cell-click" style="display:none" name="reportid">{{$value->reportid}}</td>
                    <td class="cell-click">{{$value->name}}</td>
                    <td class="cell-click">{{$value->description}}</td>
                    <td class="cell-click">{{$value->created_at}}</td>
                    <td>
                        <a href="{{ route('report.show', $value->reportid) }}" class="btn btn-primary" style="margin-right: 3px;">View</a>
                        <a href="{{ route('report.edit', $value->reportid) }}" class="btn btn-info" style="margin-right: 3px;">Edit</a>
                        <button form="delete-report-form-{{$value->reportid}}" type="submit" class="btn btn-danger" style="margin-right: 3px;">Delete</button>
                        <form id="delete-report-form-{{$value->reportid}}" class="delete-report" action="/report/{{$value->reportid}}" method="POST" onsubmit="return confirm('Do you want to delete this report id: {{$value->reportid}}?');">
                            {{ csrf_field() }}
                            <input type="hidden" name="_method" value="DELETE">
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="row">
        <div style="text-align: center;"><button type="button" onclick="window.location.href='/report/create'" class="btn btn-primary">Create New Report</button></div>
    </div>
</div>

@endsection

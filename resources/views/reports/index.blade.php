@extends('layouts.main')
@section('page-header') Report Page
@endsection

@section('contents')

<div class="container-fluid">
    <div class="row">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Created at</th>
                    <th width="25%">Action</th>
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
                    <form id="delete-report-form-{{$value->reportid}}" class="delete-report" action="/report/{{$value->reportid}}" method="POST" onsubmit="return confirm('Do you want to delete this report: {{$value->reportid}}?');">
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
        <div style="text-align: center;"><button type="button" onclick="window.location.href='/report/create'" class="btn btn-primary">Create</button></div>
    </div>
</div>

<style>
    .cell-click {
        cursor: pointer;
    }
</style>


<script>
    $(document).ready(function($) {
        $('.cell-click').click(function(){
            $reportid = $(this).parent().find('td:nth-child(2)').text();
            window.location.href = '/report/' + $reportid;
        });
    });

    // $('form.delete-report').submit(function(e){
    //     e.preventDefault() // Don't post the form, unless confirmed
    //     if (confirm('Are you sure?')) {
    //         // Post the form
    //         $(e.target).closest('form').submit() // Post the surrounding form
    //     }
    // });
</script>
@endsection

@extends('layouts.main')
@section('page-header') Máy chủ khu vực
@endsection

@section('contents')

<div class="container-fluid">
    <div class="row">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Host</th>
                    <th>Description</th>
                    <th>Database</th>
                    <th>Note</th>
                    <th>Created at</th>
                    <th>Updated at</th>
                    <th width="15%">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($localServers as $key => $value)
                <tr>
                    <td class="cell-click">{{(($localServers->currentPage()-1)*$pagination)+$key+1}}</td>
                    <td class="cell-click" style="display:none" name="localid">{{$value->id}}</td>
                    <td class="cell-click">{{$value->name}}</td>
                    <td class="cell-click">{{$value->host}}</td>
                    <td class="cell-click">{{$value->description}}</td>
                    <td class="cell-click">{{$value->database}}</td>
                    <td class="cell-click">{{$value->note}}</td>
                    <td class="cell-click">{{$value->created_at}}</td>
                    <td class="cell-click">{{$value->updated_at}}</td>
                    <td>
                        {{-- <a href="{{ route('local.show', $value->id) }}" class="btn btn-primary" style="margin-right: 3px;">View</a> --}}
                        <a href="{{ route('local.edit', $value->id) }}" class="btn btn-info" style="margin-right: 3px;">Edit</a>
                        <button form="delete-local-form-{{$value->id}}" type="submit" class="btn btn-danger" style="margin-right: 3px;">Delete</button>
                        <form id="delete-local-form-{{$value->id}}" class="delete-report" action="/local/{{$value->id}}" method="POST" onsubmit="return confirm('Do you want to delete this local server name: {{$value->name}}?');">
                            {{ csrf_field() }}
                            <input type="hidden" name="_method" value="DELETE">
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="text-align: center;"><?php echo $localServers->render(); ?></div>

    {{-- <div class="row">
        <div style="text-align: center;"><button type="button" onclick="window.location.href='/report/create'" class="btn btn-primary">Create New Report</button></div>
    </div> --}}
</div>

<script>
    $(document).ready(function($) {
        $('.cell-click').dblclick(function(){
            $reportid = $(this).parent().find('td:nth-child(2)').text();
            window.location.href = '/local/' + $reportid;
        });
    });
</script>


@endsection

@extends('layouts.main')
@section('page-header') Bản sao CSDL
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
                    <th>Host</th>
                    <th>User</th>
                    <th>Channel</th>
                    <th>State</th>
                    <th>Error</th>
                    <th>Log text</th>
                    <th>Created at</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($repls as $key => $value)
                <tr>
                    <td class="cell-click">{{(($repls->currentPage()-1)*$pagination)+$key+1}}</td>
                    <td class="cell-click">{{$value->name}}</td>
                    <td class="cell-click">{{$value->description}}</td>
                    <td class="cell-click">{{$value->host}}</td>
                    <td class="cell-click">{{$value->user}}</td>
                    <td class="cell-click">{{$value->channel}}</td>
                    <td class="cell-click">{{$value->state}}</td>
                    <td class="cell-click">{{$value->error}}</td>
                    <td class="cell-click">{{$value->log_text}}</td>
                    <td class="cell-click">{{$value->created_at}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div style="text-align: center;"><?php echo $repls->render(); ?></div>
    {{-- <div class="row">
        <div style="text-align: center;"><button type="button" onclick="window.location.href='/report/create'" class="btn btn-primary">Create New Report</button></div>
    </div> --}}
</div>

@endsection

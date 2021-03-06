@extends('layouts.main')
@section('page-header') {{__('Dump Database')}}
@endsection

@section('contents')

<div class="container-fluid">
    <div class="row">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{__('Host')}}</th>
                    <th>{{__('Database')}}</th>
                    <th>{{__('Log File')}}</th>
                    <th>{{__('Log Pos')}}</th>
                    <th>{{__('Filename')}}</th>
                    <th>{{__('Log text')}}</th>
                    <th>{{__('Created at')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($dumps as $key => $value)
                <tr>
                    <td class="cell-click">{{(($dumps->currentPage()-1)*$pagination)+$key+1}}</td>
                    <td class="cell-click">{{$value->host}}</td>
                    <td class="cell-click">{{$value->database}}</td>
                    <td class="cell-click">{{$value->log_file}}</td>
                    <td class="cell-click">{{$value->log_pos}}</td>
                    <td class="cell-click">{{$value->filename}}</td>
                    <td class="cell-click">{{$value->log_text}}</td>
                    <td class="cell-click">{{$value->created_at}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div style="text-align: center;"><?php echo $dumps->render(); ?></div>


    {{-- <div class="row">
        <div style="text-align: center;"><button type="button" onclick="window.location.href='/report/create'" class="btn btn-primary">Create New Report</button></div>
    </div> --}}
</div>

@endsection

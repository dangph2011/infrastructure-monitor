@extends('layouts.main')
@section('page-header') {{__('Replication')}}
@endsection

@section('contents')

<div class="container-fluid">
    <div class="row">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{__('Channel Name')}}</th>
                    <th>{{__('Service State')}}</th>
                    <th>{{__('Last Error Message')}}</th>
                    <th>{{__('Host')}}</th>
                    <th>{{__('Port')}}</th>
                    <th>{{__('Slave User')}}</th>
                    <th>{{__('Created at')}}</th>
                    <th>{{__('Updated at')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($replogs as $key => $value)
                <tr>
                    <td class="cell-click">{{(($replogs->currentPage()-1)*$pagination)+$key+1}}</td>
                    <td class="cell-click">{{$value->CHANNEL_NAME}}</td>
                    <td class="cell-click">{{$value->SERVICE_STATE}}</td>
                    <td class="cell-click">{{$value->LAST_ERROR_MESSAGE}}</td>
                    <td class="cell-click">{{$value->HOST}}</td>
                    <td class="cell-click">{{$value->PORT}}</td>
                    <td class="cell-click">{{$value->USER}}</td>
                    <td class="cell-click">{{$value->created_at}}</td>
                    <td class="cell-click">{{$value->updated_at}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="text-align: center;"><?php echo $replogs->render(); ?></div>

    {{-- <div class="row">
        <div style="text-align: center;"><button type="button" onclick="window.location.href='/report/create'" class="btn btn-primary">Create New Report</button></div>
    </div> --}}
</div>

<script>
    $(document).ready(function($) {
        $('.cell-click').dblclick(function(){
            $reportid = $(this).parent().find('td:nth-child(2)').text();
            window.location.href = '/replication/' + $reportid;
        });
    });
</script>

@endsection

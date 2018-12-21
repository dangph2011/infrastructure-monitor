@extends('layouts.main')
@section('page-header') Report Page
@endsection

@section('contents')

<div class="container-fluid">
    <div class="row">
        <form method="GET" action="/report">
            {{csrf_field()}}
            <div>

                <div class='col-md-4'>
                    <div class="form-group">
                        <label for="">Nhóm</label>
                        <select class="form-control" name="groupid" id="" onchange="this.form.submit()">
                            <option value=0>Tất cả</option>
                            @foreach($groups as $group)
                                {{$rq_groupid}}
                                @if ($group->groupid == $rq_groupid)
                                    <option value="{{ $group->groupid }}" selected> {{ $group->name }}</option>
                                @else
                                <option value="{{ $group->groupid }}" > {{ $group->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class='col-md-4'>
                    <div class="form-group">
                        <label for="">Máy chủ</label>
                        <select class="form-control" name="hostid" id="" onchange="this.form.submit()">
                            <option value=0>Tất cả</option>
                            @foreach($hosts as $host)
                                @if ($host->hostid == $rq_hostid)
                                    <option value="{{ $host->hostid }}" selected> {{ $host->name }}</option>
                                @else
                                    <option value="{{ $host->hostid }}"> {{ $host->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class='col-md-4'>
                    <div class="form-group">
                        <label for="">Báo cáo</label>
                        <select class="form-control" name="graphid" id="" onchange="this.form.submit()">
                            <option value=0>Tất cả</option>
                            <option value=1 selected>CPU máy chủ</option>
                            <option value=2>IN Out máy chủ</option>

                        </select>
                    </div>
                </div>
                {{-- <div class='col-md-4'>
                    <div class="form-group">
                        <label for="">Báo cáo</label>
                        <select class="form-control" name="graphid" id="" onchange="this.form.submit()">
                            <option value=0>Tất cả</option>
                            @foreach($reports as $report)
                                @if ($report->reportid == $rq_reportid)
                                    <option value="{{ $report->reportid }}" selected> {{ $report->name }}</option>
                                @else
                                    <option value="{{ $report->reportid }}"> {{ $report->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div> --}}
            </div>
        </form>


    </div>
</div>
@endsection

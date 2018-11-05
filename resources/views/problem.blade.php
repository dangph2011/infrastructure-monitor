@extends('layouts.main')
@section('page-header') Problem Page
@endsection
 {{--
@section('scripts')
<script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
@endsection
 --}}
@section('contents')

<div class="container-fluid">
    <div class="row">
        <form method="GET" action="/problem">
            {{csrf_field()}}
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
                        <label for="">Show</label>
                        <div class="form-check form-check-inline">
                            <label class="form-check-label">
                                <input onchange='this.form.submit()' class="form-check-input" type="radio" name="show_problems" id="show_problems_3" value="3" {{ $rq_show_problems == '3' ? 'checked' : '' }}>Sự cố
                            </label>
                            <label class="form-check-label">
                                <input onchange='this.form.submit()' class="form-check-input" type="radio" name="show_problems" id="show_problems_2" value="2"  {{ $rq_show_problems == '2' ? 'checked' : '' }}>Tất cả
                            </label>
                        </div>
                    </div>
                </div>
        </form>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>Severity</th>
                    <th>Recovery Time</th>
                    <th>Status</th>
                    {{-- <th>Info</th> --}}
                    <th>Host</th>
                    <th>Problem</th>
                    <th>Duration</th>
                    {{-- <th>Action</th>
                    <th>Tag</th> --}}
                </tr>
            </thead>
            <tbody>
                @foreach($problems as $problem)
                <tr>
                     {{-- Time --}}
                    <td>{{\Carbon\Carbon::createFromTimestamp($problem->clock)}}</td>

                    {{-- Severity --}}
                    <td bgcolor="#{{$config['severity_color_' . $problem->trigger->priority]}}"> {{$config['severity_name_'.$problem->trigger->priority]}} </td>
                    {{-- @if ($problem['r_eventid'] == 0)
                        <td bgcolor="#{{$config['severity_color_' . $problem->trigger->priority]}}"> {{$config['severity_name_'.$problem->trigger->priority]}} </td>
                    @else
                        <td bgcolor="#59db8f"> {{$config['severity_name_'.$problem->trigger->priority]}} </td>
                    @endif --}}

                    {{-- Recovery Time --}}
                    @if ($problem->r_clock == 0)
                        <td></td>
                    @else
                        <td>{{\Carbon\Carbon::createFromTimestamp($problem->r_clock)}}</td>
                    @endif

                    {{-- Status --}}
                    @if ($problem->r_eventid == 0)
                        <td> <span style="color: #{{$config['problem_ack_color']}}">PROBLEM</span> </td>
                    @else
                        <td> <span style="color: #{{$config['ok_ack_color']}}">RESOLVED</span> </td>
                    @endif

                    {{-- Info --}}
                    {{-- Host --}}
                    <td>Zabbix Server</td>

                    {{-- Problem --}}
                    <td>{{$problem->trigger->description}}</td>

                    {{-- Duration --}}
                    @if ($problem->r_eventid == 0)
                        <td>{{\Carbon\Carbon::createFromTimestamp($problem->clock)->diffForHumans(null, true, true, 3)}}</td>
                    @else
                        <td>{{\Carbon\Carbon::createFromTimestamp($problem->clock)->diffForHumans('@'.$problem->r_clock, true, true, 3)}}</td>
                    @endif
                    {{-- Action --}}
                    {{-- Tag --}}
                </tr>
                @endforeach
            </tbody>
        </table>


    </div>
</div>
@endsection

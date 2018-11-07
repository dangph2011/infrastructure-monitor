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
        <form method="GET" action="problems/trigger">
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
                    <label for="">Phân loại</label>
                    <div class="form-check form-check-inline">
                        <label class="form-check-label">
                            <input onchange='this.form.submit()' class="form-check-input" type="radio" name="show_triggers" id="show_triggers_3" value="3" {{ $rq_show_triggers == '3' ? 'checked' : '' }}>Sự cố
                        </label>
                        <label class="form-check-label">
                            <input onchange='this.form.submit()' class="form-check-input" type="radio" name="show_triggers" id="show_triggers_2" value="2"  {{ $rq_show_triggers == '2' ? 'checked' : '' }}>Tất cả
                        </label>
                    </div>
                </div>
            </div>
        </form>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Severity</th>
                    <th>Status</th>
                    <th>Info</th>
                    <th>Time</th>
                    <th>Age</th>
                    <th>Ack</th>
                    <th>Host</th>
                    <th>Name</th>
                    <th>Decription</th>
                </tr>
            </thead>
            <tbody>
                @foreach($triggers as $trigger)
                <tr>
                    {{-- severity  --}}
                    @if ($trigger->value)
                        <td bgcolor="#{{$config['severity_color_' . $trigger->priority]}}"> {{$config['severity_name_'.$trigger->priority]}} </td>
                    @else
                        <td bgcolor="#59db8f"> {{$config['severity_name_'.$trigger->priority]}} </td>
                    @endif

                    {{-- status --}}
                    @if ($trigger->value == TRIGGER_VALUE_TRUE)
                        <td> <span style="color: #{{$config['problem_ack_color']}}">PROBLEM</span> </td>
                    @elseif ($trigger->value == TRIGGER_VALUE_FALSE)
                        <td> <span style="color: #{{$config['ok_ack_color']}}">OK</span> </td>
                    @endif

                    {{-- info --}}
                    @if ($trigger->state == TRIGGER_STATE_UNKNOWN)
                    <td><i class="fa fa-info-circle"></i></i></td>
                    @else
                        <td></td>
                    @endif

                    {{-- time --}}
                    @if ($trigger->lastchange == 0)
                        <td>Never</td>
                    @else
                        <td>{{\Carbon\Carbon::createFromTimestamp($trigger->lastchange)}}</td>
                    @endif

                    {{-- age --}}
                    @if ($trigger->lastchange == 0)
                        <td></td>
                    @else
                        <td>{{\Carbon\Carbon::createFromTimestamp($trigger->lastchange)->diffForHumans(null,true,true, 3)}}</td>
                        {{-- <td>{{convertDate2age($trigger->lastchange)}}</td> --}}
                    @endif
                    {{-- ack --}}
                    <td> </td>
                    {{-- host --}}
                    <td>Zabbix Server</td>

                    {{-- name --}}
                    <td>{{$trigger->description}}</td>

                    {{-- description --}}
                    {{-- <td><a href="/">{{$trigger->comments}}</a></td> --}}
                    <td>{{$trigger->comments}}</td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<style type="text/css">
    input[type="radio"]{margin: 10px 5px};}
 </style>
@endsection

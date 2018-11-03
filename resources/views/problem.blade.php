@extends('layouts.main')
@section('page-header') Problem Page
@endsection

{{-- @section('scripts')
<script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
@endsection --}}

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
        </form>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>Severity</th>
                    <th>Recovery Time</th>
                    <th>Status</th>
                    <th>Info</th>
                    <th>Host</th>
                    <th>Problem</th>
                    <th>Duration</th>
                    <th>Action</th>
                    <th>Tag</th>
                </tr>
            </thead>
            <tbody>
                 {{-- @foreach($users as $user) --}}
                  <tr>
                      {{-- <td> {{$user->id}} </td>
                      <td> {{$user->name}} </td>
                      <td> {{$user->last_name}} </td>
                      <td> {{$user->email}} </td>
                      <td> {{$user->phone}} </td>
                      <td> {{$user->address}} </td>
                      <td> {{$user->id}} </td>
                      <td> {{$user->id}} </td>
                      <td> {{$user->id}} </td>
                      <td> {{$user->id}} </td> --}}
                  </tr>
                 {{-- @endforeach --}}
           </tbody>
        </table>


    </div>
</div>
@endsection

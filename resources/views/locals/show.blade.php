@extends('layouts.main')
@section('page-header') Bản sao CSDL
@endsection

@section('contents')

<div class="container-fluid">
    <div class='col-md-1'>
        </div>
    <div class='col-md-10'class="row">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Column</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($status as $value)
                    @foreach ($value as $k => $v)
                    <tr>
                        <td class="cell-click">{{$k}}</td>
                        <td class="cell-click">{{$v}}</td>
                    </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection

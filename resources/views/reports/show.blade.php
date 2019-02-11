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
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td scope="row">1</td>
                    <td>{{$report->name}}</td>
                    <td>{{$report->description}}</td>
                    <td>{{$report->created_at}}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="row">
        <div style="text-align: center;"><button type="button" onclick="window.location.href='/report/create'" class="btn btn-primary">Create</button></div>
    </div>
</div>
@endsection

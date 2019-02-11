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
                @foreach ($reports as $key => $value)
                <tr>
                    <td scope="row">{{$key+1}}</td>
                    <td>{{$value->name}}</td>
                    <td>{{$value->description}}</td>
                    <td>{{$value->created_at}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="row">
        <div style="text-align: center;"><button type="button" onclick="window.location.href='/report/create'" class="btn btn-primary">Create</button></div>
    </div>
</div>
@endsection

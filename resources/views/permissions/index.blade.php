{{-- \resources\views\permissions\index.blade.php --}}
@extends('layouts.app')
@section('title', '| Permissions')
@section('content')

<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>Available Permissions</h2>
        </div>
        <div class="pull-right">
            <a class="btn btn-success" href="{{ route('permissions.create') }}">Create New Permission</a>
        </div>
        <br/>
    </div>
</div>

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>No</th>
            <th>Name</th>
            <th>Roles</th>
            <th width="280px">Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($permissions as $permission)
        <tr>
            <td>{{ ++$i }}</td>
            <td>{{ $permission->name }}</td>
            <td>
                @if(!empty($permission->roles()->pluck('name')))
                    @foreach($permission->roles()->pluck('name') as $v)
                        <label class="badge badge-success">{{ $v }}</label>
                    @endforeach
                @endif
                </td>
            <td>
                <a class="btn btn-primary" href="{{ route('permissions.edit',$permission->id) }}">Edit</a>
                {!! Form::open(['method'=> 'DELETE', 'route' => ['permissions.destroy', $permission->id], 'style'=>'display:inline', 'onsubmit'=>"return confirm('Do you really want to delete {$permission->name}?');"]) !!}
                {!! Form::submit('Delete', ['class'=> 'btn btn-danger delete']) !!} {!! Form::close() !!}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{!! $permissions->render() !!}

@endsection

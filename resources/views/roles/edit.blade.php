@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                    {{-- {{$user->name}} --}}
                <div class="card-header">{{ __('Edit Role') }}</div>
                <div class="card-body">
                    {!! Form::model($role, ['method' => 'PUT','route' => ['roles.update', $role->id]]) !!}
                    @csrf

                    <div class="form-group row">
                        <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

                        <div class="col-md-6">
                            <input id="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{$role->name}}" required autofocus>

                            @if ($errors->has('name'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="roles" class="col-md-4 col-form-label text-md-right">{{ __('Permission') }}</label>
                        <div class="col-md-6">
                            <div class='form-group'>
                                @foreach ($permissions as $permission)
                                    {{Form::checkbox('permissions[]',  $permission->id) }}
                                    {{Form::label($permission->name, ucfirst($permission->name)) }}<br>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="form-group row mb-0">
                        <div class="col-md-6 offset-md-4">
                            <button type="submit" class="btn btn-primary">
                                {{ __('Submit') }}
                            </button>
                        </div>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

@endsection


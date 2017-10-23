@extends("SimpleMenu::admin.$css_fw.shared")
@section('title', "Edit '$user->name'")

@section('sub')
    <div class="level">
        <div class="level-left">
            <h3 class="title">
                <a href="{{ url()->previous() }}">{{ trans('SimpleMenu::messages.go_back') }}</a>
            </h3>
        </div>
        <div class="level-right">
            <a href="{{ route($crud_prefix.'.users.create') }}"
                class="button is-success">
                @lang('SimpleMenu::messages.app_add_new')
            </a>
        </div>
    </div>

    {{ Form::model($user, ['method' => 'PUT', 'route' => [$crud_prefix.'.users.update', $user->id]]) }}

        {{-- Account --}}
        <div class="columns">
            <div class="column is-2">
                <h3 class="title">Account</h3>
            </div>
            <div class="column is-10">
                {{-- name --}}
                <div class="field">
                    {{ Form::label('name', 'Name', ['class' => 'label']) }}
                    <div class="control">
                        {{ Form::text('name', $user->name, ['class' => 'input']) }}
                    </div>
                    @if($errors->has('name'))
                        <p class="help is-danger">
                            {{ $errors->first('name') }}
                        </p>
                    @endif
                </div>

                {{-- email --}}
                <div class="field">
                    {{ Form::label('email', 'Email', ['class' => 'label']) }}
                    <div class="control">
                        {{ Form::email('email', $user->email, ['class' => 'input']) }}
                    </div>
                    @if($errors->has('email'))
                        <p class="help is-danger">
                            {{ $errors->first('email') }}
                        </p>
                    @endif
                </div>

                {{-- password --}}
                <div class="field">
                    {{ Form::label('password', 'Password', ['class' => 'label']) }}
                    <div class="control">
                        {{ Form::password('password', ['class' => 'input', 'placeholder'=>'******']) }}
                    </div>
                    @if($errors->has('password'))
                        <p class="help is-danger">
                            {{ $errors->first('password') }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
        <hr>

        {{-- Guards --}}
        <div class="columns">
            <div class="column is-2">
                <h3 class="title">Guards</h3>
            </div>
            <div class="column is-10">
                {{-- roles --}}
                <div class="field">
                    {{ Form::label('roles', 'Roles', ['class' => 'label']) }}
                    <div class="control">
                        {{ Form::select(
                            'roles[]',
                            $roles,
                            $user->roles->pluck('name', 'name'),
                            ['class' => 'select2', 'multiple' => 'multiple']
                        ) }}
                    </div>
                    @if($errors->has('roles'))
                        <p class="help is-danger">
                            {{ $errors->first('roles') }}
                        </p>
                    @endif
                </div>

                {{-- permissions --}}
                <div class="field">
                    {{ Form::label('permissions', 'Permissions', ['class' => 'label']) }}
                    <div class="control">
                        {{ Form::select(
                            'permissions[]',
                            $permissions,
                            $user->permissions->pluck('name', 'name'),
                            ['class' => 'select2', 'multiple' => 'multiple']
                        ) }}
                    </div>
                    @if($errors->has('permissions'))
                        <p class="help is-danger">
                            {{ $errors->first('permissions') }}
                        </p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="columns">
            <div class="column is-2"></div>
            <div class="column is-2">
                {{ Form::submit(trans('SimpleMenu::messages.app_update'), ['class' => 'button is-warning is-fullwidth']) }}
            </div>
        </div>

    {{ Form::close() }}
@endsection

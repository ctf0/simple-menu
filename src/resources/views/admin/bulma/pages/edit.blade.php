@extends("SimpleMenu::admin.$css_fw.shared")
@section('title')
    Edit "{{ empty($page->title) ? collect($page->getTranslations('title'))->first() : $page->title }}"
@endsection
@php
    $locales = SimpleMenu::AppLocales();
@endphp

@section('sub')
    <div class="level">
        <div class="level-left">
            <h3 class="title">
                <a href="{{ url()->previous() }}">{{ trans('SimpleMenu::messages.go_back') }}</a>
            </h3>
        </div>
        <div class="level-right">
            <a href="{{ route($crud_prefix.'.pages.create') }}"
                class="button is-success">
                @lang('SimpleMenu::messages.app_add_new')
            </a>
        </div>
    </div>

    <page-comp inline-template select-first="{{ LaravelLocalization::getCurrentLocale() }}">
        <div>
            {{ Form::model($page, ['method' => 'PUT', 'route' => [$crud_prefix.'.pages.update', $page->id]]) }}

                {{-- Meta --}}
                <div class="columns">
                    <div class="column is-2">
                        <h3 class="title">Meta Keywords</h3>
                    </div>
                    <div class="column is-10">
                        {{-- key --}}
                        <div class="field">
                            <div class="control input-box">
                                <div class="select toggle-locale">
                                    <select v-model="meta">
                                        @foreach ($locales as $code)
                                            <option value="{{ $code }}">{{ $code }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @foreach ($locales as $code)
                                    <input type="text"
                                        name="meta[{{ $code }}]"
                                        class="input toggle-pad"
                                        v-show="showMeta('{{ $code }}')"
                                        value="{{ $page->getTranslationWithoutFallback('meta',$code) }}"
                                        placeholder="keyword1, etc..">
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <hr>

                {{-- Control --}}
                <div class="columns">
                    <div class="column is-2">
                        <h3 class="title link" @click="toggleControl = !toggleControl">Control</h3>
                    </div>
                    <div class="column is-10" v-show="toggleControl">
                        {{-- action --}}
                        <div class="field">
                            {{ Form::label('action', 'Action', ['class' => 'label']) }}
                            <div class="control">
                                {{ Form::text(
                                    'action',
                                    $page->action,
                                    ['class' => 'input',
                                    'placeholder'=>"SomeController@index"])
                                }}
                            </div>
                        </div>

                        {{-- template --}}
                        <div class="field">
                            {{ Form::label('template', 'Template', ['class' => 'label']) }}
                            <div class="control">
                                {{ Form::text(
                                    'template',
                                    $page->template,
                                    ['class' => 'input','placeholder'=>"hero"])
                                }}
                            </div>
                            @if($errors->has('template'))
                                <p class="help is-danger">
                                    {{ $errors->first('template') }}
                                </p>
                            @endif
                        </div>

                        {{-- route_name --}}
                        <div class="field">
                            {{ Form::label('route_name', 'Route Name', ['class' => 'label']) }}
                            <div class="control">
                                {{ Form::text(
                                    'route_name',
                                    $page->route_name,
                                    ['class' => 'input','placeholder'=>"route-name"])
                                }}
                            </div>
                            @if($errors->has('route_name'))
                                <p class="help is-danger">
                                    {{ $errors->first('route_name') }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
                <hr>

                {{-- Content --}}
                <div class="columns">
                    <div class="column is-2">
                        <h3 class="title link" @click="toggleContent = !toggleContent">Content</h3>
                    </div>
                    <div class="column is-10" v-show="toggleContent">
                        {{-- title --}}
                        <div class="field">
                            {{ Form::label('title', 'Title', ['class' => 'label']) }}
                            <div class="control input-box">
                                <div class="select toggle-locale">
                                    <select v-model="title">
                                        @foreach ($locales as $code)
                                            <option value="{{ $code }}">{{ $code }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @foreach ($locales as $code)
                                    <input type="text" name="title[{{ $code }}]"
                                        class="input toggle-pad"
                                        v-show="showTitle('{{ $code }}')"
                                        value="{{ $page->getTranslationWithoutFallback('title',$code) }}"
                                        placeholder="Some Title">
                                @endforeach
                            </div>
                            @if($errors->has('title'))
                                <p class="help is-danger">
                                    {{ $errors->first('title') }}
                                </p>
                            @endif
                        </div>

                        {{-- body --}}
                        <div class="field">
                            {{ Form::label('body', 'Body', ['class' => 'label']) }}
                            <div class="control input-box">
                                <div class="select toggle-locale">
                                    <select v-model="body">
                                        @foreach ($locales as $code)
                                            <option value="{{ $code }}">{{ $code }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @foreach ($locales as $code)
                                    <textarea id="body-{{ $code }}"
                                        name="body[{{ $code }}]"
                                        class="textarea"
                                        v-show="showBody('{{ $code }}')">
                                        {{ $page->getTranslationWithoutFallback('body',$code) }}
                                    </textarea>
                                @endforeach
                            </div>
                        </div>

                        {{-- desc --}}
                        <div class="field">
                            {{ Form::label('desc', 'Description', ['class' => 'label']) }}
                            <div class="control input-box">
                                <div class="select toggle-locale">
                                    <select v-model="desc">
                                        @foreach ($locales as $code)
                                            <option value="{{ $code }}">{{ $code }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @foreach ($locales as $code)
                                    <textarea id="desc-{{ $code }}"
                                        name="desc[{{ $code }}]"
                                        class="textarea"
                                        v-show="showDesc('{{ $code }}')">
                                        {{ $page->getTranslationWithoutFallback('desc',$code) }}
                                    </textarea>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <hr>

                {{-- Access --}}
                <div class="columns">
                    <div class="column is-2">
                        <h3 class="title link" @click="toggleAccess = !toggleAccess">Access</h3>
                    </div>
                    <div class="column is-10" v-show="toggleAccess">
                        {{-- prefix --}}
                        <div class="field">
                            {{ Form::label('prefix', 'Url Prefix', ['class' => 'label']) }}
                            <div class="control input-box">
                                <div class="select toggle-locale">
                                    <select v-model="prefix">
                                        @foreach ($locales as $code)
                                            <option value="{{ $code }}">{{ $code }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @foreach ($locales as $code)
                                    <input type="text"
                                        name="prefix[{{ $code }}]"
                                        class="input toggle-pad"
                                        v-show="showPrefix('{{ $code }}')"
                                        value="{{ $page->getTranslationWithoutFallback('prefix',$code) }}"
                                        placeholder="abc">
                                @endforeach
                            </div>
                        </div>

                        {{-- url --}}
                        <div class="field">
                            {{ Form::label('url', 'Url', ['class' => 'label']) }}
                            <div class="control input-box">
                                <div class="select toggle-locale">
                                    <select v-model="url">
                                        @foreach ($locales as $code)
                                            <option value="{{ $code }}">{{ $code }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @foreach ($locales as $code)
                                    <input type="text"
                                        name="url[{{ $code }}]"
                                        class="input toggle-pad"
                                        v-show="showUrl('{{ $code }}')"
                                        value="{{ $page->getTranslationWithoutFallback('url',$code) }}"
                                        placeholder="xyz/{someParam}">
                                @endforeach
                            </div>
                            @if($errors->has('url'))
                                <p class="help is-danger">
                                    {{ $errors->first('url') }}
                                </p>
                            @endif
                        </div>

                        {{-- menus --}}
                        <div class="field">
                            {{ Form::label('menus', 'Menus', ['class' => 'label']) }}
                            <div class="control">
                                {{ Form::select(
                                    'menus[]',
                                    $menus,
                                    $page->menus->pluck('id', 'name'),
                                    ['class' => 'select2', 'multiple' => 'multiple'])
                                }}
                            </div>
                        </div>
                    </div>
                </div>
                <hr>

                {{-- Guards --}}
                <div class="columns">
                    <div class="column is-2">
                        <h3 class="title link" @click="toggleGuards = !toggleGuards">Guards</h3>
                    </div>
                    <div class="column is-10" v-show="toggleGuards">
                        {{-- roles --}}
                        <div class="field">
                            {{ Form::label('roles', 'Roles', ['class' => 'label']) }}
                            <div class="control">
                                {{ Form::select(
                                    'roles[]',
                                    $roles,
                                    $page->roles->pluck('name', 'name'),
                                    ['class' => 'select2', 'multiple' => 'multiple'])
                                }}
                            </div>
                        </div>

                        {{-- permissions --}}
                        <div class="field">
                            {{ Form::label('permissions', 'Permissions', ['class' => 'label']) }}
                            <div class="control">
                                {{ Form::select(
                                    'permissions[]',
                                    $permissions,
                                    $page->permissions->pluck('name', 'name'),
                                    ['class' => 'select2', 'multiple' => 'multiple'])
                                }}
                            </div>
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
        </div>
    </page-comp>
@endsection

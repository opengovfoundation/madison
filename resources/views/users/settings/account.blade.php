@extends('users.settings')

@section('settings_content')
    {{ Form::model($user, ['route' => ['users.settings.account.update', $user->id], 'method' => 'put']) }}
        <div class="row">
            <div class="col-md-3">
                {{ Form::mInput('text', 'fname', trans('messages.user.fname'), null, ['required' => '']) }}
            </div>
            <div class="col-md-3">
                {{ Form::mInput('text', 'lname', trans('messages.user.lname'), null, ['required' => '']) }}
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                {{ Form::mInput('email', 'email', trans('messages.user.email'), null, ['required' => ''], trans('messages.user.email_help')) }}
            </div>
        </div>
        <hr>
        <div class="panel-group" id="verification_info" role="tablist">
            <div class="panel panel-default">
                <div class="panel-heading" role="tab">
                    <h3 class="panel-title">
                        <a role="button" data-toggle="collapse" data-parent="#verification_info" href="#info" aria-expanded="false" aria-controls="info" style="display:block;">
                            @lang('messages.user.verification_info')
                        </a>
                    </h3>
                </div>
                <div id="info" class="panel-collapse collapse" role="tabpanel">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                {{ Form::mInput('text', 'address1', trans('messages.info.address1')) }}
                            </div>
                            <div class="col-md-6">
                                {{ Form::mInput('text', 'address2', trans('messages.info.address2')) }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                {{ Form::mInput('text', 'city', trans('messages.info.city')) }}
                            </div>
                            <div class="col-md-4">
                                {{ Form::mInput('text', 'state', trans('messages.info.state')) }}
                            </div>
                            <div class="col-md-4">
                                {{ Form::mInput('text', 'postal_code', trans('messages.info.postal_code')) }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                {{ Form::mInput('tel', 'phone', trans('messages.info.phone')) }}
                                {{ Form::mInput('url', 'url', trans('messages.info.url')) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        {{ Form::mSubmit() }}
    {{ Form::close() }}
@endsection

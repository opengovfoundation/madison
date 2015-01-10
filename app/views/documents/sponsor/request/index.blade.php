@extends('layouts/main')
@section('content')
  <div class="row">
    <div class="col-md-12">
      <ol class="breadcrumb">
        <li><a href="/" target="_self">{{ trans('messages.home') }}</a></li>
        <li><a href="/documents" target="_self">{{ trans('messages.document') }}s</a></li>
        <li class="active">{{ trans('messages.reqindiesponsor') }}</li>
      </ol>
    </div>
  </div>
  <div class="row">
    <div class="content col-md-12">
      <div class="row">
        <div class="col-md-12">
          <h1>{{ trans('messages.reqindiesponsor') }}</h1>
          <p>{{ trans('messages.provideinfoindie') }}</p>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          {{ Form::open(array('url' => 'documents/sponsor/request', 'method'=>'POST' )) }}
          <div class="form-group">
            <label for="address1">{{ trans('messages.address1') }}:</label>
            <input type="text" class="form-control" name="address1" id="address1" placeholder="{{ trans('messages.enteraddress1') }}" value="{{ Auth::user()->address1 }}"/>
          </div>
          <div class="form-group">
            <label for="address2">{{ trans('messages.address2') }}:</label>
            <input type="text" class="form-control" name="address2" id="address2" placeholder="{{ trans('messages.enteraddress2') }}" value="{{ Auth::user()->address2 }}"/>
          </div>
          <div class="form-group">
            <label for="city">{{ trans('messages.city') }}:</label>
            <input type="text" class="form-control" name="city" id="city" placeholder="{{ trans('messages.city') }}" value="{{ Auth::user()->city }}"/>
          </div>
          <div class="form-group">
            <label for="state">{{ trans('messages.state') }}:</label>
            {{ Form::select('state', array('default' => Lang::get('messages.pleaseselect')) + Geography::getUSStates(), !empty(Auth::user()->state) ? Auth::user()->state : 'default', array('class'=>'form-control')) }}
          </div>
          <div class="form-group">
            <label for="postal">{{ trans('messages.postalcode') }}:</label>
            <input type="text" class="form-control" name="postal" id="postal" placeholder="{{ trans('messages.enterpostalcode') }}" value="{{ Auth::user()->postal_code }}"/>
          </div>
          <div class="form-group">
            <label for="phone">{{ trans('messages.contactphone') }}:</label>
            <input type="text" class="form-control" name="phone" id="phone" placeholder="{{ trans('messages.entercontactphone') }}" value="{{ Auth::user()->phone_number }}"/>
          </div>
          <button type="submit" class="btn btn-default">{{ trans('messages.submit') }}</button>
          
          {{ Form::token() }}
          {{ Form::close() }}
        </div>
      </div>
    </div>
  </div>
@endsection
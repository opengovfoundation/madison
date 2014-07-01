@extends('layouts/main')
@section('content')
  <div class="row">
    <div class="col-md-12">
      <ol class="breadcrumb">
        <li><a href="/" target="_self">Home</a></li>
        <li><a href="/documents" target="_self">Documents</a></li>
        <li class="active">Request Independent Sponsor</li>
      </ol>
    </div>
  </div>
  <div class="row">
    <div class="content col-md-12">
      <div class="row">
        <div class="col-md-12">
          <h1>Request Independent Sponsor</h1>
          <p>Please provide the following information to request becoming an Independent Sponsor</p>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          {{ Form::open(array('url' => 'documents/sponsor/request', 'method'=>'POST' )) }}
          <div class="form-group">
            <label for="address1">Address 1:</label>
            <input type="text" class="form-control" name="address1" id="address1" placeholder="Enter Address Line 1" value="{{ Auth::user()->address1 }}"/>
          </div>
          <div class="form-group">
            <label for="address2">Address 2:</label>
            <input type="text" class="form-control" name="address2" id="address2" placeholder="Enter Address Line 2" value="{{ Auth::user()->address2 }}"/>
          </div>
          <div class="form-group">
            <label for="city">City:</label>
            <input type="text" class="form-control" name="city" id="city" placeholder="City" value="{{ Auth::user()->city }}"/>
          </div>
          <div class="form-group">
            <label for="state">State:</label>
            {{ Form::select('state', array('default' => 'Please Select') + Geography::getUSStates(), !empty(Auth::user()->state) ? Auth::user()->state : 'default', array('class'=>'form-control')) }}
          </div>
          <div class="form-group">
            <label for="postal">Postal Code:</label>
            <input type="text" class="form-control" name="postal" id="postal" placeholder="Enter Postal / Zip Code" value="{{ Auth::user()->postal_code }}"/>
          </div>
          <div class="form-group">
            <label for="phone">Contact Phone:</label>
            <input type="text" class="form-control" name="phone" id="phone" placeholder="Enter Contact Phone Number" value="{{ Auth::user()->phone_number }}"/>
          </div>
          <button type="submit" class="btn btn-default">Submit</button>
          
          {{ Form::token() }}
          {{ Form::close() }}
        </div>
      </div>
    </div>
  </div>
@endsection
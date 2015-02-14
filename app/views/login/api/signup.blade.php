{{--
<div class="row">
	<div class="md-col-12">
		<h1>Signup</h1>
	</div>
</div>
--}}
<div class="row">
	<div class="col-md-10 col-md-offset-1">
		{{ Form::open(array('url'=>'api/user/signup', 'method'=>'post')) }}
		<div class="errors"></div>
		<!-- First Name -->
		<div class="form-group">
			{{ Form::label('fname', Lang::get('messages.fname')) . Form::text('fname', Input::old('fname'), array('placeholder'=>Lang::get('messages.fname'), 'class'=>'form-control')) }}
		</div>
		<!-- Last Name -->
		<div class="form-group">
			{{ Form::label('lname', Lang::get('messages.lname')) . Form::text('lname', Input::old('lname'), array('placeholder'=>Lang::get('messages.lname'), 'class'=>'form-control')) }}
		</div>
		<!-- Email -->
		<div class="form-group">
			{{ Form::label('email', 'Email') . Form::text('email', Input::old('email'), array('placeholder'=>'Email', 'class'=>'form-control')) }}
		</div>
		<!-- Password -->
		<div class="form-group">
			{{ Form::label('password', Lang::get('messages.password')) . Form::password('password', array('placeholder'=>Lang::get('messages.password'), 'class'=>'form-control')) }}
		</div>
		<!-- Submit -->
		{{ Form::submit(Lang::get('messages.signup'), array('class'=>'btn btn-default')) }}
		{{ Form::token() . Form::close() }}
	</div>
</div>
<div class="row">
	<div class="col-md-12 social-login-wrapper">
	  <div class="row">
	    <div class="col-md-12">
	      <a href="/user/facebook-login" class="btn social-login-btn facebook-login-btn">
	        <img src="/img/icon-facebook.png" alt="facebook icon" />
	        {{ trans('messages.signupwith') }} Facebook
	      </a>
	    </div> 
	    <div class="col-md-12">
	      <a href="/user/twitter-login" class="btn social-login-btn twitter-login-btn">
	        <img src="/img/icon-twitter.png" alt="twitter icon" />
	        {{ trans('messages.signupwith') }} Twitter
	      </a>
	    </div>
	    <div class="col-md-12">
	      <a href="/user/linkedin-login" class="btn social-login-btn linkedin-login-btn">
	        <img src="/img/icon-linkedin.png" alt="linkedin icon" />
	        {{ trans('messages.signupwith') }} LinkedIn
	      </a>
	    </div>
	  </div>
	</div>
</div>

{{-- 
<div class="row">
	<div class="md-col-12">
		<h1>Login</h1>
	</div>
</div>
--}}

<div class="row">
	<div class="col-md-10 col-md-offset-1">
		{{ Form::open(array('url'=>'api/user/login', 'method'=>'post')) }}
		<div class="errors"></div>
		<!-- Email -->
		<div class="form-group">
			{{ Form::label('email', 'Email') . Form::text('email', Input::old('email'), array('placeholder'=>'Email', 'class'=>'form-control')) }}
		</div>
		<!-- Password -->
		<div class="form-group">
			{{ Form::label('password', 'Password') . Form::password('password', array('placeholder'=>'Password', 'class'=>'form-control')) }}
		</div>
		<!-- Submit -->
		{{ Form::submit('Login', array('class'=>'btn btn-default')) }}
		<a class="forgot-password" href="{{ URL::to('password/remind') }}">Forgot your password?</a>
		{{ Form::token() . Form::close() }}
	</div>
</div>
<div class="row">
	<div class="col-md-12 social-login-wrapper">
	  <div class="row">
	    <div class="col-md-12">
	      <a href="/user/facebook-login" class="btn btn-default social-login-btn facebook-login-btn">
	        <img src="/img/icon-facebook.png" alt="facebook icon" />
	        {{ trans('messages.loginwith') }} Facebook
	      </a>
	    </div> 
	    <div class="col-md-12">
	      <a href="/user/twitter-login" class="btn btn-default social-login-btn twitter-login-btn">
	        <img src="/img/icon-twitter.png" alt="twitter icon" />
	        {{ trans('messages.loginwith') }} Twitter
	      </a>
	    </div>
	    <div class="col-md-12">
	      <a href="/user/linkedin-login" class="btn btn-default social-login-btn linkedin-login-btn">
	        <img src="/img/icon-linkedin.png" alt="linkedin icon" />
	        {{ trans('messages.loginwith') }} LinkedIn
	      </a>
	    </div>
	  </div>
	</div>
</div>

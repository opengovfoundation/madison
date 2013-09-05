@layout('layouts.main')
@section('content')
	<div class="content col-md-12">
		<div class="row">
			<div class="md-col-12">
				<h1>Login</h1>
			</div>
		</div>
		<div class="row">
			<div class="col-md-10 col-md-offset-1">
				{{ Form::open('login', 'post') }}
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
				{{ Form::token() . Form::close() }}
			</div>
		</div>
	</div>
@endsection

<div class="span12">
	<div class="row-fluid">
		<div class="span12">
			<h1>Login</h1>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span2 spacer"></div>
		<div class="content span8">
			<div class="row-fluid">
				<div class="span4 spacer"></div>
				<div class="span4">
					{{ Form::open('login', 'post') }}
					{{ Form::label('email', 'Email') . Form::text('email', Input::old('email'), array('placeholder'=>'Email')) }}
					{{ Form::label('password', 'Password') . Form::password('password', array('placeholder'=>'Password')) }}
					{{ Form::submit('Login', array('class'=>'btn')) }}
					{{ Form::token() . Form::close() }}
				</div>
				<div class="span4 spacer"></div>
			</div>
		</div>
		<div class="span2 spacer"></div>
	</div>
</div>
@endsection
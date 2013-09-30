@layout('layouts/main')

@section('content')
	<h1>Create Account</h1>
	<form method="post" action="signup">
		<div>
			<label for="fname">First Name:</label>
			<input type="text" name="fname" placeholder="First Name" />
		</div>
		<div>
			<label for="lname">Last Name:</label>
			<input type="text" name="lname" placeholder="Last Name"/>
		</div>
		<div>
			<label for="email">Email:</label>
			<input type="email" name="email" placeholder="Email" />
		</div>
		<div>
			<label for="password">Password:</label>
			<input type="password" name="password" placeholder="Password" />
		</div>
		<div>
			<input type="hidden" name="new_user" value="on" />
			<input type="submit" value="Login" />
		</div>
	</form>
@endsection
@layout('layouts/main')
@section('nav')
@parent
@endsection
@section('content')
<h1>Create Account</h1>
<div class="row-fluid well well-large">
	<div class="span12">
		<form method="post" action="signup">
			<table cellpadding="10">
				<tr>
					<th><label for="fname">First Name:</label></th>
					<td><input type="text" name="fname" placeholder="First Name" /></td>
				</tr>
				<tr>
					<th><label for="lname">Last Name:</label></th>
					<td><input type="text" name="lname" placeholder="Last Name"/></td>
				</tr>
				<tr>
					<th><label for="email">Email:</label></th>
					<td><input type="email" name="email" placeholder="Email" /></td>
				</tr>
				<tr>
					<th><label for="password">Password:</label></th>
					<td><input type="password" name="password" placeholder="Password"/></td>
				</tr>
				<tr>
					<td>
						<input type="hidden" name="new_user" value="on"/>
						<input type="submit" value="Login"/>
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>
@endsection
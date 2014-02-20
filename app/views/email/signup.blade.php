<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8" />
	</head>
	<body>
		<h2>Email Confirmation</h2>

		<div>
			Please confirm your email by clicking <a href="{{ url('user/verify/' . $token, $parameters = array(), $secure = null) }}">here</a>
		</div>

	</body>
</html>
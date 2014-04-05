<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8" />
	</head>
	<body>
		<h1>Your feedback was viewed by a sponsor</h1>

		<p>Congratulations!  {{ $sponsor }} has seen your {{ $label }} on <a href="{{ url('docs/' . $slug, $parameters = array(), $secure = null) }}">{{ $title }}<a>:</p>

		<p>
			{{ $text }}
		</p>

		<p>
			What does this mean?  Your contribution to the policymaking process was viewed by {{ $sponsor }}, 
			thanks to Madison.  That's the first step towards government that listens to you
			and works for you, on your terms.
		</p>

		<p>
			What can you do next? Come back to <a href="{{ url('docs/' . $slug, $parameters = array(), $secure = null) }}">{{ $title }}<a> 
			and rejoin the debate on the issues you care about
			most.  Or add your voice to the laws being made right now by your neighbors,
			your elected officials, and you.
		</p>

		<p>Have a question or suggestion for Madison? <a href="mailto:support@mymadison.io">Tell us</a> so we can improve Madison to serve you and your community better.</p>
		<p>Thank you.  We can't wait to see what you do next with Madison.</p>

		<p>The OpenGov Foundation Team</p>
	</body>
</html>



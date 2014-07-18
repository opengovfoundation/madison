<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8" />
	</head>
	<body>

    {{ var_dump($doc, true) }}

		<p>A comment has been added to - {{ $doc['title'] }} ({{ $doc['slug'] }})</p>
    <blockquote>{{ $comment['text'] }}</blockquote>
		
		<p>&ndash; The OpenGov Foundation Team</p>
	</body>
</html>



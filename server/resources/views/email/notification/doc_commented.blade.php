<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8" />
	</head>
	<body>

		<p>A comment has been added to - {{ link_to('docs/' . $doc['slug'], $doc['title']) }}</p>
    <blockquote>{{ $comment['text'] }}</blockquote>

		<p>&ndash; The OpenGov Foundation Team</p>
	</body>
</html>

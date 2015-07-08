<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8" />
	</head>
	<body>

		<p>A document has been edited! - {{ link_to('docs/' . $doc['slug'], $doc['title']) }}</p>

		<p>&ndash; The OpenGov Foundation Team</p>
	</body>
</html>

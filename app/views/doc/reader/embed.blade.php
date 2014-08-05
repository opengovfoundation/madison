@extends('layouts/embed')
@section('content')
<div id="ogDocHeader">
	<div id="ogDocTitle"><h1><a href="{{ $doc->getLink() }}" target="__blank">{{ $doc->title }}</a></h1></div>
	<?php $sponsor = $doc->sponsor()->first(); ?>
	<?php if(!is_null($sponsor)): ?>
	<div id="ogDocSponsor"><strong>Sponsored by </strong><span>{{ $sponsor->getDisplayName() }}</span></div>
	<?php endif; ?>
	<div id="ogDocContent">{{ $doc->get_content('html') }}</div>
</div>
@endsection
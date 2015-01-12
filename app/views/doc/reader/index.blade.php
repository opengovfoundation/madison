@extends('layouts/main')
@section('content')
	@if(Auth::check())
		<script>
			var user = {
				id: {{ Auth::user()->id }},
				email: '{{ Auth::user()->email }}',
				name: '{{ Auth::user()->fname . ' ' . substr(Auth::user()->lname, 0, 1) }}'
			};
		</script>
	@else
		<script>
			var user = {
				id: '',
				email: '',
				name: ''
			}
		</script>
	@endif
	<script>
		var doc = {{ $doc->toJSON() }};
		@if($showAnnotationThanks)
			$.showAnnotationThanks = true;
		@else
			$.showAnnotationThanks = false;
		@endif
	</script>
	{{ HTML::script('js/doc.js') }}

<div class="modal fade" id="annotationThanks" tabindex="-1" role="dialog" aria-labelledby="annotationThanks" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
    </div>
  </div>
</div>
<div ng-controller="DocumentPageController" class="document-wrapper">
	<div class="doc-header row">
		<div class="container doc-info-container" ng-controller="ReaderController" ng-init="init({{ $doc->id }})">
			<div class="doc-info col-md-12">
				<div class="">
					<h1>{{ $doc->title }}</h1>
				</div>
				<div class="doc-sponsor" ng-repeat="sponsor in doc.sponsor">
					<strong>Sponsored by </strong><span>@{{ sponsor.display_name }}</span>
				</div>
				<div class="doc-status" ng-repeat="status in doc.statuses">
					<strong>Status: </strong><span>@{{ status.label }}</span>
				</div>
				<div class="doc-date" ng-repeat="date in doc.dates">
					<strong>@{{ date.label }}: </strong><span>@{{ date.date | parseDate | date:'shortDate' }}</span>
				</div>
				<div class="doc-intro" ng-if="introtext">
					<p><strong>Introduction:</strong></p>
					<div class="markdown" data-ng-bind-html="introtext"></div>
				</div>
				<div class="doc-poll">
					<a id="doc-support" href="#" class="btn btn-default doc-support" ng-click="support(true, $event)" ng-class="{'btn-success': supported}">Support This Document</a>
					<a id="doc-oppose" href="#" class="btn btn-default doc-oppose" ng-click="support(false, $event)" ng-class="{'btn-danger': opposed}">Oppose This Document</a>
				</div>
			</div>
		</div>
	</div>
	<ul class="nav nav-tabs" role="tablist" tourtip="@{{ step_messages.step_3 }}" tourtip-step="3">
		<li ng-class="{'active':secondtab == false}"><a href="#tab-activity" target="_self" role="tab" data-toggle="tab">Bill</a></li>
		<li ng-class="{'active':secondtab == true}"><a href="#tab-discussion" target="_self" role="tab" data-toggle="tab">Discussion</a></li>
		<a href="{{ $doc->slug }}/feed" class="rss-link" target="_self"><img src="/img/rss-fade.png" class="rss-icon" alt="RSS Icon"></a>
	</ul>
	<div class="tab-content doc-tabs">
		<div id="tab-activity" ng-class="{'active':secondtab == false}" class="tab-pane row">
			<div class="col-md-3" id="toc-column">
				<div class="document-toc">
					<div class="toc-container container row affix-elm" data-offset-top="309">
						<div class="col-md-3 toc-content" id="toc">
							<h2>Table of Contents</h2>
							<div ng-controller="DocumentTocController" id="toc-container">
								<ul>
									<li ng-repeat="heading in headings">
										<a class="toc-heading toc-@{{ heading.tag | lowercase }}" href="#@{{ heading.link }}">@{{ heading.title }}</a>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div id="content" class="content doc_content @if(Auth::check())logged_in@endif" tourtip="@{{ step_messages.step_2 }}" tourtip-step="2">
					<div id="doc_content" tourtip="@{{ step_messages.step_4 }}" tourtip-step="4">{{ $doc->get_content('html') }}</div>
				</div>
			</div>
			<div class="col-md-3">
				<!-- Start Introduction GIF -->
				<div class="how-to-annotate" ng-if="!hideIntro">
					<span class="how-to-annotate-close glyphicon glyphicon-remove" ng-click="hideHowToAnnotate()"></span>
					<h2>How to Participate</h2>
					<div class="">
						<img src="/img/how-to-annotate.gif" class="how-to-annotate-img img-responsive" />
					</div>
					<div class="">
						<ol>
							<li>Read the policy document.</li>
							<li>Sign up to add your voice.</li>
							<li>Annotate, Comment, Support or Oppose!</li>
						</ol>
					</div>
				</div>
				<!-- End Introduction GIF -->
				<div ng-controller="AnnotationController" ng-init="init({{ $doc->id }})" class="rightbar participate">
					@include('doc.reader.annotations')
				</div>
			</div>
		</div>

		<div id="tab-discussion" ng-class="{'active': secondtab == true}" class="tab-pane row">
			<div class="col-md-12">
				<div ng-controller="CommentController" ng-init="init({{ $doc->id }})" class="rightbar participate">
					@include('doc.reader.comments')
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

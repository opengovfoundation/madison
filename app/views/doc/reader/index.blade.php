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
				@if($doc->id == 90)
				<div class="doc-intro">
					<p><strong>Introduction:</strong></p>
					<p>You are invited to join more than 30 federal managers  and collaborate on the U.S. Public Participation Playbook before its initial release. Your insights will help ensure it has a solid foundation which other organizations, government agencies and citizens themselves can build upon. It is critical to the success of this resource that it not only addresses the needs of open government, but is designed with open government principles in its DNA.</p>
					<p>All sections of the playbook are under consideration. There are three main sections to each play you can suggest new content for:</p>
					<ol>
						<li>Checklist -- considerations or steps to follow when designing or evaluating a public participation program</li>
						<li>Case Studies -- real world examples that exemplify the play</li>
						<li>Metrics -- suggestions for how to measure the effectiveness of the play</li>
					</ol
					<p>This initial collaborative period will last until December, 17, 2014, and all comments will be reviewed and responded to by the Public Participation Working Group. By January 2015 an edited, formal version of the initial U.S. Public Participation Playbook will be released for piloting by agencies and further, ongoing public contribution. </p>
					<p>Questions or ideas? Email <a href="mailto:justin.herman@gsa.gov" target="_blank">justin.herman@gsa.gov</a></p>
				</div>
				@endif
				<div class="btn-group">
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

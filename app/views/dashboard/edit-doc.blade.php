@extends('layouts/main')
@section('content')
{{ HTML::style('vendor/pagedown/assets/demo.css') }}
{{ HTML::script('vendor/pagedown/assets/Markdown.Editor.js') }}
{{ HTML::script('vendor/pagedown/assets/Markdown.Sanitizer.js') }}
<div class="row">
	<ol class="breadcrumb">
		<li><a href="/dashboard">Dashboard</a></li>
		<li><a href="/dashboard/docs">Documents</a></li>
		<li class="active">{{ $doc->title }}</li>
	</ol>
</div>
<div class="row content" ng-controller="DashboardEditorController" ng-init="init()">
	<div class="col-md-12">
		<div class="row">
			<h2>Document Information</h2>
			<div class="col-md-12">
				<div class="row">
					<h3>Sponsor</h3>
					<input type="hidden" ui-select2="sponsorOptions" ng-model="sponsor" data-placeholder="Select Document Sponsor">


					{{-- 
					<select ui-select2 ng-model="sponsor" data-placeholder="Select Document Sponsor">
						<option value=""></option>
						<option ng-repeat="user in verifiedUsers" value="<% user.id %>"><% user.fname %> <% user.lname %> - <% user.email %></option>
					</select>
					--}}
				</div>
				<div class="row">
					<h3>Categories</h3>
					<input type="hidden" ui-select2="categoryOptions" ng-model="categories" />
				</div>
				
			</div>
		</div>
		<div class="row">
			<h2>Content</h2>
			{{ Form::open(array('url' => 'dashboard/docs/' . $doc->id, 'method' => 'put', 'id'=>'doc_content_form')) }}
				<input type="hidden" name="content_id" value="{{{ $contentItem->id }}}"/>

				<div class="doc_item_content">
					<div id="wmd-button-bar"></div>
					<textarea class="wmd-input" id="wmd-input" name="content"
						>{{{ $contentItem->content }}}</textarea>
					<div id="wmd-preview" class="wmd-panel wmd-preview"></div>
					<script type="text/javascript">
						var doc = {
							id: {{ $doc->id }}
						}

						$(function () {
							var converter1 = Markdown.getSanitizingConverter();
							var editor1 = new Markdown.Editor(converter1);

							editor1.run();
						});
					</script>
				</div>
			{{ Form::hidden('doc_id', $doc->id) }}
			<div class="form_actions">
				{{ Form::submit('Save Doc', array('name' => 'submit', 'id' => 'submit', 'class'=>'btn')) }}
			</div>
			{{ Form::token() . Form::close() }}
			<div id="save_message" class="alert hidden"></div>
		</div>
	</div>
</div>
@endsection
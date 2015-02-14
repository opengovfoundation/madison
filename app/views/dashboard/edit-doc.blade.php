@extends('layouts/main')
@section('content')
<div class="row">
	<ol class="breadcrumb">
		<li><a href="/dashboard" target="_self">{{ trans('messages.dashboard') }}</a></li>
		<li><a href="/dashboard/docs" target="_self">{{ trans('messages.document') }}s</a></li>
		<li class="active">{{ $doc->title }}</li>
	</ol>
</div>
<div class="row content" ng-controller="DashboardEditorController" ng-init="init()">
	<a href="/docs/{{ $doc->slug }}" style="float:right" class="public-link" target="_self"><span class="glyphicon glyphicon-eye-open"></span> Public View</a>
	<div class="col-md-12">
	{{ Form::open(array('url' => 'dashboard/docs/' . $doc->id, 'method' => 'put', 'id'=>'doc_content_form',
		'class' => 'form-horizontal', 'style' => 'style="padding: 0 50px; border: 1px dotted lightgray;"')) }}
		<tabset>
			<tab heading="Document Content">
				<div class="row">
					<input type="hidden" name="content_id" value="{{{ $contentItem->id }}}"/>
					<div class="row">
							<input type="hidden" name="content_id" value="{{{ $contentItem->id }}}" ng-model="doc.content.id"/>

							<div class="doc_item_content">
								<div id="wmd-button-bar"></div>
								<textarea class="wmd-input" id="wmd-input" name="content" ng-model="doc.content.content"
									>{{{ $contentItem->content }}}</textarea>
								<div id="wmd-preview" class="wmd-panel wmd-preview"></div>
							</div>
						{{ Form::hidden('doc_id', $doc->id) }}
						<p><small>Need help converting a Microsoft Word document?  <a href="http://word-to-markdown.herokuapp.com/" target="_blank">There's a tool for that.</a></small></p>
						<p><small>New to Markdown? Check out our <a href="/pdf/MarkdownCheatSheet.pdf" target="_blank">Markdown Cheat Sheet.</a></small></p>
						<div class="form_actions">
							{{ Form::submit('Save Doc', array('name' => 'submit', 'id' => 'submit', 'class'=>'btn')) }}
						</div>
						<div id="save_message" class="alert hidden"></div>
					</div>
					{{ Form::token() . Form::close() }}
				</div>
			</tab>
			<tab heading="Document Information">
				<div class="row">
					<h2>Document Information</h2>
					<div class="col-md-7">
						<form class="form-horizontal">
							<div class="form-group">
								<label for="title" class="col-sm-2 control-label">Title: </label>
								<div class="col-sm-10">
									<input type="text" name="title" id="title" value="{{{ $doc->title }}}" ng-model="doc.title" class="form-control" />
								</div>
							</div>
							<div class="form-group">
								<label for="slug" class="col-sm-2 control-label">Slug: </label>
								<div class="col-sm-10">
									<input type="text" name="slug" id="slug" value="{{{ $doc->slug }}}" ng-model="doc.slug" class="form-control"/>
									<p class="help-block">a-z (lowercase), 0-9, and "-" only.</p>
								</div>
							</div>
							<div class="form-group">
								<label for="status" class="col-sm-2 control-label">Status: </label>
								<div class="col-sm-10 select2-full-width">
									<input name="status" type="hidden" ui-select2="statusOptions" ng-model="status" ng-change="statusChange(status)" data-placeholder="">
								</div>
							</div>
							<div class="form-group">
								<label for="sponsor" class="col-sm-2 control-label">Sponsor: </label>
								<div class="col-sm-10 select2-full-width">
									<input type="hidden" ui-select2="sponsorOptions" ng-model="sponsor" ng-change="sponsorChange(sponsor)" id="sponsor">
								</div>
							</div>
							<div class="form-group">
								<label for="categories" class="col-sm-2 control-label">Categories: </label>
								<div class="col-sm-10 select2-full-width">
									<div name="categories" type="hidden" ui-select2="categoryOptions" ng-model="categories" ng-change="categoriesChange(categories)" multiple ></div>
								</div>
							</div>
						</form>
					</div>
					<div class="col-md-4 col-md-offset-1">
						<div class="row" ng-if="dates.length > 0">
							<strong>Existing Dates:</strong>	
						</div>
						<div class="existing-date row" ng-repeat="date in dates">
							<form class="form-horizontal">
								<div class="form-group no-bottom-margin">
									<div class="col-sm-10">
										<input class="date-label form-control" ng-model="date.label" />
									</div>
									<label class="control-label col-sm-1"><a class="close" ng-click="deleteDate(date)">&times;</a></label>
								</div>
								<div class="dropdown">
									<a class="dropdown-toggle" data-toggle="dropdown" data-target="#">
										@{{ date.date | date:'short' }}
									</a>
									<ul class="dropdown-menu">
										<datetimepicker ng-model="date.date" datetimepicker-config="{dropdownSelector: '.dropdown-toggle' }"></datetimepicker>
									</ul>
								</div>
								<div class="btn btn-info" ng-show="date.$changed" ng-click="saveDate(date)">Update</div>
							</form>
						</div>
						<div class="dates row">
							<form>
								<div class="form-group">
									<div class="new-date col-sm-10">
										<input name="newdate-label" class="form-control" ng-model="newdate.label" type="text" placeholder="Date Label" />
										<datetimepicker ng-model="newdate.date" on-set-time="createDate"></datetimepicker>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</tab>
		</tabset>
	</div>
</div>
@endsection

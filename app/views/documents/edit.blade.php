@extends('layouts/main')
@section('content')


<div class="container">
  <div class="row" ng-controller="DashboardEditorController" ng-init="init()">
    <div class="col-md-12">
      <ol class="breadcrumb">
        <li><a href="/"><i class="icon icon-home"></i> {{ trans('messages.home')}}</a></li>
        <li><a href="/documents" target="_self">{{ trans('messages.document') }}s</a></li>
        <li class="active">{{ $doc->title }}</li>
      </ol>


      <a href="/docs/{{ $doc->slug }}" style="float:right" class="public-link" target="_self"><span class="glyphicon glyphicon-eye-open"></span> {{ trans('messages.publicview') }}</a>
      
      {{ Form::open(array('url' => '/documents/edit/' . $doc->id, 'method' => 'put', 'id'=>'doc_content_form', 'style' => 'style="padding: 0 50px; border: 1px dotted lightgray;"')) }}
        <tabset>
          <tab heading="Document Content">
            <input type="hidden" name="content_id" value="{{{ $contentItem->id }}}"/>
            <input type="hidden" name="content_id" value="{{{ $contentItem->id }}}" ng-model="doc.content.id"/>
            <div class="doc_item_content">
              <div class="row">
                <div class="col-md-6">
                  <div id="wmd-button-bar"></div>
                  <div class="form-group">
                    <textarea class="form-control" id="wmd-input" name="content" rows="20" ng-model="doc.content.content">{{{ $contentItem->content }}}</textarea>
                  </div>
                </div>
                <div class="col-md-6">
                  <div id="wmd-preview" class="wmd-panel wmd-preview"></div>
                </div>
              </div>
            </div>
            {{ Form::hidden('doc_id', $doc->id) }}
            <div class="form_actions">
              {{ Form::submit('Save Doc', array('name' => 'submit', 'id' => 'submit', 'class'=>'btn btn-primary')) }}
            </div>
            <div id="save_message" class="alert hidden"></div>
            {{ Form::token() . Form::close() }}
          </tab>
          <tab heading="Document Information">
            <div class="row">
              <div class="col-md-8">
                <form class="mt">
                  <div class="form-group">
                    <label for="title">{{ trans('messages.title') }}: </label>
                    <input type="text" name="title" id="title" value="{{{ $doc->title }}}" ng-model="doc.title" class="form-control" />
                  </div>
                  <div class="form-group">
                    <label for="slug">Slug: </label>
                    <input type="text" name="slug" id="slug" value="{{{ $doc->slug }}}" ng-model="doc.slug" class="form-control"/>
                    <small class="help-block">a-z (lowercase), 0-9, and "-" only.</small>
                  </div>
                  <div class="form-group">
                    <label for="short-url">{{ trans('messages.shorturl') }}:</label>
                    <button class="btn btn-default" ng-show="!short_url" ng-click="getShortUrl()">{{ trans('messages.getshorturl') }}</button>
                    <input type="text" class="form-control" ng-show="short_url" ng-model="short_url">
                  </div>
                  <div class="form-group">
                    <label for="status">{{ trans('messages.status') }}: </label>
                    <div class="select2-full-width">
                      <input name="status" type="hidden" ui-select2="statusOptions" ng-model="status" ng-change="statusChange(status)">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="sponsor">{{ trans('messages.sponsor') }}: </label>
                    <div class="select2-full-width">
                      <input type="hidden" ui-select2="sponsorOptions" ng-model="sponsor" ng-change="sponsorChange(sponsor)" id="sponsor">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="categories">{{ trans('messages.categories') }}: </label>
                    <div class="select2-full-width">
                      <input type="hidden" ui-select2="categoryOptions" ng-model="categories" ng-change="categoriesChange(categories)" />
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="intro-text">{{ trans('messages.introtext') }}:</label>
                    <textarea class="form-control" rows="10" ng-model="introtext" ng-change="updateIntroText(introtext)"></textarea>
                    <small class="help-block">Markdown Friendly.  Auto-saves 3 seconds after you're done editing.</small>
                  </div>
                </form>
              </div>
              <div class="col-md-4">
                <div class="mt" ng-if="dates.length > 0">
                  <h6>{{ trans('messages.existingdates') }}:</h6> 
                </div>
                <div class="existing-date" ng-repeat="date in dates">
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
                    <div class="btn btn-info" ng-show="date.$changed" ng-click="saveDate(date)">{{ trans('messages.update') }}</div>
                  </form>
                </div>
                <div class="dates mt">
                  <form>
                    <div class="form-group">
                      <div class="new-date">
                        <input name="newdate-label" class="form-control" ng-model="newdate.label" type="text" placeholder="Date Label" />
                        <datetimepicker ng-model="newdate.date" on-set-time="createDate"></datetimepicker>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </tab>
          <tab heading="Embed Document">
            <div class="mt">
              <p>{{ trans('messages.pastetoembed') }}</p>
              <textarea class="form-control" rows="5" cols="80"/>{{ $doc->getEmbedCode() }}</textarea>
            </div>
          </tab>
        </tabset>

    </div>
  </div>
</div>
@endsection

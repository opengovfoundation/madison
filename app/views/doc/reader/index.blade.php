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
<div class="document-wrapper" ng-controller="DocumentPageController">
  <div class="container">

    <div class="row" ng-controller="ReaderController" ng-init="init({{ $doc->id }})">
      <div class="col-md-8">
        <div class="doc-head">
          <h1>{{ $doc->title }}</h1>
          <ul class="list-unstyled">
            <li>
              <small>Publicado: @{{ doc.created_at }}</small>
            </li>
            <li>
              <small>Última Actualización: @{{ doc.updated_at }}</small>
            </li>
          </ul>
          <div class="doc-extract" ng-if="introtext">
            <div class="markdown" data-ng-bind-html="introtext"></div>
          </div>
          <div class="doc-actions">
            <a id="doc-support" href="#" class="btn btn-primary" ng-click="support(true, $event)" ng-class="{'btn-success': supported}">
              <span class="glyphicon glyphicon-ok"></span>
              {{ trans('messages.supportdoc') }}
            </a>
            <a id="doc-oppose" href="#" class="btn btn-default" ng-click="support(false, $event)" ng-class="{'btn-danger': opposed}">
              <span class="glyphicon glyphicon-remove"></span>
              {{ trans('messages.opposedoc') }}
            </a>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-8">
        <ul class="nav nav-tabs" role="tablist" tourtip="@{{ step_messages.step_3 }}" tourtip-step="3">
          <li ng-class="{'active':secondtab == false}"><a href="#tab-activity" target="_self" role="tab" data-toggle="tab">{{ trans('messages.bill') }}</a></li>
          <li ng-class="{'active':secondtab == true}"><a href="#tab-discussion" target="_self" role="tab" data-toggle="tab">{{ trans('messages.discussion') }}</a></li>
          <a href="{{ $doc->slug }}/feed" class="rss-link" target="_self"><img src="/img/rss-fade.png" class="rss-icon" alt="RSS Icon"></a>
        </ul>

        <div class="tab-content">
          <div id="tab-activity" ng-class="{'active':secondtab == false}" class="tab-pane">
            <div id="content" class="@if(Auth::check())logged_in@endif" tourtip="@{{ step_messages.step_2 }}" tourtip-step="2">
              <div id="doc_content" class="doc-content-main" tourtip="@{{ step_messages.step_4 }}" tourtip-step="4">
                {{ $doc->get_content('html') }}
              </div>
            </div>
          </div>

          <div id="tab-discussion" ng-class="{'active': secondtab == true}" class="tab-pane">
            <div class="doc-forum" ng-controller="CommentController" ng-init="init({{ $doc->id }})">
              @include('doc.reader.comments')
            </div>
          </div>
        </div>

      </div>
      <div class="col-md-4">
        <div class="doc-content-sidebar">
          <div class="sidebar-unit">
            <h4>{{ trans('messages.howtoparticipate'); }}</h4>
            <hr class="red">
            <ol>
              <li>{{ trans('messages.readpolicy') }}</li>
              <li>{{ trans('messages.signupnaddvoice') }}</li>
              <li>{{ trans('messages.anncommsuppopp') }}</li>
            </ol>
            <img src="/img/how-to-annotate.gif" class="how-to-annotate-img img-responsive" />
          </div>
          
          <div class="sidebar-unit" ng-controller="DocumentTocController" ng-show="headings.length > 0">
            <h4>{{ trans('messages.tableofcontents') }}</h4>
            <hr class="red">
            <div id="toc-container">
              <ul class="list-unstyled doc-headings-list">
                <li ng-repeat="heading in headings">
                  <a class="toc-heading toc-@{{ heading.tag | lowercase }}" href="#@{{ heading.link }}">@{{ heading.title }}</a>
                </li>
              </ul>
            </div>
          </div>



          <div class="sidebar-unit">
            <h4>{{ trans('messages.annotations') }}</h4>
            <hr class="red">
            <div ng-controller="AnnotationController" ng-init="init({{ $doc->id }})" class="rightbar participate">
              @include('doc.reader.annotations')
            </div>
          </div>

        </div>
      </div>
    </div>


  </div>
</div>
@endsection

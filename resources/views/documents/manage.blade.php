@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1>@lang('messages.document.manage')</h1>
        @include('components.breadcrumbs.document', ['sponsor' => $document->sponsors()->first(), 'document' => $document])
    </div>

    @include('components.errors')

    <div class="row">
        @include('documents.partials.manage-sidebar')

        <div class="col-md-9">
            @yield('manage_content')
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>
            @php // TRANSLATORS: This comment you will see in the .po files
                echo e(__('Hello World!'))
            @endphp
        </h1>

        <p>{{ __('This is a test') }}</p>
    </div>
@endsection

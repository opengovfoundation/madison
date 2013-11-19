@extends('layouts.main')
@section('content')
	<div class="content col-md-12">
		<div class="row">
			<div class="md-col-12">
				<h1>{{ $user->fname . ' ' . substr($user->lname, 0, 1) }}.</h1>
			</div>
		</div>
		<div class="row">
			<div class="col-md-3 col-md-offset-1">
				<img src="http://www.gravatar.com/avatar/{{ md5(strtolower(trim($user->email))) }}?s=200" class="img-rounded img-responsive" alt="" />
				@if(Auth::check() && Auth::user()->id == $user->id)
				<a href="{{ URL::to('user/edit/' . Auth::user()->id) }}" class="red">Edit Profile</a>
				@endif
			</div>
			<div class="col-md-8">
				<div class="row">
					<div class="col-md-12">
						<h2>Suggestions</h2>
						@foreach($user->suggestions as $suggestion)
							<div class="row user-note user-suggestion">
								<div class="col-md-12">
									<a href="{{ URL::to('note/' . $suggestion->id) }}" class="black">{{ StringDiff::diff($suggestion->orig_content, $suggestion->content) }}</a>
								</div>
								<div class="col-md-2">
									<p>{{ $user->fname . ' ' . substr($user->lname, 0, 1) }}.</p>
								</div>
								<div class="col-md-5">
									<p>{{ date('M jS, Y g:ia', strtotime($suggestion->updated_at)) }}</p>
								</div>
								<div class="col-md-3 col-md-offset-2">
									<p>{{ $suggestion->likes}} likes, {{ $suggestion->dislikes }} dislikes</p>
								</div>
							</div>
						@endforeach
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<h2>Comments</h2>
						@foreach($user->comments as $comment)
							<div class="row user-note user-comment">
								<div class="col-md-12">
									<a href="{{ URL::to('note/' . $comment->id) }}" class="black">{{ $comment->content }}</a>
								</div>
								<div class="col-md-2">
									<p>{{ $user->fname . ' ' . substr($user->lname, 0, 1) }}.</p>
								</div>
								<div class="col-md-5">
									<p>{{ date('M jS, Y g:ia', strtotime($comment->updated_at)) }}</p>
								</div>
								<div class="col-md-3 col-md-offset-2">
									<p>{{ $comment->likes}} likes, {{ $comment->dislikes }} dislikes</p>
								</div>
							</div>
						@endforeach
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
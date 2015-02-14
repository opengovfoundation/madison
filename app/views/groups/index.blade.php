@extends('layouts/main')
@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<ol class="breadcrumb">
					<li><a href="/"><i class="icon icon-home"></i> {{ trans('messages.home')}}</a></li>
					<li class="active">{{ trans('messages.yourgroups') }}</li>
				</ol>
				<h2>{{ trans('messages.yourgroups') }}</h2>
				<p>{{ trans('messages.wantcreategroup') }} <a href="/groups/edit">{{ trans('messages.clickhere') }}</a>.
				@if(count($userGroups) <= 0)
				<p>{{ trans('messages.notmembergroup') }}</p>
				@else
				<table class="table table-striped" id="groupsTable">
					<thead>
						<th>{{ trans('messages.displayname') }}</th>
						<th>{{ trans('messages.groupname') }}</th>
						<th>{{ trans('messages.yourrole') }}</th>
						<th>{{ trans('messages.status') }}</th>
					</thead>
					<tbody>
					<?php foreach($userGroups as $groupMember): ?>
					<?php $group = $groupMember->group()->first(); ?>
						<tr>
							<?php if($group->isGroupOwner(Auth::user()->id)): ?>
							<td><a href="/groups/edit/{{ $group->id }}">{{ $group->display_name ? $group->display_name : "N/A" }}</a></td>
							<td><a href="/groups/edit/{{ $group->id }}">{{ $group->name }}</a></td>
							<?php else: ?>
							<td>{{ $group->display_name ? $group->display_name : "N/A" }}</td>
							<td>{{ $group->name }}</td>
							<?php endif; ?>
							<td>{{ $group->findMemberByUserId(Auth::user()->id)->role }}</td>
							<td>{{ $group->status }}</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
				@endif
			</div>

		</div>
	</div>
@endsection
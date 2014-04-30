@extends('layouts/main')
@section('content')
	<div class="row">
		<div class="col-md-3">
			<ol class="breadcrumb">
				<li><a href="/dashboard">Groups</a></li>
				<li class="active">Your Groups</li>
			</ol>
		</div>
	</div>
	<div class="row">
		<div class="content col-md-12">
			<h1>Your Groups</h1>
			<p>Want to create a group? <a href="/groups/edit">Click here</a>.
			@if(count($userGroups) <= 0)
			<p>You are not the member of any groups.</p>
			@else
			<table class="table table-striped" id="groupsTable">
				<thead>
					<th>Display Name</th>
					<th>Group Name</th>
					<th>Your Role</th>
					<th>Status</th>
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
	
@endsection
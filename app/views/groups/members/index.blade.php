@extends('layouts/main')
@section('content')
	<div class="row">
		<div class="col-md-3">
			<ol class="breadcrumb">
				<li><a href="/dashboard">Groups</a></li>
				<li><a href="/groups/edit/{{ $group->id }}">{{ $group->display_name }}</a></li>
				<li class="active">Group Members</li>
			</ol>
		</div>
	</div>
	<div class="row">
		<div class="content col-md-12">
			<h1>Members of '{{ $group->name }}'</h1>
			<?php if($group->status == Group::STATUS_ACTIVE): ?>
			<p><a href="/groups/invite/{{ $group->id }}">Add new member</a></p>
			<?php endif; ?>
			<table class="table table-striped" id="groupsTable">
				<thead>
					<th>Name</th>
					<th>Role</th>
					<th>Joined</th>
					<th>&nbsp;</th>
				</thead>
				<tbody>
				<?php foreach($groupMembers as $member) : ?>
					<tr>
						<td>{{ $member->getUserName() }}</td>
						<td>
						{{ Form::select('role', Group::getRoles(true), $member->role, array('class' => 'memberRoleSelect', 'data-member-id' => $member->id)) }}
						</td>
						<td>{{ $member->created_at }} </td>
						<td><a href="/groups/member/{{ $member->id }}/delete">remove</a></td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
	<script language="javascript">
		$('.memberRoleSelect').change(function() {
			
			var newRole = $(this).val();
			var memberId = $(this).data('member-id');

			$.post('/groups/member/' + memberId + '/role', { role : newRole }, function(data) {

				if(!data.success) {
					alert("There was an error processing your request:\n\n" + data.message);
					location.reload(true);
					return;
				}

				alert(data.message);
				location.reload(true);
				
			}, 'json');
			
		});
	</script>
@endsection
@extends('layouts/main')
@section('content')
	<div class="container">
		<div class="row">
			<ol class="breadcrumb">
				<li><a href="/dashboard">{{ trans('messages.group') }}s</a></li>
				<li><a href="/groups/edit/{{ $group->id }}">{{ $group->display_name }}</a></li>
				<li class="active">{{ trans('messages.groupmembers') }}</li>
			</ol>
			<div class="col-md-12">
				<div class="content">
					<h1>{{ trans('messages.membersof') }} '{{ $group->name }}'</h1>
					<?php if($group->status == Group::STATUS_ACTIVE): ?>
					<p><a href="/groups/invite/{{ $group->id }}">{{ trans('messages.addnewmember') }}</a></p>
					<?php endif; ?>
					<table class="table table-striped" id="groupsTable">
						<thead>
							<th>{{ trans('messages.fname') }}</th>
							<th>{{ trans('messages.role') }}</th>
							<th>{{ trans('messages.joined') }}</th>
							<th>&nbsp;</th>
						</thead>
						<tbody>
						<?php foreach($groupMembers as $member) : ?>
							<tr>
								<td>{{ $member->getUserName() }}</td>
								<td>
								{{ Form::select('role', Group::getRoles(true), $member->role, array('id' => 'memberRoleSelect', 'data-member-id' => $member->id)) }}
								</td>
								<td>{{ $member->created_at }} </td>
								<td><a href="/groups/member/{{ $member->id }}/delete">{{ trans('messages.remove') }}</a></td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<script language="javascript">
		$('#memberRoleSelect').change(function() {
			var newRole = $('select option:selected').val();
			var memberId = $('select').data('member-id');

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
@layout('layouts/main')
@section('content')
<h1 class="text-center">Edit Navigation</h1>
<div class="row-fluid well well-large">
	<div class="span6">
		{{ Form::open('dashboard/nav', 'post', array('id'=>'nav_menu_form')) }}
		<h2>Navigation</h2>
		<ol id="nav_list" class="sortable">
			@if($nav != '')
				@foreach($nav as $navItem)
					<li class="nav_item">
						<div class="sort_handle">
							<span>{{ $navItem['label'] }}</span>
								<input type="hidden" value="{{ $navItem['link'] }}" name="link"/>
							<p class="delete_nav_item">x</p>
						</div>
						<?php Nav::buildChildNav($navItem); ?>
					</li>
				@endforeach
			@endif
		</ol>
		<div class="form_actions">
			{{ Form::submit('Save Nav', array('class'=>'btn')) }}
			<div id="save_message" class="alert hidden"></div>
		</div>
		{{ Form::token() . Form::close() }}
	</div>
	<div class="span6">
		<div class="row-fluid">
			<h2>Documents</h2>
			<ul>
				@if(0 == count($docs))
					<li>No Documents Found</li>
				@else
					@foreach($docs as $doc)
						<li class="menu_item">
							<span class="doc-title">{{ $doc->title }}</span>
							<input type="checkbox" value="{{ $doc->id }}"/>
							<input type="hidden" name="type" value="doc" />
						</li>
					@endforeach
				@endif
			</ul>
			<div class="form-actions">
				{{ Form::button('Add to Nav', array('id' => 'add-docs', 'class' => 'btn')) }}
			</div>
		</div>
		<div class="row-fluid">
			<h2>Custom Nav Item</h2>
			{{ Form::label('label', 'Label:') . Form::text('label', Input::old('label'), array('placeholder' => 'Label', 'id' => 'custom-label')) }}
			{{ Form::label('slug', 'Slug') . Form::text('slug', Input::old('slug'), array('placeholder' => 'Slug', 'id' => 'custom-slug')) }}
			<div class="form-actions">
				{{ Form::button('Add to Nav', array('id' => 'add-custom', 'class'=>'btn')) }}
			</div>
		</div>
	</div>
</div>
@endsection
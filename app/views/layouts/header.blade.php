<div class="navbar-header ">
  <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
    <span class="sr-only">Toggle navigation</span>
    <span class="icon-bar"></span>
    <span class="icon-bar"></span>
    <span class="icon-bar"></span>
  </button>
  <a class="brand logo-madison navbar-brand link-home" href="{{ URL::to('/') }}" target="_self">
  	Madison <span class="level">Demo</span>
  </a>
</div>
<div class="nav nav-main navbar-collapse collapse">
	<ul class="nav navbar-nav navbar-right">
		<li class="link-about"><a href="{{ URL::to('about') }}" target="_self">{{ trans('messages.about') }}</a></li>
		<li class="link-faq"><a href="{{ URL::to('faq') }}" target="_self">{{ trans('messages.faq') }}</a></li>
		<li class="link-support"><a href="https://rally.org/opengovfoundation" target="_blank">{{ trans('messages.donate') }}</a></li>
		<li class="link-subscribe"><a href="http://opengovfoundation.us6.list-manage.com/subscribe?u=9d450bf68b3df1185fc9f62b2&id=40a5a16e19" target="_blank">{{ trans('messages.subscribe') }}</a></li>
		@if(Auth::check())
			<?php 
				$activeGroupId = Session::get('activeGroupId');
			?>
			<li class="dropdown">
				<a class="dropdown-trigger" href="#" data-toggle="dropdown">{{ trans('messages.welcome') }} {{ Auth::user()->fname }} <?php if($activeGroupId > 0): ?>({{ Auth::user()->activeGroup()->getDisplayName() }})<?php endif; ?><span class="caret"></span></a>
				<ul class="dropdown-menu" role="menu">
					@if(Auth::user()->hasRole('Independent Sponsor') || Auth::user()->groups()->exists())
					<li class="link-settings"><a href="{{ URL::to('documents') }}" target="_self">{{ trans('messages.mydocs') }}</a>
					@endif
					<li class="link-settings"><a href="{{ URL::to('user/edit/' . Auth::user()->id) }}" target="_self">{{ trans('messages.settings') }}</a></li>
					<li><a href="{{ URL::to('user/edit/' . Auth::user()->id) . '/notifications' }}" target="_self">{{ trans('messages.notifications') }}</a></li>
					<li class="link-settings"><a href="{{ URL::to('groups') }}" target="_self">{{ trans('messages.group') }}</a></li>
					@if(Auth::user()->hasRole('Admin'))
					<li><a href="{{ URL::to('dashboard') }}" target="_self">{{ trans('messages.admin') }}</a></li>
					@endif
					
					<?php 
						$userGroups = Auth::user()->groups();
					?>
					
					<?php if($userGroups->count() > 0): ?>
					<li class="dropdown-submenu">
						<a class="dropdown-trigger" href="#" data-toggle="dropdown">{{ trans('messages.useas') }}</a>
						<ul class="dropdown-menu" role="menu">
						<?php if($activeGroupId !== 0): ?>
							<li class="link-settings"><a href="/groups/active/0" target="_self">{{ Auth::user()->fname }} {{ Auth::user()->lname }}</a></li>
						<?php endif; ?>
						<li class="divider"></li>
							<?php foreach($userGroups->get() as $group): ?>
								<li class="link-settings"><a href="/groups/active/{{ $group->id }}" target="_self">{{ $group->getDisplayName() }} {{ $group->id == $activeGroupId ? '(active)' : '' }}</a></li>
							<?php endforeach;?>
							
						</ul>
					</li>
					<?php endif; ?>
					<li class="link-logout"><a href="{{ URL::to('logout') }}" target="_self">{{ trans('messages.logout') }}</a></li>
				</ul>
			</li>
		@else
			<li class="link-login"><a href="{{ URL::to('user/login') }}" target="_self">{{ trans('messages.login') }}</a></li>
			<li class="link-signup"><a href="{{ URL::to('user/signup') }}" target="_self">{{ trans('messages.signup') }}</a></li>
		@endif
	</ul>
</div><!--/.navbar-collapse -->

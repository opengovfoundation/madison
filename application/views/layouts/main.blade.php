<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8"/>
		<title>Madison</title>
		@section('header_meta')
		<meta name="description" content=""/>
		<meta name = ""/>
		<meta property="og:title" content="Madison" />
    	<meta property="og:description" content="Internet users have a right to open government and a voice in government. We exist to power your participation in government. Madison is just a start." />
    	<meta property="og:type" content="website"/>
    	<meta property="og:url" content="<?php echo URL::base(); ?>"/>
    	<meta property="og:image" content=""/>
		<meta property="og:site_name" content=""/>
		@yield_section
		{{ Asset::styles() }}
		{{ Asset::scripts() }}
	</head>
	<body>
		<div id="header-wrapper">
    		<div id="header">
				<a href="<?php echo URL::base(); ?>">
					<div id="logo"></div>
				</a>
			</div>
    	</div>
		<div id="nav">
    		<div id="subnav-items">
				<?php if(isset($nav) && $nav != '') : ?>
				<?php $first = true; foreach($nav as $navItem) : ?>
					<div class="subnav_div"></div>
					<?php if(!isset($navItem['children'])) : ?>
						<a href="<?php echo URL::base() . $navItem['link']; ?>">
							<div class="subnav-item"><?php echo $navItem['label']; ?></div>
						</a>
					<?php else : ?>
						<div class="subnav_dropdown">
							<a href="<?php echo URL::base() . $navItem['link']; ?>">
								<div class="subnav-item">
								<?php echo $navItem['label']?><img src="/assets/i/arrow-down.png" class="dropdown_arrow"/>	
								</div>
							</a>
							<ul>
								<?php foreach($navItem['children'] as $child) : ?>
									<li>
										<a href="<?php echo URL::base() . $child['link']; ?>">
											<?php echo $child['label']; ?>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endif; ?>
				<?php endforeach; ?>
				<?php endif; ?>
        	</div>
    	</div>
		<div id="nav-items">
		    <?php if(Auth::check()) : ?>
        		<a href="<?php echo URL::base(); ?>/edit/user"><div class="nav-item">Update Account</div></a>
        		<a href="{{ URL::to('logout') }}"><div class="nav-item">Logout</div></a>
				<?php if(Auth::user()->user_level == 1) echo HTML::link('dashboard', 'Dashboard'); ?>
        		<div class="right" style="padding:0px 15px; text-shadow: #999 2px 2px 2px; margin-top:10px;">Welcome <?php echo Auth::user()->fname; ?></div>
      		<?php else : ?>
        		<a href="<?php echo Helpers::fbLogin(); ?>"><div class="nav-item login-button">Log in with Facebook</div></a>
        		<a href="<?php echo URL::base(); ?>/signup"><div class="nav-item">Create an Account</div></a>
        		<a href="<?php echo URL::to_action('login'); ?>"><div class="nav-item">Login</div></a>
      		<?php endif; ?>
    	</div>
		<div id="wrap">
    		<div id="content-wrapper">
				<div id="alert_wrapper" class="text-center">
					@render('success')
					@render('message')
					@render('errors')
				</div>
        		<div id="content">
				@section('content')
				Content Area
				@yield_section
				</div>
			</div>
		</div>
		<div id="footer">
      		<div style="margin-bottom:10px;">
        		<a href="<?php echo URL::base(); ?>/terms-conditions">Terms & Conditions</a> | &nbsp;
        		<a href="<?php echo URL::base(); ?>/contact-us?f=b">Report a bug</a> | &nbsp;          
      		</div>
  		</div>
	</body>
</html>
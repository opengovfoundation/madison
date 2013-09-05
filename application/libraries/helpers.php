<?php
/**
 * 	Helper functions for Madison
 */
class Helpers{
	
	/**
	 * 	Facebook Login url generation
	 */
	public static function fbLogin($redirect = ''){
		$facebook = IoC::resolve('facebook-sdk');
		
		//Redirect to the home page if no url was given
		$redirect = $redirect == '' ? URL::home() : $redirect;
		
		$params = array(
			'redirect_uri' => $redirect,
			'display' => 'popup',
			'scope' => 'email' 
		);
		
		return $facebook->getLoginUrl($params);
	}
	
	/**
	 * 	Recursive function to output a doc content tree
	 */
	public static function output_tree($parent){
		?>
		<ol>
			<li>
				
				<div id="content_<?php echo $parent->id; ?>" class="content_item"><span><?php echo $parent->content; ?></span><span id="badge_<?php echo $parent->id; ?>" class="badge pull-right"></span></div>
				<?php 
					$children = $parent->content_children()->get();
					if(count($children) > 0){
						foreach($children as $child){
							Helpers::output_tree($child);
						}
					}
				?>
			</li>
		</ol>
		<?php
	}
}

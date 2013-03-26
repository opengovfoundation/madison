<?php
class Helpers{
	
	//Generates Facebook login url
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
	
	public static function output_tree($parent){
		?>
		<ol>
			<li id="content_<?php echo $parent->id; ?>" class="content_item">
				<?php 
					echo $parent->content;
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

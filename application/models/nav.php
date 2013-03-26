<?php
class Nav {
	protected $items;
	
	public function __construct($navString){
		$items = unserialize($navString);
		$this->items = $items;
	}
	
	public static function buildChildNav($parent){
		if(!isset($parent['children'])){
			return;
		}
		?>
		<ol>
		<?php
			foreach($parent['children'] as $child){
				?>
				<li class="nav_item">
					<div class="sort_handle">
						<span><?php echo $child['label']; ?></span>
						<input type="hidden" value="<?php echo $child['link']; ?>"/>
						<p class="delete_nav_item">x</p>
					</div>
				</li>
				<?php
				Nav::buildChildNav($child);
			}
		?>
		</ol>
		<?php
	}
}


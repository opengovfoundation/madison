<?php
class DocContent extends Eloquent{
	protected $table = 'doc_contents';
	
	public function doc(){
		return $this->belongsTo('Doc');
	}
	
	public function notes(){
		return $this->hasMany('Note', 'section_id');
	}
	
	public function content_children(){
		return $this->hasMany('DocContent', 'parent_id');
	}
	
	public function content_parent(){
		return $this->belongsTo('DocContent', 'parent_id');
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
							DocContent::output_tree($child);
						}
					}
				?>
			</li>
		</ol>
		<?php
	}
	
	public static function print_admin_list($contentItem){
		?>
		<li class="doc_item">
			<div class="sort_handle">
				<span>
					<?php echo HTML::image('img/arrow-down.png', 'Down Arrow', array('class'=>'dropdown_arrow')); ?>
					<?php echo $contentItem->content ?>
				</span>
				<input type="hidden" name="content_id" value="<?php echo $contentItem->id; ?>"/>
				<p class="add_doc_item">+</p>
				<p class="delete_doc_item">&times;</p>
				<p class="doc_item_content"><textarea><?php echo $contentItem->content; ?></textarea></p>
			</div>
			<ol>
				<?php 
				foreach($contentItem->content_children()->order_by('child_priority', 'asc')->get() as $child){
					DocContent::print_admin_list($child);
				} 
				?>
			</ol>
		</li>
	<?php }
}


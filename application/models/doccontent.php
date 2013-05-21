<?php
class DocContent extends Eloquent{
	public static $table = 'doc_contents';
	public static $timestamp = true;
	
	public function doc(){
		return $this->belongs_to('Doc');
	}
	
	public function notes(){
		return $this->has_many('Note', 'section_id');
	}
	
	public function content_children(){
		return $this->has_many('DocContent', 'parent_id');
	}
	
	public function content_parent(){
		return $this->belongs_to('DocContent', 'parent_id');
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
		<?php
		
		
	}
}


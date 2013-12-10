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
				<div>
					<?php echo HTML::image('img/arrow-down.png', 'Down Arrow', array('class'=>'dropdown_arrow edit_info_link')); ?>
					<span class="markdown content-container"><?php echo $contentItem->content ?></span>
				</div>
				<input type="hidden" name="content_id" value="<?php echo $contentItem->id; ?>"/>
				<div class="add_doc_item">+</div>
				<div class="delete_doc_item">&times;</div>
				<div class="doc_item_content hidden">
					<div id="wmd-button-bar"></div>
					<textarea class="wmd-input" id="wmd-input"><?php echo $contentItem->content; ?></textarea>
					<div id="wmd-preview" class="wmd-panel wmd-preview"></div>
					<script type="text/javascript">
						$(function () {
							var converter1 = Markdown.getSanitizingConverter();

							var editor1 = new Markdown.Editor(converter1);

							editor1.run();
							console.log('done', editor1);
						});
					</script>
				</div>
			</div>
			<ol>
				<?php
				foreach($contentItem->content_children()->orderBy('child_priority', 'asc')->get() as $child){
					DocContent::print_admin_list($child);
				}
				?>
			</ol>
		</li>
	<?php }
}


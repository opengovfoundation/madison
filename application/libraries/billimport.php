<?php
/**
 * 	Import Class for House Bill XML
 * 	Not pretty at the moment, but it works
 */

	/**
	 * 	Bill class
	 */
	class BillImport{
		public $url;
		public $rawXML;
		public $domdoc;
		public $rootTag = 'legis-body';
		public $title;
		public $slug;
		public $shortname;
		public $bill;
		public $structure = array('section', 'subsection', 'paragraph', 'subparagraph', 'clause', 'subclause', 'item');
		public $text_tags = array('enum', 'header', 'text');
		
		/**
		 * 	Constructor
		 * 	1.  Set Import URL
		 * 	2.  Set rawXML and DOMDocument
		 */
		public function __construct($url){
			$this->url = $url;
			$this->setXML();
		}
		
		/**
		 * 	Sets top-level bill information
		 */
		public function createDoc(){
			$this->checkAttr('domdoc');
			
			$this->setBillTitle();
			$this->setBillSlug();
			
			$bill = new Doc();
			$bill->title = $this->title;
			$bill->slug = $this->slug;
			$bill->save();
			
			
			
			try{
				$starter = new DocContent();
				$starter->doc_id = $bill->id;
				$starter->content = $this->getBillMeta('legis-type') . ' ' . $this->getBillMeta('official-title');
				$starter->save();

				$bill->init_section = $starter->id;
				$bill->save();
				
				$this->bill = $bill;
				
				$rootElement = $this->domdoc->getElementsByTagName($this->rootTag)->item(0);
				
				$c = 0;
				if(!is_object($rootElement)){
					throw new Exception("Couldn't retrieve root element");
				}
				
				foreach($rootElement->childNodes as $child){
					$this->saveChildren($child, $starter->id, $c++);
				}
			}catch(Exception $e){
				$bill->delete();
				$starter->delete();
				throw new Exception($e->getMessage());
			}
			
			
			return true;
		}
		
		/**
		 * 	Recursive function to save children of a given node as DocContent items
		 */
		public function saveChildren($node, $parent_id, $child_priority){
			if(!isset($parent_id) || $parent_id == 0){
				throw new Exception("Error saving content.");
			}
			
			//Check the node is in the document structure elements
			@$valid = in_array($node->tagName, $this->structure);
			if(!$valid){
				//If the node has no children, return
				if(!$node->hasChildNodes()){
					return;
				}else{//Otherwise save the children, passing on the parent id ( this isn't a valid parent )
					$c = 0;
					foreach($node->childNodes as $child){
						$this->saveChildren($child, $parent_id, $c++);
					}
					
					return;
				}
			}
			
			//Save this item
			$contentItem = new DocContent();
			$contentItem->doc_id = $this->bill->id;
			$contentItem->content = $this->getNodeContent($node);
			$contentItem->child_priority = $child_priority;
			$contentItem->parent_id = $parent_id;
			$contentItem->save();
			
			if($node->childNodes->length == 0){
				return;
			}
			
			$c = 0;
			foreach($node->childNodes as $child){
				$this->saveChildren($child, $contentItem->id, $c++);
			}
		}
		
		/**
		 * 	Combine child nodes designated as relevant text
		 */
		protected function getNodeContent($node){
			$content = "";
			
			foreach($node->childNodes as $child){
				if(in_array($child->nodeName, $this->text_tags)){
					$content .= " " . preg_replace('/\s+/', ' ', $child->nodeValue);
				}
			}
			
			$content = trim($content);
			return $content;
		}
		
		/**
		 * 	Set bill slug from the bill title
		 */
		public function setBillSlug(){
			$this->slug = strtolower(urlencode(str_replace(array(' ', '.'), array('-', ''), $this->title)));
			
			return true;
			
		}
		
		/**
		 * 	Set bill title ( Checks short-title tag and then legis-num)
		 */
		public function setBillTitle(){
			//Retrieve Bill Title
			if(FALSE === ($bill_title = $this->getBillMeta('short-title'))){
				if(FALSE === ($bill_title = $this->getBillMeta('legis-num'))){
					throw new Exception("Could not retrieve bill title");
				}
			}
			
			$this->title = $bill_title;
			
			return true;
		}
		
		/**
		 * 	Set rawXML attribute and create DOMDocument
		 */
		public function setXML(){
			$this->checkAttr('url');
			
			$xml = file_get_contents($this->url);
			if(false === $xml){
				throw new Exception("Unable to retrieve url content");
			}
			
			$this->rawXML = $xml;
			
			$this->setDomDoc();
		}
		
		/**
		 * 	Get top-level meta information from the bill
		 */
		public function getBillMeta($tag){
			$billMeta = $this->domdoc->getElementsByTagName($tag);
			if(get_class($billMeta) != 'DOMNodeList'){
				throw new Exception("Incorrect bill meta object returned (" . get_class($billMeta) . ") for tag ($tag)");
			}
			
			if(!is_object($billMeta->item(0))){
				return false;
			}
			
			$billMeta = trim($billMeta->item(0)->nodeValue);
			$billMeta = preg_replace('/(\s)+/', ' ', $billMeta);
			
			return $billMeta;
		}
		
		/**
		 * 	Create DOMDocument object from raw XML
		 */
		public function setDomDoc(){
			$this->checkAttr('rawXML');
			
			$domdoc = new DOMDocument();
			$domdoc->loadXML($this->rawXML);
			
			$this->domdoc = $domdoc;
		}
		
		/**
		 * 	Sets root string where parser will start from
		 */
		public function setRootTag($rootTag){
			$this->rootTag = $rootTag;
			
			return true;
		}
		
		/**
		 * 	Check attributes are set
		 * 	Accepts single string or array of strings
		 */
		protected function checkAttr($attribute){
			if(is_array($attribute)){
				foreach($attribute as $attr){
					if(!isset($this->$attr)){
						throw new Exception("$attr not set.");
					}
				}
			}else{
				if(!isset($this->$attribute)){
					throw new Exception("$attribute not set.");
				}
			}

			return true;
		}
		
	}
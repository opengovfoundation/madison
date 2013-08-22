<?php
/**
 * 	Base controller for catch-alls
 */
class Base_Controller extends Controller {

	/**
	 * Catch-all method for requests that can't be matched.
	 *
	 * @param  string    $method
	 * @param  array     $parameters
	 * @return Response
	 */
	public function __call($method, $parameters)
	{
		return Response::error('404');
	}
	
	public function __construct(){
		//Assets
		//TODO: Remove and use bootstrap tooltips
		Asset::add('qtip-style', 'stylesheets/jquery.qtip.min.css');
		Asset::add('bootstrap-css', 'bootstrap/css/bootstrap.min.css');
		Asset::add('bootstrap-responsive', 'bootstrap/css/bootstrap-responsive.min.css');
		Asset::add('style', 'stylesheets/style.css');
		Asset::add('jquery', 'js/jquery-1.8.0.js');
		Asset::add('jquery-ui', 'js/jquery.ui.core.js');
		Asset::add('jquery-ui-widget', 'js/jquery.ui.widget.js');
		Asset::add('jquery-ui-mouse', 'js/jquery.ui.mouse.js');
		Asset::add('jquery-ui-sortable', 'js/jquery.ui.sortable.js');
		Asset::add('jquery-mjs-nestedsortable', 'js/jquery.mjs.nestedSortable.js');
		Asset::add('bootstrap-js', 'bootstrap/js/bootstrap.min.js');
		Asset::add('modernizr', 'js/modernizr-latest.js');
		Asset::add('bill-reader', 'js/bill-reader.js');
		Asset::add('main', 'js/madison.js');
		Asset::add('underscore', 'js/underscore.min.js');
		//TODO: Remove and use bootstrap tooltips
		Asset::add('qtip', 'js/jquery.qtip.min.js');
		//TODO: Remove and use bootstrap tooltips
		Asset::add('imagesloaded', 'js/imagesloaded.min.js');
		parent::__construct();
		
		$docs = Doc::order_by('updated_at', 'desc')->take(10)->get();
		View::share('docs', $docs);
	}

}
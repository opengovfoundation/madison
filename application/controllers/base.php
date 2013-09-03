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
		Asset::add('bootstrap-css', 'vendor/bootstrap/css/bootstrap.min.css');
		Asset::add('bootstrap-theme', 'vendor/bootstrap/css/bootstrap-theme.min.css');
		Asset::add('jquery', 'vendor/jquery/jquery-1.9.1.min.js');
		Asset::add('jquery-ui', 'vendor/jquery/jquery.ui.core.js');
		Asset::add('jquery-ui-widget', 'vendor/jquery/jquery.ui.widget.js');
		Asset::add('jquery-ui-mouse', 'vendor/jquery/jquery.ui.mouse.js');
		Asset::add('jquery-ui-sortable', 'vendor/jquery/jquery.ui.sortable.js');
		Asset::add('jquery-mjs-nestedsortable', 'vendor/jquery/jquery.mjs.nestedSortable.js');
		Asset::add('bootstrap-js', 'vendor/bootstrap/js/bootstrap.min.js');
		Asset::add('modernizr', 'vendor/modernizr-latest.js');
		Asset::add('underscore', 'vendor/underscore.min.js');
		Asset::add('style', 'stylesheets/style.css');
		Asset::add('main', 'js/madison.js');
		
		parent::__construct();
		
		$docs = Doc::order_by('updated_at', 'desc')->take(10)->get();
		View::share('docs', $docs);
	}

}
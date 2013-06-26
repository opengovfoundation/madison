<?php
/**
 * 	Controller for individual pages
 */
class Page_Controller extends Base_Controller 
{
	public $restful = true;

	public function get_home()
	{
		return View::make('home.index');
	}

	/**
	 * About Page
	 *
	 * @return object
	 */
	public function get_about()
	{
		$data = array(
			'page_id' => 'about',
			'page_title' => 'About the Madison Platform',
		);

		return View::make('about.index', $data);
	}

	/**
	 * FAQ Page
	 *
	 * @return object
	 */
	public function get_faq()
	{
		$data = array(
			'page_id' => 'faq',
			'page_title' => 'Frequently Asked Questions',
		);

		return View::make('faq.index', $data);
	}
}
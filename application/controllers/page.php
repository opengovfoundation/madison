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
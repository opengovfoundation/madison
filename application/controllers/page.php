<?php

class Page_Controller extends Base_Controller 
{
	public $restful = true;

	/**
	 * Home Page
	 */
	public function get_home()
	{
		$data = array(
			'page_id' => 'home',
			'page_title' => 'The Madison Project',
		);

		return View::make('home', $data);
	}

	/**
	 * About Page
	 */
	public function get_about()
	{
		$data = array(
			'page_id' => 'about',
			'page_title' => 'About the Madison Platform',
		);

		return View::make('about', $data);
	}

	/**
	 * FAQ Page
	 */
	public function get_faq()
	{
		$data = array(
			'page_id' => 'faq',
			'page_title' => 'Frequently Asked Questions',
		);

		return View::make('faq', $data);
	}
}
<?php

class PageController extends BaseController 
{
	public $restful = true;

	/**
	 * Home Page
	 */
	public function home()
	{
		$data = array(
			'page_id'		=> 'home',
			'page_title'	=> 'The Madison Project',
			'rightbar'		=> array('recentdocs')
		);

		return View::make('page.index', $data);
	}

	/**
	 * About Page
	 */
	public function about()
	{
		$data = array(
			'page_id'		=> 'about',
			'page_title'	=> 'About the Madison Platform',
			'rightbar'		=> array('getstarted')
		);

		return View::make('page.index', $data);
	}

	/**
	 * FAQ Page
	 */
	public function faq()
	{
		$data = array(
			'page_id'		=> 'faq',
			'page_title'	=> 'Frequently Asked Questions',
			'rightbar'		=> array('getstarted')
		);

		return View::make('page.index', $data);
	}
}
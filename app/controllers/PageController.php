<?php

class PageController extends BaseController 
{
	public $restful = true;

	/**
	 * Home Page
	 */
	public function home()
	{
		return View::make('single');
	}

	/**
	 * About Page
	 */
	public function getAbout()
	{
		return View::make('single');
		//return View::make('page.index', $data);
	}

	/**
	 * FAQ Page
	 */
	public function faq()
	{
		//return View::make('page.index', $data);
		return View::make('single');
	}

	public function privacyPolicy(){
		$data = array(
			'page_id'	=> 'privacy',
			'page_title'	=> 'Privacy Policy'
		);

		return View::make('page.index', $data);
	}

	public function terms(){
		$data = array(
			'page_id'	=> 'terms',
			'page_title'	=> 'Terms and Conditions'
		);

		return View::make('page.index', $data);
	}

	public function copyright(){
		$data = array(
			'page_id' => 'copyright',
			'page_title' => 'Copyright Policy'
		);

		return View::make('page.index', $data);
	}
}
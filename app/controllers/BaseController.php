<?php
/**
 * 	Base controller for catch-alls
 */
class BaseController extends Controller {
	
	protected $es;

	public function __construct(){
		$docs = Doc::orderBy('updated_at', 'desc')->take(10)->get();
		View::share('docs', $docs);

		$socials = array(
		                'og_image'			=> Config::get('socials.og_image'),
		                'og_title'			=> Config::get('socials.og_title'),
		                'og_description'	=> Config::get('socials.og_description'),
		                'og_url'			=> Request::url(),
		                'og_site_name'		=> Request::root(),
		           	);
		View::share('socials', $socials);

		$params = array('hosts' => array('localhost:9200'));

		$this->es = new Elasticsearch\Client($params);
	}

	/**
	*	Helper function to return error as growl message
	*
	*	@param string $message
	*	@param string $severity
	*	@return array $growled
	* @todo this should accept an array of messages / severities
	*/
	protected function growlMessage($message, $severity){
		$growled = array(
			'messages'	=> array(
				array('text' => $message, 'severity' => $severity)
			)
		);

		return $growled;
	}
}

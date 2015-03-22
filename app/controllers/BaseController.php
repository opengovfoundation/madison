<?php
/**
 * 	Base controller for catch-alls
 */
class BaseController extends Controller
{
    
    protected $es;

    public function __construct()
    {
        $docs = Doc::orderBy('updated_at', 'desc')->take(10)->get();
        View::share('docs', $docs);

        $socials = array(
                        'og_image'            => Config::get('socials.og_image'),
                        'og_title'            => Config::get('socials.og_title'),
                        'og_description'    => Config::get('socials.og_description'),
                        'og_url'            => Request::url(),
                        'og_site_name'        => Request::root(),
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
    protected function growlMessage($messages, $severity)
    {
        $growled = array('messages'    => array());

        //If we've been passed an array of messages
        if (is_array($messages)) {

            //If we've only been passed one severity
            if (!is_array($severity)) {
                //Set that severity for every message
                foreach ($messages as $message) {
                    array_push($growled['messages'], array('text' => $message, 'severity' => $severity));
                }
            } elseif (count($message) === count($severity)) { //Ensure we have the same number of messages <=> severities
                foreach ($messages as $index => $message) {
                    array_push($growled['messages'], array('text' => $message, 'severity' => $severity[$index]));
                }
            } else { //Throw an exception if there's a mismatch
                throw new Exception("Unable to create growl message array because of size mismatches");
            }
        } else {
            array_push($growled['messages'], array('text'    => $messages, 'severity' => $severity));
        }

        return $growled;
    }
}

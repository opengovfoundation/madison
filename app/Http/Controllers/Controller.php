<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
    }

    /**
     *	Helper function to return error as growl message.
     *
     *	@param string $message
     *	@param string $severity
     *  @param array $params
     *
     *	@return array $growled
     *
     * @todo this should accept an array of messages / severities
     *
     *  $params is an easy way to append other return information
     */
    protected function growlMessage($messages, $severity, $params = null)
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

        if ($params) {
            if (!is_array($params)) {
                $params = [$params];
            }

            return array_merge($growled, $params);
        }

        return $growled;
    }

}

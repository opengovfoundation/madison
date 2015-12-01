<?php

namespace App\Http\Controllers;

/**
 * 	Controller for Document actions.
 */
class ApiController extends Controller
{
    protected $es; // ElasticSearch client

    // public function getIndex(){
    // 	$api = array(
    // 		'message' 	=> 'Madison API',
    // 		'links'		=> array(
    // 		    'annotation'	=> array(
    //          'create'	=> array(
    // 					'url'	=> action('AnnotationApiController@postIndex')
    // 				),
    // 		    	'read'		=> array(
    // 		    		'url'	=> action('AnnotationApiController@getIndex', array('id'))
    // 		    	),
    // 		    	'update'	=> array(
    // 					'url'	=> action('AnnotationApiController@putIndex', array('id'))
    // 				),
    // 				'destroy'	=> array(
    // 					'url'	=> action('AnnotationApiController@deleteIndex', array('id'))
    // 				),
    // 				'search'	=> array(
    // 					'url'	=>action('AnnotationApiController@getSearch')
    // 				)
    // 		    ),
    // 		)
    // 	);

    // 	return Response::json($api);
    // }
}

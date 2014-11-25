<?php

namespace Madison\Services\ServiceProviders;

use \Illuminate\Support\ServiceProvider;

class SessionRejectServiceProvider extends ServiceProvider {

  public function register(){
    $me = $this;
    $this->app->bind('session.reject', 
      function($app) use ($me){
        return function($request) use ($me) {
          return call_user_func_array(array($me, 'reject'), array($request));
        }  
      }
    );
  }

  protected function reject($request){
    return (Request::isMethod('get') && Request::is('api/docs'));
  }
}

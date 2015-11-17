angular.module('madisonApp.controllers')
  .controller('OauthLoginController', ['$state', '$translate', 'growl',
    function ($state, $translate, growl) {
      growl.success( $translate.instant('form.login.success') );
      $state.go( 'index' );
    }
  ]);

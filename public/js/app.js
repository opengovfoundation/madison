/*global window*/
/*global history*/
window.jQuery = window.$;

var user;

var imports = [
  'ngAnimate',
  'ngSanitize',
  'ngResource',
  'ngRoute',
  'pascalprecht.translate',
  'madisonApp.constants',
  'madisonApp.filters',
  'madisonApp.services',
  'madisonApp.resources',
  'madisonApp.directives',
  'madisonApp.controllers',
  'madisonApp.prompts',
  'madisonApp.translate',
  'angulartics',
  'angulartics.google.analytics',
  'ui.router',
  'ct.ui.router.extras.future',
  'ui.bootstrap',
  'ui.bootstrap.datetimepicker',
  'angular-growl',
  'ipCookie',
  'angularFileUpload',
  'yaru22.angular-timeago'
];

try {
  var app = angular.module('madisonApp', imports);
} catch (err) {
  console.log('Caught Error: ' + err);
}

try {
  //Set up Growl notifications and interceptor
  app.config(['growlProvider', '$httpProvider',
    function (growlProvider, $httpProvider) {
      //Set up growl notifications
      growlProvider.messagesKey("messages");
      growlProvider.messageTextKey("text");
      growlProvider.messageSeverityKey("severity");
      growlProvider.onlyUniqueMessages(true);
      growlProvider.globalTimeToLive(
        {
          'error': -1,
          'info': 3000,
          'warning': 3000,
          'success': 3000
        }
      );
      $httpProvider.interceptors.push(growlProvider.serverMessagesInterceptor);
    }]);
} catch (err) {
  console.error(err);
}

//Set HTML5 mode
app.config(['$locationProvider',
  function ($locationProvider) {
    $locationProvider.html5Mode({
      enabled: true,
      requireBase: false
    });
  }]);

app.run(function(AuthService, annotationService, AUTH_EVENTS, $rootScope,
  $window, $location, $state, growl, SessionService, Page, $translate, $q) {

  $rootScope.loadPages = function() {
    return $q(function(resolve, reject) {

      Page.query(function(pages) {
        $rootScope.headerLinks = _.where(pages, { header_nav_link: true });
        $rootScope.footerLinks = _.where(pages, { footer_nav_link: true });
        resolve(pages);
      }, function(err) {
        $translate('errors.general.load').then(function(translation) {
          growl.error(translation);
        });
        reject();
      });

    });
  };

  /**
   * Load pages!
   */
  $rootScope.loadPages();


  if(!(window.history && history.pushState)){
    $rootScope.$on('$locationChangeStart', function(event) {
      var loc = window.location;
      var hash = loc.hash;
      var path = loc.pathname || '/';

      if (!hash) {
        loc.href = '/#' + path + (loc.search || '');
      }
    });
  }

  AuthService.setUser(user);

  //Check authorization on state change
  $rootScope.$on('$stateChangeStart', function (event, next) {
    var authorizedRoles = next.data.authorizedRoles;

    if (!AuthService.isAuthorized(authorizedRoles)) {
      event.preventDefault();

      if (AuthService.isAuthenticated()) {
        //user is not allowed
        $rootScope.$broadcast(AUTH_EVENTS.notAuthorized);
      } else {
        //user is not logged in
        $rootScope.$broadcast(AUTH_EVENTS.notAuthenticated);
      }
    }
  });

  //Check for 403 Errors ( Forbidden )
  $rootScope.$on(AUTH_EVENTS.notAuthorized, function () {
    growl.error('You are not authorized to view that page');
  });

  //Check for 401 Errors ( Not Authorized / Not logged in )
  $rootScope.$on(AUTH_EVENTS.notAuthenticated, function () {
    growl.error('You must be logged in to view that page');
    if($location.path() !== '/login')
    {
      $rootScope.returnTo = $location.path();
    }
    $state.go('login');
  });

  //Debugging Document Pages
  $rootScope.$on('$stateChangeStart',
    function (event, toState, toParams, fromState, fromParams) {

      if (toState.name === 'doc-page') {
        annotationService.destroyAnnotator();
      }
    });
});

//Manually bootstrap app because we want to get data before running
angular.element(document).ready(function () {
  var initInjector = angular.injector(['ng']);
  var $http = initInjector.get('$http');
  user = {};

  function installGA ( gaAccount ) {

    //Check we're actually passed an account value
    if ( gaAccount ) {
      var embed = document.createElement( 'script' );
      embed.text = "(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script','//www.google-analytics.com/analytics.js','ga');ga('create', '" + gaAccount + "', 'auto');//ga('send', 'pageview');";

      var scripts = document.getElementsByTagName( 'script' )[0];
      scripts.parentNode.insertBefore( embed, scripts );
    } else {
      console.info( "Couldn't install Google Analytics: %s", gaAccount );
    }
  }

  function installUservoice ( uservoiceHash ) {
    if ( !uservoiceHash ) {
      console.info( 'Unable to load UserVoice.  Settings: %s', uservoiceHash );
      return;
    }

    //Install Uservoice ( copied from uservoice embed script )
    //var UserVoice = window.UserVoice || [];
    window.UserVoice = [];

    var uv = document.createElement( 'script' );
    uv.type = 'text/javascript';
    uv.async = true;
    uv.src = '//widget.uservoice.com/' + uservoiceHash + '.js';
    var s = document.getElementsByTagName( 'script' )[0];
    s.parentNode.insertBefore( uv, s );

    // Set colors
    window.UserVoice.push( [ 'set', {
      accent_color: '#448dd6',
      trigger_color: 'white',
      trigger_background_color: 'rgba(46, 49, 51, 0.6)'
    } ] );

    // Identify the user and pass traits
    // To enable, replace sample data with actual user traits and uncomment the line
    window.UserVoice.push( [ 'identify', {

      //email:      'john.doe@example.com', // User’s email address
      //name:       'John Doe', // User’s real name
      //created_at: 1364406966, // Unix timestamp for the date the user signed up
      //id:         123, // Optional: Unique id of the user (if set, this should not change)
      //type:       'Owner', // Optional: segment your users by type
      //account: {
      //  id:           123, // Optional: associate multiple users with a single account
      //  name:         'Acme, Co.', // Account name
      //  created_at:   1364406966, // Unix timestamp for the date the account was created
      //  monthly_rate: 9.99, // Decimal; monthly rate of the account
      //  ltv:          1495.00, // Decimal; lifetime value of the account
      //  plan:         'Enhanced' // Plan name for the account
      //}
    } ] );

    // Add default trigger to the bottom-right corner of the window:
    window.UserVoice.push( [ 'addTrigger', {
      mode: 'contact',
      trigger_position: 'bottom-right'
    } ] );

    // Or, use your own custom trigger:
    //UserVoice.push(['addTrigger', '#id', { mode: 'contact' }]);

    // Autoprompt for Satisfaction and SmartVote (only displayed under certain conditions)
    window.UserVoice.push( [ 'autoprompt', {} ] );
  }

  //Get the current user
  $http.get('/api/user/current')
    .success(function (res) {
      var key;

      if (!$.isEmptyObject(res) && Object.getOwnPropertyNames(res.user).length > 0) {
        for (key in res.user) {
          if (res.user.hasOwnProperty(key)) {
            user[key] = res.user[key];
          }
        }
      } else {
        user = null;
      }

      //Get the vendor settings ( callback hell :( )
      $http.get( '/api/settings/vendors' )
        .then( function( res ) {
          vendors = res.data;

          installGA( vendors.ga );
          installUservoice( vendors.uservoice );

          angular.bootstrap(document, ['madisonApp']);
        });
    });

    $(document).delegate(
      '.nav-main li a:not(.dropdown-trigger)',
      'click',
      collapseNavMenu);

    $(document).delegate(
      '.link-home',
      'click',
      collapseNavMenu);

    function collapseNavMenu() {
      if ($('.navbar-collapse').attr('aria-expanded') === 'true') {
        $('.navbar-collapse').collapse('hide');
      }
    }
});


window.console = window.console || {};
window.console.log = window.console.log || function () {return; };

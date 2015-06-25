/*global window, document*/
angular.module( 'madisonApp.services' )
  .service( 'VendorService', function( $http, $window ) {
    var vendorService = {};

    vendorService.installUservoice = function() {
      var uservoiceHash = vendorService.settings.uservoice;

      //Install Uservoice ( copied from uservoice embed script )
      var UserVoice = window.UserVoice || [];
      var uv = document.createElement( 'script' );
      uv.type = 'text/javascript';
      uv.async = true;
      uv.src = '//widget.uservoice.com/' + uservoiceHash + '.js';
      var s = document.getElementsByTagName( 'script' )[0];
      s.parentNode.insertBefore( uv, s );

      // Set colors
      UserVoice.push( [ 'set', {
        accent_color: '#448dd6',
        trigger_color: 'white',
        trigger_background_color: 'rgba(46, 49, 51, 0.6)'
      } ] );

      // Identify the user and pass traits
      // To enable, replace sample data with actual user traits and uncomment the line
      UserVoice.push( [ 'identify', {

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
      UserVoice.push( [ 'addTrigger', {
        mode: 'contact',
        trigger_position: 'bottom-right'
      } ] );

      // Or, use your own custom trigger:
      //UserVoice.push(['addTrigger', '#id', { mode: 'contact' }]);

      // Autoprompt for Satisfaction and SmartVote (only displayed under certain conditions)
      UserVoice.push( [ 'autoprompt', {} ] );
    };

    vendorService.installGA = function() {
      var gaAccount = vendorService.settings.ga;

      //Install google analytics
    };

    vendorService.installVendors = function() {
      $http.get( '/api/settings/vendors' )
        .success( function( data ) {
          console.log( data );
          vendorService.settings = {
            "uservoice": data.uservoice,
            "ga": data.ga
          };

          //Install the vendor libraries
          vendorService.installUservoice();
          vendorService.installGA();
        } ).error( function() {
          console.error( 'Unable to load settings for vendor libraries!' );
        } );
    };

    return vendorService;

  } );

angular.module( 'madisonApp' )
  .config( [ '$stateProvider', '$futureStateProvider', '$urlRouterProvider', 'USER_ROLES',
    function( $stateProvider, $futureStateProvider, $urlRouterProvider, USER_ROLES ) {

    $urlRouterProvider.otherwise( '404' );

    $stateProvider
      .state( 'index', {
        url: "/",
        controller: "HomePageController",
        templateUrl: "/templates/pages/home.html",
        data: {
          authorizedRoles: [ USER_ROLES.all ]
        }
      } )
      // This is for older IE, which can't handle the '/#' route.
      .state( 'index-alias', {
        url: "",
        controller: "HomePageController",
        templateUrl: "/templates/pages/home.html",
        data: {
          authorizedRoles: [ USER_ROLES.all ]
        }
      } )
      .state( 'intro', {
        url: '/intro',
        controller: "IntroPageController",
        templateUrl: '/templates/pages/intro.html',
        data: {
          authorizedRoles: [ USER_ROLES.all ]
        }
      } )
      .state( 'doc-page', {
        url: "/docs/:slug",
        controller: "DocumentPageController",
        templateUrl: "/templates/pages/doc.html",
        data: {
          authorizedRoles: [ USER_ROLES.all ]
        }
      } )
      .state( 'my-documents', {
        url: "/documents",
        controller: 'MyDocumentsController',
        templateUrl: "/templates/pages/my-documents.html",
        data: {
          authorizedRoles: [ USER_ROLES.all ]
        }
      } )
      .state( 'deleted-docs', {
        url: '/documents/deleted',
        controller: 'DeletedDocsController',
        templateUrl: '/templates/pages/deleted-docs.html',
        data: {
          authorizedRoles: [ USER_ROLES.all ]
        }
      } )
      .state( 'admin-deleted-docs', {
        url: '/administrative-dashboard/docs/deleted',
        controller: 'DeletedDocsController',
        templateUrl: '/templates/pages/deleted-docs.html',
        data: {
          authorizedRoles: [ USER_ROLES.admin ]
        }
      } )
      .state( 'edit-doc', {
        url: '/dashboard/docs/:id',
        controller: 'DashboardEditorController',
        templateUrl: '/templates/pages/edit-doc.html',
        data: {
          authorizedRoles:
            [ USER_ROLES.admin, USER_ROLES.independent, USER_ROLES.groupMember ]
        }
      } )
      .state( 'login', {
        url: '/user/login',
        controller: "LoginPageController",
        templateUrl: "/templates/pages/login.html",
        data: {
          authorizedRoles: [ USER_ROLES.all ]
        }
      } )
      .state( 'facebook-login', {
        url: '/user/login/facebook-login',
        controller: 'OauthLoginController',
        data: {
          authorizedRoles: [ USER_ROLES.all ]
        }
      } )
      .state( 'twitter-login', {
        url: '/user/login/twitter-login',
        controller: 'OauthLoginController',
        data: {
          authorizedRoles: [ USER_ROLES.all ]
        }
      } )
      .state( 'linkedin-login', {
        url: '/user/login/linkedin-login',
        controller: 'OauthLoginController',
        data: {
          authorizedRoles: [ USER_ROLES.all ]
        }
      } )
      .state( 'signup', {
        url: '/user/signup',
        controller: "SignupPageController",
        templateUrl: "/templates/pages/signup.html",
        data: {
          authorizedRoles: [ USER_ROLES.all ]
        }
      } )
      .state( 'password-reset-request', {
        url: '/password/reset',
        controller: 'PasswordResetController',
        templateUrl: '/templates/pages/password-reset-request.html',
        data: {
          authorizedRoles: [ USER_ROLES.all ]
        }
      } )
      .state( 'password-reset-landing', {
        url: '/password/reset/:token',
        controller: 'PasswordResetLandingController',
        templateUrl: '/templates/pages/password-reset-landing.html',
        data: {
          authorizedRoles: [ USER_ROLES.all ]
        }
      } )
      .state( 'resend-confirmation', {
        url: '/verification/resend',
        controller: 'ResendConfirmationController',
        templateUrl: '/templates/pages/resend-confirmation.html',
        data: {
          authorizedRoles: [ USER_ROLES.all ]
        }
      } )
      .state( 'resend-confirmation-landing', {
        url: '/user/verify/:token',
        controller: 'ResendConfirmationController',
        data: {
          authorizedRoles: [ USER_ROLES.all ]
        }
      } )
      .state( 'user-notification-settings', {
        url: "/user/edit/:user/notifications",
        controller: "UserNotificationsController",
        templateUrl: "/templates/pages/user-notification-settings.html",
        data: {
          authorizedRoles: [ USER_ROLES.admin, USER_ROLES.independent, USER_ROLES.groupMember, USER_ROLES.basic ]
        }
      } )
      .state( 'group-management', {
        url: "/groups",
        controller: "GroupManagementController",
        templateUrl: "/templates/pages/group-management.html",
        data: {
          authorizedRoles: [ USER_ROLES.all ]
        }
      } )
      .state( 'create-group', {
        url: "/groups/edit",
        controller: "GroupEditController",
        templateUrl: "/templates/pages/group-edit.html",
        data: {
          authorizedRoles: [ USER_ROLES.admin, USER_ROLES.independent, USER_ROLES.groupMember, USER_ROLES.basic ]
        }
      } )
      .state( 'edit-group', {
        url: "/groups/edit/:groupId",
        controller: "GroupEditController",
        templateUrl: "/templates/pages/group-edit.html",
        data: {
          authorizedRoles: [ USER_ROLES.admin, USER_ROLES.groupMember ]
        }
      } )
      .state( 'manage-group-members', {
        url: "/groups/:id/members",
        controller: "GroupMembersController",
        templateUrl: "/templates/pages/group-members.html",
        data: {
          authorizedRoles: [ USER_ROLES.admin, USER_ROLES.groupMember ]
        }
      } )
      .state( 'invite-group-members', {
        url: '/groups/:id/invite',
        controller: "GroupMembersController",
        templateUrl: '/templates/pages/group-members-invite.html',
        data: {
          authorizedRoles: [ USER_ROLES.admin, USER_ROLES.groupMember ]
        }
      } )
      .state( 'administrative-dashboard', {
        url: "/administrative-dashboard",
        templateUrl: "/templates/pages/administrative-dashboard.html",
        data: {
          authorizedRoles: [ USER_ROLES.admin ]
        }
      } )
      .state( 'dashboard-docs-list', {
        url: "/administrative-dashboard/docs",
        templateUrl: '/templates/pages/dashboard-docs-list.html',
        controller: 'DashboardDocumentsController',
        data: {
          authorizedRoles: [ USER_ROLES.admin ]
        }
      } )
      .state( 'dashboard-pages-list', {
        url: '/administrative-dashboard/pages',
        templateUrl: '/templates/pages/pages-list.html',
        controller: 'PagesListController',
        data: {
          authorizedRoles: [ USER_ROLES.admin ]
        }
      } )
      .state( 'dashboard-edit-page', {
        url: '/administrative-dashboard/pages/:id',
        templateUrl: '/templates/pages/page-edit.html',
        controller: 'PageEditController',
        data: {
          authorizedRoles: [ USER_ROLES.admin ]
        }
      } )
      .state( 'verify-account', {
        url: '/administrative-dashboard/verify-account',
        templateUrl: '/templates/pages/verify-account.html',
        controller: 'DashboardVerifyController',
        data: {
          authorizedRoles: [ USER_ROLES.admin ]
        }
      } )
      .state( 'verify-sponsors', {
        url: '/administrative-dashboard/verify-sponsors',
        templateUrl: '/templates/pages/verify-sponsors.html',
        controller: 'DashboardVerifySponsorsController',
        data: {
          authorizedRoles: [ USER_ROLES.admin ]
        }
      } )
      .state( 'site-settings', {
        url: '/administrative-dashboard/site-settings',
        templateUrl: '/templates/pages/site-settings.html',
        controller: 'SiteSettingsController',
        data: {
          authorizedRoles: [ USER_ROLES.admin ]
        }
      } )
      .state( 'user-edit', {
        url: '/user/edit/:id',
        templateUrl: '/templates/pages/user-edit.html',
        controller: 'UserEditPageController',
        data: {
          authorizedRoles: [ USER_ROLES.admin, USER_ROLES.independent, USER_ROLES.groupMember, USER_ROLES.basic ]
        }
      } )
      .state( 'user-sponsor-request', {
        url: '/user/sponsor',
        templateUrl: '/templates/pages/user-sponsor-request.html',
        controller: 'UserSponsorPageController',
        data: {
          authorizedRoles: [ USER_ROLES.all ]
        }
      } )
      .state( '404', {
        url: '/404',
        controller: '404Controller',
        templateUrl: '/templates/pages/404.html',
        data: {
          authorizedRoles: [ USER_ROLES.all ]
        }
      } );

      $futureStateProvider.stateFactory('customPage', function($q, futureState) {
        var state = {
          name: futureState.name,
          url: futureState.url,
          controller: 'ContentController',
          templateUrl: '/templates/pages/content.html',
          resolve: {
            thePage: function() {
              return futureState.page;
            }
          },
          data: {
            authorizedRoles: [ USER_ROLES.all ]
          }
        };

        return $q.when(state);
      });

      $futureStateProvider.addResolve(function($http) {
        return $http.get('/api/pages?external=false').then(function(resp) {

          angular.forEach(resp.data, function(page) {
            var fstate = {
              type: 'customPage',
              name: page.url,
              url: page.url,
              page: page
            };

            $futureStateProvider.futureState(fstate);
          });

        });
      });
  } ] );

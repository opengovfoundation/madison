angular.module( 'madisonApp' )
  .config( [ '$stateProvider', '$urlRouterProvider', 'USER_ROLES',
    function( $stateProvider, $urlRouterProvider, USER_ROLES ) {

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
      .state( 'verify-account', {
        url: '/administrative-dashboard/verify-account',
        templateUrl: '/templates/pages/verify-account.html',
        controller: 'DashboardVerifyController',
        data: {
          authorizedRoles: [ USER_ROLES.admin ]
        }
      } )
      .state( 'verify-group', {
        url: '/administrative-dashboard/verify-group',
        templateUrl: '/templates/pages/verify-group.html',
        controller: 'DashboardVerifyGroupController',
        data: {
          authorizedRoles: [ USER_ROLES.admin ]
        }
      } )
      .state( 'verify-independent', {
        url: '/administrative-dashboard/verify-independent',
        templateUrl: '/templates/pages/verify-independent.html',
        controller: 'DashboardVerifyUserController',
        data: {
          authorizedRoles: [ USER_ROLES.admin ]
        }
      } )
      .state( 'admin-notifications', {
        url: "/administrative-dashboard/notification-settings",
        templateUrl: '/templates/pages/admin-notifications.html',
        controller: 'DashboardNotificationsController',
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
      .state( 'user', {
        url: '/user/:id',
        templateUrl: '/templates/pages/user.html',
        controller: 'UserPageController',
        data: {
          authorizedRoles: [ USER_ROLES.all ]
        }
      } )
      .state( 'content', {
        url: '/{page:faq|about|privacy-policy|copyright|terms-and-conditions|404}',
        controller: 'ContentController',
        templateUrl: '/templates/pages/content.html',
        data: {
          authorizedRoles: [ USER_ROLES.all ]
        }
      } );
  } ] );

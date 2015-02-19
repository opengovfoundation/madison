angular.module('madisonApp')
  .config(['$stateProvider', '$urlRouterProvider', 'USER_ROLES', function ($stateProvider, $urlRouterProvider, USER_ROLES) {
    $urlRouterProvider.otherwise('404');

    $stateProvider
      .state('index', {
        url: "/",
        controller: "HomePageController",
        templateUrl: "/templates/pages/home.html",
        data: {
          title: "Madison Home",
          authorizedRoles: [USER_ROLES.all]
        }
      })
      .state('doc-page', {
        url: "/docs/:slug",
        controller: "DocumentPageController",
        templateUrl: "/templates/pages/doc.html",
        data: {
          title: "Document Page",
          authorizedRoles: [USER_ROLES.all]
        }
      })
      .state('my-documents', {
        url: "/documents",
        controller: 'MyDocumentsController',
        templateUrl: "/templates/pages/my-documents.html",
        data: {
          title: "My Documents",
          authorizedRoles: [USER_ROLES.admin, USER_ROLES.independent, USER_ROLES.groupMember]
        }
      })
      .state('login', {
        url: '/user/login',
        controller: "LoginPageController",
        templateUrl: "/templates/pages/login.html",
        data: {
          title: "Login to Madison",
          authorizedRoles: [USER_ROLES.all]
        }
      })
      .state('signup', {
        url: '/user/signup',
        controller: "SignupPageController",
        templateUrl: "/templates/pages/signup.html",
        data: {
          title: "Signup for Madison",
          authorizedRoles: [USER_ROLES.all]
        }
      })
      .state('password-reset-request', {
        url: '/password/reset',
        controller: 'PasswordResetController',
        templateUrl: '/templates/pages/password-reset-request.html',
        data: {
          title: 'Password Reset',
          authorizedRoles: [USER_ROLES.all]
        }
      })
      .state('password-reset-landing', {
        url: '/password/reset/:token',
        controller: 'PasswordResetLandingController',
        templateUrl: '/templates/pages/password-reset-landing.html',
        data: {title: 'Change Password'}
      })
      .state('resend-confirmation', {
        url: '/verification/resend',
        controller: 'ResendConfirmationController',
        templateUrl: '/templates/pages/resend-confirmation.html',
        data: {title: 'Resend Confirmation Email'}
      })
      .state('resend-confirmation-landing', {
        url: '/user/verify/:token',
        controller: 'ResendConfirmationController',
        data: {title: 'Verifying Email'}
      })
      .state('faq', {
        url: "/faq",
        templateUrl: "/templates/pages/faq.html",
        data: {title: "Frequently Asked Questions"}
      })
      .state('about', {
        url: "/about",
        templateUrl: "/templates/pages/about.html",
        data: {title: "About Madison"}
      })
      .state('privacy-policy', {
        url: '/privacy-policy',
        templateUrl: "/templates/pages/privacy-policy.html",
        data: {title: "Privacy Policy"}
      })
      .state('copyright', {
        url: '/copyright',
        templateUrl: "/templates/pages/copyright.html",
        data: {title: "Copyright Policy"}
      })
      .state('terms-and-conditions', {
        url: '/terms-and-conditions',
        templateUrl: "/templates/pages/terms-and-conditions.html",
        data: {title: "Terms and Conditions"}
      })
      .state('user-notification-settings', {
        url: "/user/edit/:user/notifications",
        controller: "UserNotificationsController",
        templateUrl: "/templates/pages/user-notification-settings.html",
        data: {title: "Notification Settings"}
      })
      .state('group-management', {
        url: "/groups",
        controller: "GroupManagementController",
        templateUrl: "/templates/pages/group-management.html",
        data: {title: "Group Management"}
      })
      .state('create-group', {
        url: "/groups/edit",
        controller: "GroupEditController",
        templateUrl: "/templates/pages/group-edit.html",
        data: {title: "Create Group"}
      })
      .state('edit-group', {
        url: "/groups/edit/:groupId",
        controller: "GroupEditController",
        templateUrl: "/templates/pages/group-edit.html",
        data: {title: "Edit Group"}
      })
      .state('manage-group-members', {
        url: "/groups/:id/members",
        controller: "GroupMembersController",
        templateUrl: "/templates/pages/group-members.html",
        data: {title: "Manage Group Members"}
      })
      .state('invite-group-members', {
        url: '/groups/:id/invite',
        controller: "GroupMembersController",
        templateUrl: '/templates/pages/group-members-invite.html',
        data: {title: "Invite Group Member"}
      })
      .state('administrative-dashboard', {
        url: "/administrative-dashboard",
        templateUrl: "/templates/pages/administrative-dashboard.html",
        data: {title: "Administrative Dashboard"}
      })
      .state('verify-account', {
        url: '/administrative-dashboard/verify-account',
        templateUrl: '/templates/pages/verify-account.html',
        controller: 'DashboardVerifyController',
        data: {title: 'Verify Account'}
      })
      .state('verify-group', {
        url: '/administrative-dashboard/verify-group',
        templateUrl: '/templates/pages/verify-group.html',
        controller: 'DashboardVerifyGroupController',
        data: {title: 'Verify Group'}
      })
      .state('verify-independent', {
        url: '/administrative-dashboard/verify-independent',
        templateUrl: '/templates/pages/verify-independent.html',
        controller: 'DashboardVerifyUserController',
        data: {title: 'Verify Independent Sponsor'}
      })
      .state('user', {
        url: '/user/:id',
        templateUrl: '/templates/pages/user.html',
        controller: 'UserPageController',
        data: {title: 'User Profile'}
      })
      .state('user-edit', {
        url: '/user/edit/:id',
        templateUrl: '/templates/pages/user-edit.html',
        controller: 'UserEditPageController',
        data: {title: 'Edit User Profile'}
      })
      .state('404', {
        url: '/404',
        templateUrl: '/templates/pages/404.html',
        data: {title: "Uh oh."}
      });
  }])
  .run(function ($rootScope, AUTH_EVENTS, AuthService) {
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
  });
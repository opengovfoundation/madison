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
      .state('edit-doc', {
        url: '/dashboard/docs/:id',
        controller: 'DashboardEditorController',
        templateUrl: '/templates/pages/edit-doc.html',
        data: {
          title: 'Edit Document',
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
      .state('facebook-login', {
        url: '/user/login/facebook-login',
        controller: function ($state, growl) {
          growl.success('Facebook login successful.');
          $state.go('index');
        },
        data: {
          authorizedRoles: [USER_ROLES.all]
        }
      })
      .state('twitter-login', {
        url: '/user/login/twitter-login',
        controller: function ($state, growl) {
          growl.success('Twitter login successful.');
          $state.go('index');
        },
        data: {
          authorizedRoles: [USER_ROLES.all]
        }
      })
      .state('linkedin-login', {
        url: '/user/login/linkedin-login',
        controller: function ($state, growl) {
          growl.success('LinkedIn login successful.');
          $state.go('index');
        },
        data: {
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
        data: {
          title: 'Change Password',
          authorizedRoles: [USER_ROLES.all]
        }
      })
      .state('resend-confirmation', {
        url: '/verification/resend',
        controller: 'ResendConfirmationController',
        templateUrl: '/templates/pages/resend-confirmation.html',
        data: {
          title: 'Resend Confirmation Email',
          authorizedRoles: [USER_ROLES.all]
        }
      })
      .state('resend-confirmation-landing', {
        url: '/user/verify/:token',
        controller: 'ResendConfirmationController',
        data: {
          title: 'Verifying Email',
          authorizedRoles: [USER_ROLES.all]
        }
      })
      .state('faq', {
        url: "/faq",
        templateUrl: "/templates/pages/faq.html",
        data: {
          title: "Frequently Asked Questions",
          authorizedRoles: [USER_ROLES.all]
        }
      })
      .state('about', {
        url: "/about",
        templateUrl: "/templates/pages/about.html",
        data: {
          title: "About Madison",
          authorizedRoles: [USER_ROLES.all]
        }
      })
      .state('privacy-policy', {
        url: '/privacy-policy',
        templateUrl: "/templates/pages/privacy-policy.html",
        data: {
          title: "Privacy Policy",
          authorizedRoles: [USER_ROLES.all]
        }
      })
      .state('copyright', {
        url: '/copyright',
        templateUrl: "/templates/pages/copyright.html",
        data: {
          title: "Copyright Policy",
          authorizedRoles: [USER_ROLES.all]
        }
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
        data: {
          title: "Notification Settings",
          authorizedRoles: [USER_ROLES.admin, USER_ROLES.independent, USER_ROLES.groupMember, USER_ROLES.basic]
        }
      })
      .state('group-management', {
        url: "/groups",
        controller: "GroupManagementController",
        templateUrl: "/templates/pages/group-management.html",
        data: {
          title: "Group Management",
          authorizedRoles: [USER_ROLES.admin, USER_ROLES.groupMember]
        }
      })
      .state('create-group', {
        url: "/groups/edit",
        controller: "GroupEditController",
        templateUrl: "/templates/pages/group-edit.html",
        data: {
          title: "Create Group",
          authorizedRoles: [USER_ROLES.admin, USER_ROLES.independent, USER_ROLES.groupMember, USER_ROLES.basic]
        }
      })
      .state('edit-group', {
        url: "/groups/edit/:groupId",
        controller: "GroupEditController",
        templateUrl: "/templates/pages/group-edit.html",
        data: {
          title: "Edit Group",
          authorizedRoles: [USER_ROLES.admin, USER_ROLES.groupMember]
        }
      })
      .state('manage-group-members', {
        url: "/groups/:id/members",
        controller: "GroupMembersController",
        templateUrl: "/templates/pages/group-members.html",
        data: {
          title: "Manage Group Members",
          authorizedRoles: [USER_ROLES.admin, USER_ROLES.groupMember]
        }
      })
      .state('invite-group-members', {
        url: '/groups/:id/invite',
        controller: "GroupMembersController",
        templateUrl: '/templates/pages/group-members-invite.html',
        data: {
          title: "Invite Group Member",
          authorizedRoles: [USER_ROLES.admin, USER_ROLES.groupMember]
        }
      })
      .state('administrative-dashboard', {
        url: "/administrative-dashboard",
        templateUrl: "/templates/pages/administrative-dashboard.html",
        data: {
          title: "Administrative Dashboard",
          authorizedRoles: [USER_ROLES.admin]
        }
      })
      .state('dashboard-docs-list', {
        url: "/administrative-dashboard/docs",
        templateUrl: '/templates/pages/dashboard-docs-list.html',
        controller: 'DashboardDocumentsController',
        data: {
          title: 'Create / Edit Documents',
          authorizedRoles: [USER_ROLES.admin]
        }
      })
      .state('verify-account', {
        url: '/administrative-dashboard/verify-account',
        templateUrl: '/templates/pages/verify-account.html',
        controller: 'DashboardVerifyController',
        data: {
          title: 'Verify Account',
          authorizedRoles: [USER_ROLES.admin]
        }
      })
      .state('verify-group', {
        url: '/administrative-dashboard/verify-group',
        templateUrl: '/templates/pages/verify-group.html',
        controller: 'DashboardVerifyGroupController',
        data: {
          title: 'Verify Group',
          authorizedRoles: [USER_ROLES.admin]
        }
      })
      .state('verify-independent', {
        url: '/administrative-dashboard/verify-independent',
        templateUrl: '/templates/pages/verify-independent.html',
        controller: 'DashboardVerifyUserController',
        data: {
          title: 'Verify Independent Sponsor',
          authorizedRoles: [USER_ROLES.admin]
        }
      })
      .state('admin-notifications', {
        url: "/administrative-dashboard/notification-settings",
        templateUrl: '/templates/pages/admin-notifications.html',
        controller: 'DashboardNotificationsController',
        data: {
          title: 'Notification Settings',
          authorizedRoles: [USER_ROLES.admin]
        }
      })
      .state('site-settings', {
        url: '/administrative-dashboard/site-settings',
        templateUrl: '/templates/pages/site-settings.html',
        controller: 'SiteSettingsController',
        data: {
          title: 'Administrative Site Settings',
          authorizedRoles: [USER_ROLES.admin]
        }
      })
      .state('user', {
        url: '/user/:id',
        templateUrl: '/templates/pages/user.html',
        controller: 'UserPageController',
        data: {
          title: 'User Profile',
          authorizedRoles: [USER_ROLES.all]
        }
      })
      .state('user-edit', {
        url: '/user/edit/:id',
        templateUrl: '/templates/pages/user-edit.html',
        controller: 'UserEditPageController',
        data: {
          title: 'Edit User Profile',
          authorizedRoles: [USER_ROLES.admin, USER_ROLES.independent, USER_ROLES.groupMember, USER_ROLES.basic]
        }
      })
      .state('404', {
        url: '/404',
        templateUrl: '/templates/pages/404.html',
        data: {
          title: "Uh oh.",
          authorizedRoles: [USER_ROLES.all]
        }
      });
  }]);

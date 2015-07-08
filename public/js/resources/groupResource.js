angular.module('madisonApp.resources')
  .factory("Group", function ($resource) {
    var Group = $resource("/api/groups/:id", [], {
      getMembers: {
        method: 'GET',
        url: "/api/groups/:id/members",
        isArray: true
      },
      getRoles: {
        method: 'GET',
        url: '/api/groups/roles',
        isArray: true
      },
      updateMemberRole: {
        method: 'PUT',
        url: '/api/groups/:id/members/:memberId',
        params: {id: '@id', memberId: '@memberId'}
      },
      inviteMember: {
        method: 'PUT',
        url: '/api/groups/:id/invite',
        params: {id: '@id'}
      },
      removeMember: {
        method: 'DELETE',
        url: '/api/groups/:id/members/:memberId',
        params: {id: '@id', memberId: '@memberId'}
      }
    });

    return Group;
  });
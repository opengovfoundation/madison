# ROUTES!

Routes are grouped by what kind of resource they *return*.

All routes prefixed with /api.

## Users
GET  /user/:id
GET  /user/current
PUT  /user/edit/:id
POST /user/ogin
GET  /user/logout
POST /user/signup
GET  /user/verify - get verified users
GET  /user/admin - all admins
POST /user/admin?admin - add user as admin
GET  /user/verify - gets user verification requests
POST /user/verify?request,status - update a verification request
GET  /user/independent/verify?params - get ind sponsor requests
POST /user/independent/verify?request,status - update an ind sponsor request
GET  /users/:id/support/:doc_id - get supported data for doc by user (confirm?)
POST /user/verify-email?token
PUT  /user/:id/edit/email?email,password - update email and password

## Sponsors (Users / Groups)
GET  /user/sponsors/all
PUT  /user/sponsor - update user contact info to become ind sponsor
GET  /docs/:id/sponsor
POST /docs/:id/sponsor

## Groups
GET  /groups
POST /groups
GET  /groups/:id
PUT  /groups/:id
DEL  /groups/:id
POST /groups/active/:id - set group as active (acting as)
POST /groups/active/0 - remove group as active
GET  /user/:id/groups

## Group Members
GET  /groups/:id/members
PUT  /groups/:id/members/:id - update group member
PUT  /groups/:id/invite - invite a user to a group
DEL  /groups/:id/members/:id - remove group member
GET  /groups/verify - get group verficiation requests
PUT  /groups/verify/:id - update group verification status (confirm?)

## Roles
GET  /groups/roles

## Documents
GET  /docs?title(other params too)
GET  /docs/all <--- ?!
GET  /api/docs/count
POST /docs
GET  /docs/:id
PUT  /docs/:id
DEL  /docs/:id
POST /docs/:id/content
DEL  /docs/:id/content/page
GET  /docs/slug/:slug - get doc by slug
GET  /docs/:id/content?format,page
GET  /user/:id/docs
GET  /docs/featured
PUT  /docs/featured?docs - doc order of featured docs
DEL  /docs/featured/:id
POST /docs/:id/title - save title
POST /docs/:id/publishstate - save publish state
POST /docs/:id/slug - save slug
POST /docs/:id/content/:page - save content for current page
GET  /docs/:id/introtext
POST /docs/:id/introtext
DEL  /docs/:id/featured-image
POST /docs/featured?id - save doc as a featured doc
DEL  /docs/featured/:id - remove doc as featured
PUT  /docs/:id/restore - restore a deleted document
GET  /docs/deleted?admin - get deleted documents (admin or not)
POST /docs/:id/support?supported - update supported status on doc
POST /docs/:id/:activity/:action - perform action, like / flag

## Statuses
GET  /docs/:id/status
GET  /docs/statuses
POST /docs/:id/status

## Dates
POST /docs/:id/dates - create a new date
DEL  /docs/:id/dates/:id
PUT  /dates/:id
GET  /docs/:id/dates

## Categories
GET  /docs/categories
GET  /docs/:id/categories
POST /docs/:id/categories

## Annotations
POST /docs/:id/annotations/:id/seen - notify author that annotation has been seen
POST /docs/:id/annotations/:id/likes - like an annotation
POST /docs/:id/annotations/:id/flags - flag an annotation
GET  /docs/:id/annotations
GET  /docs/:id/annotations/:id
POST /docs/:id/annotations
PUT  /docs/:id/annotations/:id
DEL  /docs/:id/annotations/:id
DEL  /docs/:id/annotations/search?search_params

## Comments
GET  /docs/:id/comments
GET  /docs/:id/comments?parent_id
POST /docs/:id/comments
POST /docs/:id/annotations/:id/comments - comment on an annotation

## Settings
GET  /settings/vendors - get vendor settings to install on client (GA, uservoice)

## Notifications
GET  /user/:id/notifications
PUT  /usr/:id/notifications

## Misc
POST /password/remind?email
POST /password/reset?email,password,password_confirmation,token
POST /verification/resend?email,password

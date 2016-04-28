# Documents Collection

[x] GET /docs/ -- DocumentController@getDocs
[ ] POST /docs/ -- DocumentController@postDocs
[ ] GET /docs/count -- DocumentController@getDocCount
[ ] GET /docs/{state} -- DocumentController@getDocs
[ ] POST /docs/featured -- DocumentController@postFeatured
[ ] PUT /docs/featured -- DocumentController@putFeatured
[ ] DELETE /docs/featured/{doc} -- DocumentController@deleteFeatured
[ ] GET /docs/recent/{query?} -- DocumentController@getRecent
[ ] GET /docs/categories -- DocumentController@getCategories
[ ] GET /docs/statuses -- DocumentController@getAllStatuses
[ ] GET /docs/sponsors -- DocumentController@getAllSponsors
[ ] GET /docs/featured -- DocumentController@getFeatured
[ ] GET /docs/deleted -- DocumentController@getDeletedDocs
[ ] PUT /dates/{date} -- DocumentController@putDate

# Single Doc Routes
[X] GET /docs/{doc} -- DocumentController@getDoc
[ ] PUT /docs/{doc} -- DocumentController@update
[ ] POST /docs/{doc}/support/ -- DocumentController@postSupport
[ ] GET /users/{user}/support/{doc} -- UserController@getSupport
[ ] GET /docs/{doc}/categories -- DocumentController@getCategories
[ ] GET /docs/{doc}/introtext -- DocumentController@getIntroText
[ ] GET /docs/{doc}/content -- DocumentController@getContent
[ ] GET /docs/{doc}/sponsor/{sponsor} -- DocumentController@hasSponsor
[ ] GET /docs/{doc}/sponsor -- DocumentController@getSponsor
[ ] GET /docs/{doc}/status -- DocumentController@getStatus
[ ] GET /docs/{doc}/dates -- DocumentController@getDates
[ ] GET /docs/{doc}/images/{image} -- DocumentController@getImage
[ ] GET /docs/slug/{slug} -- DocumentController@getDocBySlug
[ ] POST /docs/{doc}/introtext -- DocumentController@postIntroText
[ ] POST /docs/{doc}/title -- DocumentController@postTitle
[ ] POST /docs/{doc}/sponsor -- DocumentController@postSponsor
[ ] POST /docs/{doc}/publishstate -- DocumentController@postPublishState
[ ] POST /docs/{doc}/slug -- DocumentController@postSlug
[ ] POST /docs/{doc}/content -- DocumentController@postContent
[ ] PUT /docs/{doc}/content/{page} -- DocumentController@putContent
[ ] DELETE /docs/{doc}/content/{page} -- DocumentController@deleteContent
[ ] GET /docs/embed/{slug} -- DocumentController@getEmbedded
[ ] GET /docs/{slug}/feed -- DocumentController@getFeed
[ ] POST /docs/{doc}/featured-image -- DocumentController@uploadImage
[ ] POST /docs/{doc}/status -- DocumentController@postStatus
[ ] DELETE /docs/{doc} -- DocumentController@deleteDoc
[ ] PUT /docs/{doc}/restore -- DocumentController@getRestoreDoc
[ ] DELETE /docs/{doc}/featured-image -- DocumentController@deleteImage
[ ] DELETE /docs/{doc}/dates/{date} -- DocumentController@deleteDate
[ ] POST /docs/{doc}/dates -- DocumentController@postDate
[ ] POST /docs/{doc}/categories -- DocumentController@postCategories

# Annotation Action Routes
[ ] POST /docs/{doc}/annotations/{annotation}/likes -- AnnotationController@postLikes
[ ] POST /docs/{doc}/annotations/{annotation}/flags -- AnnotationController@postFlags
[ ] POST /docs/{doc}/annotations/{annotation}/seen -- AnnotationController@postSeen
[ ] GET /docs/{doc}/annotations/{annotation}/likes -- AnnotationController@getLikes
[ ] GET /docs/{doc}/annotations/{annotation}/flags -- AnnotationController@getFlags

# Annotation Comment Routes
[ ] GET /docs/{doc}/annotations/{annotation}/comments -- AnnotationController@getComments
[ ] POST /docs/{doc}/annotations/{annotation}/comments -- AnnotationController@postComments
[ ] GET /docs/{doc}/annotations/{annotation}/comments/{comment} -- AnnotationController@getComments

# Annotation Routes
[ ] GET /annotations/search -- AnnotationController@getSearch
[ ] GET /docs/{doc}/annotations/{annotation?} -- AnnotationController@getIndex
[ ] POST /docs/{doc}/annotations -- AnnotationController@postIndex
[ ] PUT /docs/{doc}/annotations/{annotation} -- AnnotationController@putIndex
[ ] DELETE /docs/{doc}/annotations/{annotation} -- AnnotationController@deleteIndex

# Document Comment Routes
[ ] POST /docs/{doc}/comments -- CommentController@postIndex
[ ] GET /docs/{doc}/comments -- CommentController@getIndex
[ ] GET /docs/{doc}/comments/{comment?} -- CommentController@getComment
[ ] POST /docs/{doc}/comments/{comment}/likes -- CommentController@postLikes
[ ] POST /docs/{doc}/comments/{comment}/flags -- CommentController@postFlags
[ ] POST /docs/{doc}/comments/{comment}/comments -- CommentController@postComments
[ ] POST /docs/{doc}/comments/{comment}/seen -- CommentController@postSeen

# User Routes
[ ] GET /user/{user} -- UserController@getUser
[ ] GET /user/verify/ -- UserController@getVerify
[ ] POST /user/verify/ -- UserController@postVerify
[ ] PUT /user/sponsor -- SponsorController@putRequest
[ ] GET /user/admin/ -- UserController@getAdmins
[ ] POST /user/admin/ -- UserController@postAdmin
[ ] GET /user/independent/verify/ -- UserController@getIndependentVerify
[ ] POST /user/independent/verify/ -- UserController@postIndependentVerify
[ ] GET /user/current -- UserController@getCurrent
[ ] PUT /user/{user}/edit/email -- UserController@editEmail
[ ] GET /user/{user}/docs -- DocumentController@getUserDocuments
[ ] GET /user/{user}/notifications -- UserController@getNotifications
[ ] PUT /user/{user}/notifications -- UserController@putNotifications
[ ] GET /user/{user}/groups -- UserController@getGroups
[ ] GET /user/facebook-login -- UserController@getFacebookLogin
[ ] GET /user/twitter-login -- UserController@getTwitterLogin
[ ] GET /user/linkedin-login -- UserController@getLinkedinLogin
[ ] PUT /user/edit/{user} -- UserController@putEdit
[ ] POST /user/verify-email -- UserController@postVerifyEmail
[ ] POST /password/remind -- RemindersController@postRemind
[ ] POST /password/reset -- RemindersController@postReset
[ ] GET /user/sponsors/all -- DocumentController@getAllSponsorsForUser
[ ] GET /sponsors/all -- SponsorController@getAllSponsors
[ ] POST /verification/resend -- RemindersController@postConfirmation

# Group Routes
[ ] GET /groups/verify/ -- GroupController@getVerify
[ ] PUT /groups/verify/{groupId} -- GroupController@putVerify
[ ] POST /groups/active/{groupId} -- GroupController@setActiveGroup
[ ] GET /groups/roles -- GroupController@getRoles
[ ] GET /groups/{group?} -- GroupController@getGroup
[ ] POST /groups/{group?} -- GroupController@postGroup
[ ] DELETE /groups/{groupId}/members/{memberId} -- GroupController@removeMember
[ ] PUT /groups/{groupId}/invite -- GroupController@processMemberInvite
[ ] GET /groups/{groupId}/members -- GroupController@getMembers
[ ] PUT /groups/{groupId}/members/{memberId} -- GroupController@putMember

# User Login / Signup AJAX requests
[ ] POST /user/login -- UserController@postLogin
[ ] POST /user/signup -- UserController@postSignup

# Auth Token Route
[ ] GET /user/login -- AuthController@login
[ ] GET /user/logout -- AuthController@logout
[ ] GET /auth/token -- AuthController@token

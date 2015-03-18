<?php

class MadisonEvent
{
    //Admin Notifications
    const NEW_USER_SIGNUP = "madison.user.signup";
    const DOC_EDITED = "madison.doc.edited";
    const DOC_COMMENTED = "madison.doc.commented";
    const DOC_ANNOTATED = "madison.doc.annotated";
    const DOC_COMMENT_COMMENTED = "madison.doc.comment.commented";
    const VERIFY_REQUEST_ADMIN = "madison.verification.admin";
    const VERIFY_REQUEST_GROUP = "madison.verification.group";
    const VERIFY_REQUEST_USER = "madison.verification.user";
    const NEW_DOCUMENT = "madison.doc.new";

    //User Notifications
    const NEW_GROUP_DOCUMENT = "madison.user.new_group_doc";
    const NEW_DOC_VERSION = "madison.user.new_doc_version";
    const NEW_ACTIVITY_VOTE = "madison.user.new_activity_vote";
    const NEW_ACTIVITY_COMMENT = "madison.user.new_activity_comment";

    //Other Events
    const DOC_SUBCOMMENT = "madison.doc.subcomment";

    /**
     *	Return viable admin notifications.
     *
     *	@param void
     *
     *	@return array
     */
    public static function validAdminNotifications()
    {
        return array(
            static::DOC_COMMENT_COMMENTED => "When a comment is made on a comment",
            static::DOC_COMMENTED => "When a document is commented on",
            static::DOC_ANNOTATED => "When a document is annotated",
            static::DOC_EDITED => "When a document is edited",
            static::NEW_DOCUMENT => "When a new document is created",
            static::NEW_USER_SIGNUP => "When a new user signs up",
            static::VERIFY_REQUEST_ADMIN => "When a new admin verification is requested",
            static::VERIFY_REQUEST_GROUP => "When a new group verification is requested",
            static::VERIFY_REQUEST_USER => "When a new independent author verification is requested",
        );
    }

    /**
     *	Return viable user notifications.
     *
     *	@param void
     *
     *	@return array
     */
    public static function validUserNotifications()
    {
        return array(
            static::NEW_GROUP_DOCUMENT => "When a group a user belongs to posts a new document",
            //static::NEW_DOC_VERSION => "When a new version of a document is posted that a user has interacted with",
            static::NEW_ACTIVITY_VOTE => "When an upvote / downvote is made on a user's post",
            static::NEW_ACTIVITY_COMMENT => "When a comment is posted on a user's activity",
        );
    }
}

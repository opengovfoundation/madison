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
	*	Return viable admin notifications
	*
	*	@param void
	*	@return array
	*/
	static function validAdminNotifications() {
		return array(
			static::DOC_COMMENT_COMMENTED => Lang::get('messages.commentoncomment'),
			static::DOC_COMMENTED => Lang::get('messages.commentondocument'),
			static::DOC_ANNOTATED => Lang::get('messages.commentondocannotated'),
			static::DOC_EDITED => Lang::get('messages.documentedited'),
			static::NEW_DOCUMENT => Lang::get('messages.newdoccreated'),
			static::NEW_USER_SIGNUP => Lang::get('messages.newusersignsup'),
			static::VERIFY_REQUEST_ADMIN => Lang::get('messages.newadminverifreq'),
			static::VERIFY_REQUEST_GROUP => Lang::get('messages.newgroupverifreq'),
			static::VERIFY_REQUEST_USER => Lang::get('messages.newindieverifreq')
		);
	}

	/**
	*	Return viable user notifications
	*
	*	@param void
	*	@return array
	*/
	static function validUserNotifications() {
		return array(
			static::NEW_GROUP_DOCUMENT => Lang::get('messages.newgroupdoc'),
			//static::NEW_DOC_VERSION => "When a new version of a document is posted that a user has interacted with",
			static::NEW_ACTIVITY_VOTE => Lang::get('messages.votepost'),
			static::NEW_ACTIVITY_COMMENT => Lang::get('messages.commentactivity')
		);
	}


}
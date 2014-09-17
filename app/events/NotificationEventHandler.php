<?php

class NotificationEventHandler
{

	const FROM_EMAIL_ADDRESS = 'sayhello@opengovfoundation.org';
	const FROM_EMAIL_NAME = 'Madison Email Robot';

	/**
	*	Array of event templates used for each notification event
	* 
	* @var array
	*/
	protected $eventEmailTemplates = array(
		MadisonEvent::NEW_USER_SIGNUP 				=> "email.notification.new_user",
		MadisonEvent::DOC_EDITED 							=> "email.notification.doc_edited",
		MadisonEvent::DOC_COMMENTED 					=> "email.notification.doc_commented",
		MadisonEvent::DOC_ANNOTATED						=> "email.notification.doc_annotated",
		MadisonEvent::DOC_COMMENT_COMMENTED 	=> "email.notification.comment_commented",
		MadisonEvent::VERIFY_REQUEST_ADMIN 		=> "email.notification.verify_admin",
		MadisonEvent::VERIFY_REQUEST_GROUP 		=> "email.notification.verify_request_group",
		MadisonEvent::VERIFY_REQUEST_USER 		=> "email.notification.verify_request_user",
		MadisonEvent::NEW_DOCUMENT 						=> "email.notification.new_document",
		MadisonEvent::NEW_ACTIVITY_COMMENT		=> "email.notification.user.new_activity_comment",
		MadisonEvent::NEW_ACTIVITY_VOTE				=> "email.notification.user.new_activity_vote"
	);
	
	/**
	*	Process notices to send out
	* 
	* @param array $notices
	* @param string $event
	* @return array $retval['type' => (Notification::<type>), 'email' => (string), 'template' => (string)]
	*/
	protected function processNotices($notices, $event)
	{
		if(!isset($this->eventEmailTemplates[$event])) {
			throw new Exception("No Email Template found for event");
		}
		
		$emailTemplate = $this->eventEmailTemplates[$event];
		
		$retval = array();

		foreach($notices as $notice) {
			switch($notice->type) {
				case Notification::TYPE_EMAIL:
					switch(true) {
						case isset($notice->group_id) && ($notice->group_id > 0):
							
							$users = Group::findUsersByRole(Group::ROLE_OWNER);
							
							foreach($users as $user) {
								$retval[] = array(
									'type' => Notification::TYPE_EMAIL,
									'email' => $user->email,
									'template' => $emailTemplate
								);
							}
							
							break;
						case isset($notice->user_id) && ($notice->user_id > 0):
							
							$user = User::where('id', '=', $notice->user_id)->first();
							
							if(!$user) {
								continue;
							}
							
							$retval[] = array(
								'type' => Notification::TYPE_EMAIL,
								'email' => $user->email,
								'template' => $emailTemplate
							);
							
							break;
						default: // Admin not a group or specific user
							
							$users = User::findByRoleName(Role::ROLE_ADMIN);
							
							foreach($users as $user) {
								$retval[] = array(
									'type' => Notification::TYPE_EMAIL,
									'email' => $user->email,
									'template' => $emailTemplate
								);
							}
							break;
					}
					break;
					
				case Notification::TYPE_TEXT:
					break;
			}
		}
		
		return $retval;
	}
	
	protected function doNotificationActions($notifications, $meta) 
	{
		foreach($notifications as $notification) {
			switch($notification['type']) {
				case Notification::TYPE_EMAIL:
					if(!Config::get('app.debug')) {
						Mail::queue(
							$notification['template'],
							$meta['data'],
							function($message) use($notification, $meta)
							{
								$message->subject($meta['subject']);
								$message->from($meta['from_email_address'], $meta['from_email_name']);
								$message->to($notification['email']);
							}
						);
					} 
						
					break;
				case Notification::TYPE_TEXT:
					break;
			}
		}
		
	}
	public function onNewUserSignup($data)
	{

		$notices = Notification::getActiveNotifications(MadisonEvent::NEW_USER_SIGNUP);
		
		$notifications = $this->processNotices($notices, MadisonEvent::NEW_USER_SIGNUP);
		
		$this->doNotificationActions($notifications, array(
			'data' => array('user' => $data->toArray()),
			'subject' => "New User has Signed up!",
			'from_email_address' => 'sayhello@opengovfoundation.org',
			'from_email_name' => 'Madison' 
		));
	}
	
	/**
	*	Method for handling comment notifications
	*	
	*	@param Comment $data
	*	@return null
	*/
	public function onDocCommented($data)
	{
		$notices = Notification::getActiveNotifications(MadisonEvent::DOC_COMMENTED);
		
		$notifications = $this->processNotices($notices, MadisonEvent::DOC_COMMENTED);

		$doc = Doc::find($data->doc_id);

		$this->doNotificationActions($notifications, array(
			'data' => array('comment' => $data->toArray(), 'doc' => $doc->toArray()),
			'subject' => "A new comment on a doc!",
			'from_email_address' => 'sayhello@opengovfoundation.org',
			'from_email_name' => 'Madison'
		));
	}

	/**
	*	Method for handling annotation notifications
	*	
	*	@param Annotation $data
	* @return null
	*/
	public function onDocAnnotated($data)
	{
		$notices = Notification::getActiveNotifications(MadisonEvent::DOC_ANNOTATED);
		$notifications = $this->processNotices($notices, MadisonEvent::DOC_ANNOTATED);

		$doc = Doc::find($data->doc_id);
		
		//Load annotation link
		$data->link = $data->getLink();

		$this->doNotificationActions($notifications, array(
			'data' 								=> array('annotation' => $data->toArray(), 'doc' => $doc->toArray()),
			'subject'							=> 'A new annotation on a document!',
			'from_email_address'	=> 'sayhello@opengovfoundation.org',
			'from_email_name'			=> 'Madison'
		));

	}
	
	public function onDocCommentCommented($data)
	{
		$notices = Notification::getActiveNotifications(MadisonEvent::DOC_COMMENT_COMMENTED);
		
		$notifications = $this->processNotices($notices, MadisonEvent::DOC_COMMENT_COMMENTED);
		
		$this->doNotificationActions($notifications, array(
			'data' => $data,
			'subject' => "A new comment on a doc comment!",
			'from_email_address' => 'sayhello@opengovfoundation.org',
			'from_email_name' => 'Madison'
		));
	}

	public function onDocSubcomment($subcomment, $activity){

		//Notify any admins watching for comments
		$this->onDocCommented($subcomment);
		
		//Notify the user if he's subscribed to updates
		$notice = Notification::where('user_id', '=', $activity['user_id'])
			->where('event', '=', MadisonEvent::NEW_ACTIVITY_COMMENT)
			->get();

		//If the user has subscribed to activity subcomments
		if(isset($notice)){
			$notification = $this->processNotices($notice, MadisonEvent::NEW_ACTIVITY_COMMENT);

			$this->doNotificationActions($notification, array(
				'data' => array("subcomment" => $subcomment->toArray(), "activity" => $activity->toArray()),
				'subject'	=> "A user has commented on your activity!",
				'from_email_address'	=> 'sayhello@opengovfoundation.org',
				'from_email_name'			=> 'Madison Email Robot'
			));
		}
	}

	public function onNewActivityVote($vote_type, $activity, $user){
		//Notify the user if he's subscribed to updates
		$notice = Notification::where('user_id', '=', $activity['user_id'])
			->where('event', '=', MadisonEvent::NEW_ACTIVITY_VOTE)
			->get();

		switch($vote_type){
			case 'like':
				$intro = 'Congrats';
				break;
			case 'dislike':
				$intro = 'Oops';
				break;
			default:
				$intro = 'Hey';
				break;
		}

		//If the user has subscribed to activity votes
		if(isset($notice)){
			$notification = $this->processNotices($notice, MadisonEvent::NEW_ACTIVITY_VOTE);

			$this->doNotificationActions($notification, array(
				'data'	=> array('intro' => $intro, 'vote_type' => $vote_type, 'activity' => $activity->toArray(), 'user' => $user->toArray()),
				'subject'	=> 'A user has voted on your activity!',
				'from_email_address'	=> static::FROM_EMAIL_ADDRESS,
				'from_email_name'			=> static::FROM_EMAIL_NAME
			));
		}

	}
	
	public function onDocEdited($data)
	{
		$notices = Notification::getActiveNotifications(MadisonEvent::DOC_EDITED);
		
		$notifications = $this->processNotices($notices, MadisonEvent::DOC_EDITED);
		
		$this->doNotificationActions($notifications, array(
			'data' => array('doc' => $data->toArray()),
			'subject' => "A document has been edited!",
			'from_email_address' => 'sayhello@opengovfoundation.org',
			'from_email_name' => 'Madison'
		));
	}
	
	public function onNewDocument($data)
	{
		$notices = Notification::getActiveNotifications(MadisonEvent::NEW_DOCUMENT);
		
		$notifications = $this->processNotices($notices, MadisonEvent::NEW_DOCUMENT);
		
		$this->doNotificationActions($notifications, array(
			'data' => array('doc' => $data->toArray()),
			'subject' => "A document has been created!",
			'from_email_address' => 'sayhello@opengovfoundation.org',
			'from_email_name' => 'Madison'
		));
	}
	
	public function onVerifyAdminRequest($data)
	{
		$notices = Notification::getActiveNotifications(MadisonEvent::VERIFY_REQUEST_ADMIN);
		
		$notifications = $this->processNotices($notices, MadisonEvent::VERIFY_REQUEST_ADMIN);
		
		$this->doNotificationActions($notifications, array(
			'data' => $data,
			'subject' => "An admin requests verification!",
			'from_email_address' => 'sayhello@opengovfoundation.org',
			'from_email_name' => 'Madison'
		));
	}
	
	public function onVerifyGroupRequest($data)
	{
		$notices = Notification::getActiveNotifications(MadisonEvent::VERIFY_REQUEST_GROUP);
		
		$notifications = $this->processNotices($notices, MadisonEvent::VERIFY_REQUEST_GROUP);
		
		$this->doNotificationActions($notifications, array(
			'data' => array('group' => $data->toArray()),
			'subject' => "A group requests verification!",
			'from_email_address' => 'sayhello@opengovfoundation.org',
			'from_email_name' => 'Madison'
		));
	}
	
	public function onVerifyUserRequest($data)
	{
		$notices = Notification::getActiveNotifications(MadisonEvent::VERIFY_REQUEST_USER);
		
		$notifications = $this->processNotices($notices, MadisonEvent::VERIFY_REQUEST_USER);
		
		$this->doNotificationActions($notifications, array(
			'data' => array('user' => $data->toArray()),
			'subject' => "An individual requests verification!",
			'from_email_address' => 'sayhello@opengovfoundation.org',
			'from_email_name' => 'Madison'
		));
	}
	
	public function subscribe($eventManager)
	{
		$eventManager->listen(MadisonEvent::NEW_USER_SIGNUP, 'NotificationEventHandler@onNewUserSignup');
		$eventManager->listen(MadisonEvent::DOC_COMMENT_COMMENTED, 'NotificationEventHandler@onDocCommentCommented');
		$eventManager->listen(MadisonEvent::DOC_COMMENTED, 'NotificationEventHandler@onDocCommented');
		$eventManager->listen(MadisonEvent::DOC_ANNOTATED, 'NotificationEventHandler@onDocAnnotated');
		$eventManager->listen(MadisonEvent::DOC_EDITED, 'NotificationEventHandler@onDocEdited');
		$eventManager->listen(MadisonEvent::NEW_DOCUMENT, 'NotificationEventHandler@onNewDocument');
		$eventManager->listen(MadisonEvent::VERIFY_REQUEST_ADMIN, 'NotificationEventHandler@onVerifyAdminRequest');
		$eventManager->listen(MadisonEvent::VERIFY_REQUEST_GROUP, 'NotificationEventHandler@onVerifyGroupRequest');
		$eventManager->listen(MadisonEvent::VERIFY_REQUEST_USER, 'NotificationEventHandler@onVerifyUserRequest');
		$eventManager->listen(Madisonevent::DOC_SUBCOMMENT, 'NotificationEventHandler@onDocSubcomment');
		$eventManager->listen(MadisonEvent::NEW_ACTIVITY_VOTE, 'NotificationEventHandler@onNewActivityVote');
	}
}
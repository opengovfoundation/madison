<?php

class NotificationEventHandler
{
	protected $eventEmailTemplates = array(
		OpenGovEvent::NEW_USER_SIGNUP => "email.notification.newuser",
	);
	
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
							array('user' => $meta['data']),
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
		$notices = Notification::getActiveNotifications(OpenGovEvent::NEW_USER_SIGNUP);
		
		$notifications = $this->processNotices($notices, OpenGovEvent::NEW_USER_SIGNUP);
		
		$this->doNotificationActions($notifications, array(
			'data' => $data,
			'subject' => "New User has Signed up!",
			'from_email_address' => 'sayhello@opengovfoundation.org',
			'from_email_name' => 'Madison' 
		));
	}
	
	public function onDocCommented($data)
	{
		$notices = Notification::getActiveNotifications(OpenGovEvent::DOC_COMMENTED);
		
		$notifications = $this->processNotices($notices, OpenGovEvent::DOC_COMMENTED);
		
		$this->doNotificationActions($notifications, array(
			'data' => $data,
			'subject' => "A new comment on a doc!",
			'from_email_address' => 'sayhello@opengovfoundation.org',
			'from_email_name' => 'Madison'
		));
	}
	
	public function onDocCommentCommented($data)
	{
		$notices = Notification::getActiveNotifications(OpenGovEvent::DOC_COMMENT_COMMENTED);
		
		$notifications = $this->processNotices($notices, OpenGovEvent::DOC_COMMENT_COMMENTED);
		
		$this->doNotificationActions($notifications, array(
			'data' => $data,
			'subject' => "A new comment on a doc comment!",
			'from_email_address' => 'sayhello@opengovfoundation.org',
			'from_email_name' => 'Madison'
		));
	}
	
	public function onDocEdited($data)
	{
		$notices = Notification::getActiveNotifications(OpenGovEvent::DOC_EDITED);
		
		$notifications = $this->processNotices($notices, OpenGovEvent::DOC_EDITED);
		
		$this->doNotificationActions($notifications, array(
			'data' => $data,
			'subject' => "A document has been edited!",
			'from_email_address' => 'sayhello@opengovfoundation.org',
			'from_email_name' => 'Madison'
		));
	}
	
	public function onNewDocument($data)
	{
		$notices = Notification::getActiveNotifications(OpenGovEvent::NEW_DOCUMENT);
		
		$notifications = $this->processNotices($notices, OpenGovEvent::NEW_DOCUMENT);
		
		$this->doNotificationActions($notifications, array(
			'data' => $data,
			'subject' => "A document has been created!",
			'from_email_address' => 'sayhello@opengovfoundation.org',
			'from_email_name' => 'Madison'
		));
	}
	
	public function onVerifyAdminRequest($data)
	{
		$notices = Notification::getActiveNotifications(OpenGovEvent::VERIFY_REQUEST_ADMIN);
		
		$notifications = $this->processNotices($notices, OpenGovEvent::VERIFY_REQUEST_ADMIN);
		
		$this->doNotificationActions($notifications, array(
			'data' => $data,
			'subject' => "An admin requests verification!",
			'from_email_address' => 'sayhello@opengovfoundation.org',
			'from_email_name' => 'Madison'
		));
	}
	
	public function onVerifyGroupRequest($data)
	{
		$notices = Notification::getActiveNotifications(OpenGovEvent::VERIFY_REQUEST_GROUP);
		
		$notifications = $this->processNotices($notices, OpenGovEvent::VERIFY_REQUEST_GROUP);
		
		$this->doNotificationActions($notifications, array(
			'data' => $data,
			'subject' => "A group requests verification!",
			'from_email_address' => 'sayhello@opengovfoundation.org',
			'from_email_name' => 'Madison'
		));
	}
	
	public function onVerifyUserRequest($data)
	{
		$notices = Notification::getActiveNotifications(OpenGovEvent::VERIFY_REQUEST_USER);
		
		$notifications = $this->processNotices($notices, OpenGovEvent::VERIFY_REQUEST_USER);
		
		$this->doNotificationActions($notifications, array(
			'data' => $data,
			'subject' => "An individual requests verification!",
			'from_email_address' => 'sayhello@opengovfoundation.org',
			'from_email_name' => 'Madison'
		));
	}
	
	public function onTestEvent($data)
	{
		
	}
	
	public function subscribe($eventManager)
	{
		$eventManager->listen(OpenGovEvent::TEST, 'NotificationEventHandler@onNewUserSignup');
		
		$eventManager->listen(OpenGovEvent::NEW_USER_SIGNUP, 'NotificationEventHandler@onNewUserSignup');
		$eventManager->listen(OpenGovEvent::DOC_COMMENT_COMMENTED, 'NotificationEventHandler@onDocCommentCommented');
		$eventManager->listen(OpenGovEvent::DOC_COMMENTED, 'NotificationEventHandler@onDocCommented');
		$eventManager->listen(OpenGovEvent::DOC_EDITED, 'NotificationEventHandler@onDocEdited');
		$eventManager->listen(OpenGovEvent::NEW_DOCUMENT, 'NotificationEventHandler@onNewDocument');
		$eventManager->listen(OpenGovEvent::VERIFY_REQUEST_ADMIN, 'NotificationEventHandler@onVerifyAdminRequest');
		$eventManager->listen(OpenGovEvent::VERIFY_REQUEST_GROUP, 'NotificationEventHandler@onVerifyGroupRequest');
		$eventManager->listen(OpenGovEvent::VERIFY_REQUEST_USER, 'NotificationEventHandler@onVerifyUserRequest');
	}
}
<?php

return [
    // General Stuff
    'created' => 'Created',
    'updated' => 'Updated',
    'update_failed' => 'Update failed',
    'readmore' => 'Read More',
    'submit' => 'Submit',
    'clear' => 'Clear',
    'submit' => 'Submit',
    'edit' => 'Edit',
    'delete' => 'Delete',
    'remove' => 'Remove',
    'cancel' => 'Cancel',
    'close' => 'Close',
    'order' => 'Order',
    'order_by' => 'Order By',
    'order_by_direction' => 'Order By Direction',
    'order_by_dir_desc' => 'Descending',
    'order_by_dir_asc' => 'Ascending',
    'limit' => 'Limit',
    'pagination' => 'Showing :start to :end of :total entries',
    'actions' => 'Actions',
    'id' => 'Id',
    'login' => 'Login',
    'logout' => 'Logout',
    'register' => 'Register',
    'open' => 'Open',
    'restore' => 'Restore',
    'none' => 'None',
    'close' => 'Close',
    'permalink' => 'Permalink',
    'administrator' => 'Admin',
    'email_verified' => 'Email Verified',
    'misc' => 'Misc.',
    'settings' => 'Settings',
    'type' => 'Type',
    'view' => 'View',
    'save' => 'Save',
    'add' => 'Add',
    'new' => 'New',
    'save' => 'Save',
    'get_started' => 'Get Started',
    'learn_more' => 'Learn more',
    'relevance' => 'Relevance',
    'relevance_ordering_warning' => 'Ordering by relevance only works with a search query, default order has been used.',
    'manage' => 'Manage',

    // Authentication Stuff
    'password' => 'Password',
    'confirm_password' => 'Confirm Password',
    'remember_me' => 'Remember Me',
    'forgot_password' => 'Forgot Your Password?',
    'reset_password' => 'Reset Password',
    'reset_password_send' => 'Send Password Reset Link',

    'email_verification' => [
        'banner' => 'You haven\'t verified your email address yet. We need you to verify it before we can send messages to it. Please check your inbox or :resendLinkOpen resend the verification email :resendLinkClose.',
        'ask' => 'Please verify this email address',
        'reason' => 'Before we can send you notifications and otherwise interact over email, we need you to verify you email. Just click the button below.',
        'action' => 'Verify email',
        'verified' => 'Your email has been verified',
        'already_verified' => 'Your email has already been verified',
        'sent' => 'Email verification sent',
    ],

    // Home Page Stuff
    'home' => [
        'home' => 'Home',
        'welcome' => 'Add your voice',
        'intro' => 'Legislation and regulations that impact your life are written every day. With Madison, you can work directly <em>with</em> the sponsors of these documents.',
        'how_it_works' => [
            'title' => 'How it Works',
            'step1' => 'Elected officials share policy documents on Madison for public collaboration.',
            'step2' => 'The people can read the documents, vote on the documents, and leave feedback on the documents, and their comments are sent <strong>directly to the officials</strong>.',
            'step3' => 'Officials respond to these comments and even make changes to the documents based on the feedback.',
        ],
        'sponsor_cta' => [
            'title' => 'Have a document to share?',
            'text' => 'You can easily request to become a document sponsor and add the public\'s voice and credibility to your proposal.',
            'action_text' => 'Learn about how to become a sponsor.',
        ],
        'featured_title' => 'Featured',
        'popular_title' => 'Popular',
        'all_documents' => 'View all documents',
    ],

    'search' => [
        'title' => 'Search all documents',
        'placeholder' => 'Search documents',
    ],

    'activelegislation' => 'Active Legislation',
    'recentactivity' => 'Recent Activity',
    'mostactive' => 'Most Active Documents',
    'recentlegislation' => 'Recent Legislation',


    // Document Stuff
    'document' => [
        'read' => 'Read this document',
        'title' => 'Title',
        'title_invalid' => 'Can\'t create document with that name, please try another.',
        'slug' => 'Slug',
        'slug_help' => 'You can change the slug of your document. It is strongly suggested that you not change this after you have published your document.',
        'introtext' => 'Introduction',
        'content' => 'Content',
        'sponsor' => 'Sponsor',
        'sponsor_others' => 'and others',

        'activity' => 'Activity',
        'participants' => 'Participants',
        'comment' => 'Comment',
        'comments' => 'Comments',
        'add_comment' => 'Write a comment...',
        'login_to_comment' => 'Login to comment',
        'see_replies' => 'See replies (:count)',
        'note' => 'Note',
        'note_edit_explanation_prompt' => 'Why did you make this edit?',
        'notes' => 'Notes',
        'download_comments_csv' => 'Download Comments (as CSV)',
        'collaborators' => 'Collaborators',
        'collaborators_count' => ':count collaborators',
        'sponsoredby' => 'Shared for feedback by :sponsors',
        'featured' => 'Featured',
        'featured_image' => 'Featured Image',
        'featured_image_remove' => 'Remove Featured Image',
        'featured_image_removed' => 'Featured Image Removed',
        'replies_count' => ':count replies',
        'replies' => 'Replies',
        'add_reply' => 'Write a reply...',
        'note_reply' => 'Reply to this note',
        'support_prompt' => 'What do you think of this document?',
        'support' => 'Support',
        'oppose' => 'Oppose',
        'supported' => 'Supported',
        'opposed' => 'Opposed',
        'flag' => 'Flag',
        'like' => 'Like',
        'moderate' => 'Moderate',
        'moderate_document' => 'Moderate: :document',
        'hide_comment' => 'Hide',
        'hidden_comment' => 'Hidden',
        'resolve_comment' => 'Resolve',
        'resolved_comment' => 'Resolved',
        'comment_hide_success' => 'Comment hidden',
        'comment_resolve_success' => 'Comment marked as resolved',
        'comment_action_removed' => 'Comment action removed',
        'comments_handled' => 'Handled Comments',
        'comments_unhandled' => 'Unhandled Comments',

        'created' => 'Document created.',
        'deleted' => 'Document deleted. :restoreLinkOpen Undo :restoreLinkClosed',
        'restored' => 'Document restored. It is currently unpublished.',
        'updated' => 'Document updated.',
        'page_added' => 'Page added.',

        'update_support' => 'Updated your support on this document',

        'publish_state' => 'Publish State',
        'publish_state_short' => 'Publish',
        'publish_state_help' => 'If a document is set to "unpublished", only sponsors can view it. If it is set to private, anyone with the link can view it. Otherwise it is publicly viewable.',
        'publish_states' => [
            'all' => 'All',
            \App\Models\Doc::PUBLISH_STATE_PUBLISHED => 'Published',
            \App\Models\Doc::PUBLISH_STATE_UNPUBLISHED => 'Unpublished',
            \App\Models\Doc::PUBLISH_STATE_PRIVATE => 'Private',
            \App\Models\Doc::PUBLISH_STATE_DELETED_ADMIN => 'Deleted (by admin)',
            \App\Models\Doc::PUBLISH_STATE_DELETED_USER => 'Deleted (by user)',
        ],

        'discussion_state' => 'Discussion State',
        'discussion_state_short' => 'Discussion',
        'discussion_state_help' => 'If commenting is set to "open", users will be able to comment and make notes normally. If it is "closed", then existing comments will be viewable, but new comments can not happen. If it is set to "hidden", no new commenting can happen, and existing comments will not be viewable.',
        'discussion_states' => [
            \App\Models\Doc::DISCUSSION_STATE_OPEN => 'Open',
            \App\Models\Doc::DISCUSSION_STATE_CLOSED => 'Closed',
            \App\Models\Doc::DISCUSSION_STATE_HIDDEN => 'Hidden',
        ],

        'create_as' => 'Sponsor As',
        'add_page' => 'Add Page',

        'list' => 'Documents',
        'create' => 'Create Document',
        'new' => 'New Document',
        'edit' => 'Edit Document',
        'save' => 'Save Document',
        'view' => 'View Document',
        'manage' => 'Manage Document',
    ],

    // Sponsor Stuff
    'sponsor' => [
        'become' => 'Become a Sponsor',
        'info' => [
            'introtext' => 'Sponsors are individuals and organizations that want to share a document with the public and hear their opinions using Madison. Add the public\'s voice and credibility to your proposal. It\'s simple, fast, and free.',
            'step1' => 'First :linkOpen create an account :linkClose on Madison.',
            'step2' => 'Login and :linkOpen request to become a sponsor :linkClose. A sponsor can be you as an individual or an organization to which you belong.',
            'step3' => 'Madison administrators will receive your request and contact you with two business days to verify your request.',
            'step4' => 'Once approved, Madison administrators will help you make a plan to promote your document so you get the best public engagement possible.',
            'step5' => 'With a plan in place, you can :linkOpen publish a document :linkClose and hear the opinions of the public.',
        ],
        'waiting_approval' => [
            'page_title' => 'Sponsor Awaiting Approval',
            'msg_header' => 'Your Sponsor Request Is Awaiting Approval',
            'msg_lead' => 'Thank you! You are one step closer to adding the public\'s voice and credibility to your document.',
            'msg_body' => 'Madison administrators will be contact you within two business days to verify your request.',
        ],
        'create_help' => [
            'what_is_a_sponsor' => 'Sponsors are individuals and organizations that want to share a document with the public and hear their opinions using Madison. Add the public\'s voice and credibility to your proposal.',
            'next_steps' => 'Completing this form will submit your request to the Madison administrators to authorize your sponsor account. You will be contacted within two business days to verify your request.',
        ],
        'not_a_sponsor' => [
            'title' => 'You are not a sponsor.',
            'body' => 'Sponsors are individuals and organizations that want to share a document with the public and hear their opinions using Madison. Add the public\'s voice and credibility to your proposal. It\'s simple, fast, and free.',
        ],
        'page_title_documents' => 'Documents | :sponsorName',
        'page_title_members' => 'Members | :sponsorName',
        'page_title_settings' => 'Settings | :sponsorName',
        'list' => 'Sponsors',
        'create' => 'Create Sponsor',
        'create_a' => 'Create a Sponsor',
        'my_sponsors' => 'My Sponsors',
        'all_sponsors' => 'All Sponsors',
        'edit' => 'Edit Sponsor',
        'view_docs' => 'Documents',

        'create_another' => 'Create Another Sponsor',
        'create_another_header' => 'Need another sponsor?',
        'create_another_body' => 'It doesn\'t happen often, but some people need create more than one sponsor account. Don\'t worry, the process is the same.',

        'member' => 'Member',
        'members' => 'Members',

        'name' => 'Name',
        'display_name' => 'Display Name',
        'internal_name' => 'Internal Name',

        'status' => 'Status',
        'statuses' => [
            \App\Models\Sponsor::STATUS_ACTIVE => 'Approved',
            \App\Models\Sponsor::STATUS_PENDING => 'Pending',
        ],

        'status_updated' => 'Status Updated',

        'updated' => 'Sponsor updated.',
        'update_failed' => 'Sponsor update failed.',

        'create' => 'Create a New Sponsor',
        'created' => 'Sponsor created',
        'create_failed' => 'Sponsor creation failed.',
    ],

    'sponsor_member' => [
        'add' => 'Add a Member',
        'add_user' => 'Add User',
        'create' => 'New Sponsor Member',
        'role_updated' => 'Sponsor member role updated',

        'list' => 'Sponsor Members',
        'role' => 'Role',

        'joined' => 'Joined',

        'failed_invalid_email' => 'User not found by that email address.',
        'failed_already_member' => 'That user is already a member of this sponsor',

        'created' => 'Member successfully added to Sponsor',
        'removed' => 'Member successfully removed from Sponsor',
        'need_owner' => 'A sponsor must have at least one owner',

        'roles' => [
            'owner' => 'Owner',
            'editor' => 'Editor',
            'staff' => 'Staff',
        ],
    ],

    'info' => [
        'url' => 'Website',
        'phone' => 'Phone Number',
        'address1' => 'Address 1',
        'address2' => 'Address 2',
        'city' => 'City',
        'state' => 'State',
        'postal_code' => 'Zip Code',
    ],

    'user' => [
        'user' => 'User',
        'fname' => 'First Name',
        'lname' => 'Last Name',
        'email' => 'Email Address',
        'email_help' => 'Email will not be displayed publicly.',
        'new_password' => 'New Password',
        'new_password_confirmation' => 'Re-Enter New Password',
        'verification_info' => 'Verification Information',
        'make_admin' => 'Make Admin',
        'remove_admin' => 'Remove Admin',
        'avatar_alt_text' => 'User profile image',

        'settings' => 'User Settings',
        'settings_pages' => [
            'notifications' => 'Notifications',
            'password' => 'Password',
            'account' => 'Account',
        ],
    ],

    'page' => [
        'create' => 'New Page',
        'edit' => 'Edit Page',
        'updated' => 'Page updated',
        'deleted' => 'Page deleted',

        'title' => 'Title',
        'url' => 'URL',
        'nav_title' => 'Navigation Title',
        'page_title' => 'Page Title',
        'header' => 'Page Header',
        'show_in_header' => 'Header',
        'show_in_footer' => 'Footer',
        'external' => 'External?',
    ],

    'notifications' => [
        // boilerplate stuff
        'greeting' => [
            'normal' => 'Hello!',
            'error' => 'Whoops!',
        ],
        'salutation' => 'Regards,<br>:name',
        'having_trouble' => 'If you\'re having trouble clicking the ":actionText" button, copy and paste the URL below into your web browser:',
        'thank_you' => 'Thank you for using our application!',
        'unsubscribe' => 'If you do not wish to receive these notifications, you can unsubscribe in your user settings.',

        'groups' => [
            'user' => 'User Notifications',
            'sponsor' => 'Sponsor Notifications',
            'admin' => 'Admin Notifications',
        ],

        // actual notification content
        'madison' => [
            'comment' => [
                'replied' => [
                    'preference_description' => 'When someone posts a reply to a comment of yours',

                    'subject' => ':name replied to your :comment_type on :document',
                ],

                'flagged' => [
                    'preference_description' => 'When someone flags a comment on your sponsored documents',
                    'subject' => 'Someone flagged a :comment_type on :document.',
                ],

                'liked' => [
                    'preference_description' => 'When someone likes a comment of yours',
                    'subject' => 'Someone liked your :comment_type on :document.',
                ],

                'created_on_sponsored' => [
                    'preference_description' => 'When someone makes a comment on a document you sponsored',

                    'subject' => ':name has created a :comment_type on :document',
                ],
            ],

            'document' => [
                'support_vote_changed' => [
                    'preference_description' => 'When someone votes on a document you sponsored',

                    'vote_support' => 'A user voted in support of :document.',
                    'vote_oppose' => 'A user voted in opposition of :document.',
                    'vote_support_from_oppose' => 'A user changed their vote to support :document.',
                    'vote_oppose_from_support' => 'A user changed their vote to oppose :document.',
                ],

                'published' => [
                    'preference_description' => 'When new documents are published',
                    'subject' => ':document has been published',
                ],
            ],

            'sponsor' => [
                'needs_approval' => [
                    'preference_description' => 'When a sponsor needs approval',

                    'subject' => 'The sponsor :name needs approval.',
                ],
            ],

            'user' => [
                'sponsor_membership_changed' => [
                    'preference_description' => 'When you are added to or removed from a sponsor',
                    'added_to_sponsor' => ':name has added you to :sponsor with a role of :role',
                    'removed_from_sponsor' => ':name has removed you from :sponsor',
                ],
                'sponsor_role_changed' => [
                    'preference_description' => 'When your role in a sponsor is changed',
                    'subject' => ':name changed your role for :sponsor from :old_role to :new_role',
                ],
            ],
        ],

        // helpers
        'comment_type_note' => 'note',
        'comment_type_comment' => 'comment',
        'see_comment' => 'See :Comment_type',
        'see_sponsor' => 'See Sponsor',
        'see_document' => 'See Document',
        'review_sponsor' => 'Review Sponsor',
    ],

    'admin' => [
        'admin_label' => 'Site Administration: :page',
        'featured_documents' => 'Featured Documents',
        'manage_users' => 'Manage Users',
        'manage_sponsors' => 'Manage Sponsors',
        'updated_featured_documents' => 'Feature documents updated',
        'site_settings' => 'Site Settings',
        'custom_pages' => 'Custom Pages',
        'pages' => 'Pages',
        'add_featured_document' => 'Add Featured Document',

        'setting_groups' => [
            'date_time' => 'Date & Time',
            'google_analytics' => 'Google Analytics',
        ],
        'madison' => [
            'date_format' => 'Date Format',
            'time_format' => 'Time Format',
            'google_analytics_property_id' => 'Google Analytics Key',
            'google_analytics_property_id_help' => 'The UA-XXXXX-Y string identifying which Google Analytics property you wish to track traffic against',
        ],
    ],
];

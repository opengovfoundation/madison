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
    'close' => 'Close',
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
    'admin' => 'Admin',

    // Authentication Stuff
    'password' => 'Password',
    'confirm_password' => 'Confirm Password',
    'remember_me' => 'Remember Me',
    'forgot_password' => 'Forgot Your Password?',
    'reset_password' => 'Reset Password',
    'reset_password_send' => 'Send Password Reset Link',

    // Home Page Stuff
    'home' => [
        'welcome' => 'Add your voice...',
        'intro' => 'Find legislation and regulations being developed and get informed. Then get involved and collaboratively craft the documents with the sponsors. To get started, choose from the recent documents below, or search the whole repository.',
    ],

    'searchdisplay' => 'Search: ',

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
        'title' => 'Title',
        'title_invalid' => 'Can\'t create document with that name, please try another.',
        'slug' => 'Custom URL (slug)',
        'slug_help' => 'Will automatically be set based on title if blank. Only the letters a through z (lowercase), numbers 0 through 9, and hyphens (-) may be used.  For example: my-document',
        'introtext' => 'Intro Text',
        'category' => 'Category',
        'categories' => 'Categories: ',
        'sponsor' => 'Sponsor',
        'sponsor_others' => 'and others',
        'content' => 'Content',

        'activity' => 'Activity',
        'participants' => 'Participants',
        'comments' => 'Comments',
        'add_comment' => 'Add comment',
        'login_to_comment' => 'Login to comment',
        'note' => 'Note',
        'note_edit_explanation_prompt' => 'Why did you make this edit?',
        'notes' => 'Notes',
        'collaborators' => 'Collaborators',
        'collaborators_count' => ':count collaborators',
        'sponsoredby' => 'Sponsored by',
        'featured' => 'Featured',
        'featured_image' => 'Featured Image',
        'featured_image_remove' => 'Remove Featured Image',
        'featured_image_removed' => 'Featured Image Removed',
        'replies_count' => ':count replies',
        'replies' => 'Replies',
        'add_reply' => 'Add your reply',
        'note_reply' => 'Reply to this note',
        'support' => 'Support',
        'oppose' => 'Oppose',
        'supported' => 'Supported',
        'opposed' => 'Opposed',
        'flag' => 'Flag',
        'like' => 'Like',

        'created' => 'Document created.',
        'deleted' => 'Document deleted. :restoreLinkOpen Undo :restoreLinkClosed',
        'restored' => 'Document restored. It is currently unpublished.',
        'updated' => 'Document updated.',
        'page_added' => 'Page added.',

        'update_support' => 'Updated your support on this document',

        'publish_state' => 'Publish State',
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
        'edit' => 'Edit Document',
    ],

    // Sponsor Stuff
    'sponsor' => [
        'list' => 'Sponsors',
        'create' => 'Create Sponsor',
        'my_sponsors' => 'My Sponsors',
        'edit' => 'Edit Sponsor',
        'view_docs' => 'Documents',

        'member' => 'Member',
        'members' => 'Members',

        'name' => 'Name',
        'display_name' => 'Display Name',
        'internal_name' => 'Internal Name',

        'status' => 'Status',
        'statuses' => [
            \App\Models\Sponsor::STATUS_ACTIVE => 'Active',
            \App\Models\Sponsor::STATUS_PENDING => 'Pending',
        ],


        'status_updated' => 'Status Updated',

        'updated' => 'Sponsor updated.',
        'update_failed' => 'Sponsor update failed.',

        'create' => 'New Sponsor',
        'created' => 'Sponsor created',
        'create_failed' => 'Sponsor creation failed.',
    ],

    'sponsor_member' => [
        'add' => 'Add a Member',
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
        'settings' => 'Settings',
        'fname' => 'First Name',
        'lname' => 'Last Name',
        'email' => 'Email Address',
        'email_help' => 'Email will not be displayed publicly.',
        'new_password' => 'New Password',
        'new_password_confirmation' => 'Re-Enter New Password',
        'verification_info' => 'Verification Information',

        'settings_pages' => [
            'notifications' => 'Notifications',
            'password' => 'Password',
            'account' => 'Account',
        ],
    ],

    'page' => [
        'manage' => 'Manage Pages',
        'create' => 'Create New Page',
        'edit' => 'Edit Page',
        'updated' => 'Page updated',
        'deleted' => 'Page deleted',

        'title' => 'Title',
        'url' => 'URL',
        'nav_title' => 'Navigation Title',
        'page_title' => 'Page Title',
        'header' => 'Page Header',
        'show_in_header' => 'Show in Header?',
        'show_in_footer' => 'Show in Footer?',
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

        // actual notification content
        'madison' => [
            'comment' => [
                'replied' => [
                    'preference_description' => 'When someone posts a reply to a comment of yours',

                    'subject' => ':name replied to your :comment_type on :document',
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
        'see_comment' => 'See Comment',
        'see_sponsor' => 'See Sponsor',
        'see_document' => 'See Document',
        'review_sponsor' => 'Review Sponsor',
    ],

    'settings' => [
        'featured_documents' => 'Featured Documents',
        'updated_featured_documents' => 'Feature documents updated',
    ],
];

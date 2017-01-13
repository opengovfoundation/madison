<?php

return [
    // General Stuff
    'created' => 'Created',
    'updated' => 'Updated',
    'readmore' => 'Read More',
    'submit' => 'Submit',
    'clear' => 'Clear',
    'submit' => 'Submit',
    'edit' => 'Edit',
    'delete' => 'Delete',
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
        'comments' => 'Comments',
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

        'created' => 'Document created.',
        'deleted' => 'Document deleted. :restoreLinkOpen Undo :restoreLinkClosed',
        'restored' => 'Document restored. It is currently unpublished.',
        'updated' => 'Document updated.',
        'page_added' => 'Page added.',

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
        'remove' => 'Remove',

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
        'edit' => 'Edit Profile',
        'fname' => 'First Name',
        'lname' => 'Last Name',
        'email' => 'Email Address',
        'new_password' => 'New Password',
        'new_password_confirmation' => 'Re-Enter New Password',
        'updated' => 'User updated.',
        'update_failed' => 'User update failed.',
    ],
];

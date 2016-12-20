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
        'category' => 'Category',
        'categories' => 'Categories: ',
        'group' => 'Group',
        'group_others' => 'and others',
        'activity' => 'Activity',
        'comments' => 'Comments',
        'notes' => 'Notes',
        'collaborators' => 'Collaborators',
        'sponsoredby' => 'Sponsored by',

        'created' => 'Document Created',
        'deleted' => 'Document Deleted. :restoreLinkOpen Undo :restoreLinkClosed',
        'restored' => 'Document Restored. It is currently unpublished.',

        'publish_state' => 'Publish State',
        'publish_states' => [
            'all' => 'All',
            \App\Models\Doc::PUBLISH_STATE_PUBLISHED => 'Published',
            \App\Models\Doc::PUBLISH_STATE_UNPUBLISHED => 'Unpublished',
            \App\Models\Doc::PUBLISH_STATE_PRIVATE => 'Private',
            \App\Models\Doc::PUBLISH_STATE_DELETED_ADMIN => 'Deleted (by admin)',
            \App\Models\Doc::PUBLISH_STATE_DELETED_USER => 'Deleted (by user)',
        ],

        'discussion_state' => 'Discussion State',
        'discussion_states' => [
            \App\Models\Doc::DISCUSSION_STATE_OPEN => 'Open',
            \App\Models\Doc::DISCUSSION_STATE_CLOSED => 'Closed',
            \App\Models\Doc::DISCUSSION_STATE_HIDDEN => 'Hidden',
        ],

        'create_as' => 'Sponsor As',

        'list' => 'Documents',
        'create' => 'Create Document',
        'edit' => 'Edit Document',
    ],

    'user' => [
        'edit' => 'Edit Profile',
        'fname' => 'First Name',
        'lname' => 'Last Name',
        'email' => 'Email Address',
        'url' => 'Website',
        'phone' => 'Phone Number',
        'address1' => 'Address 1',
        'address2' => 'Address 2',
        'city' => 'City',
        'state' => 'State',
        'postal_code' => 'Zip Code',
        'new_password' => 'New Password',
        'new_password_confirmation' => 'Re-Enter New Password',
        'updated' => 'User updated.',
        'update_failed' => 'User update failed.',
    ],

];

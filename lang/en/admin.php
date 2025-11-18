<?php

return [
    'panel_title' => 'Admin dashboard',
    'welcome' => 'Welcome, :name! Track core platform metrics and manage operations.',

    'stats' => [
        'total_users' => 'Total users',
        'active_tenders' => 'Active tenders',
        'suppliers' => 'Suppliers in system',
    ],

    'sections' => [
        'users' => 'User Management',
        'tenders' => 'Tender Management',
        'content' => 'Content Management',
        'ai' => 'AI Tools',
    ],

    'users' => [
        'title' => 'Users',
        'index_title' => 'User Management',
        'create_title' => 'Create User',
        'edit_title' => 'Edit User',

        'table' => [
            'col_name' => 'Name',
            'col_email' => 'Email',
            'col_role' => 'Role',
            'col_locale' => 'Language',
            'col_status' => 'Status',
            'col_created_at' => 'Created',
            'col_actions' => 'Actions',
        ],

        'filters' => [
            'title' => 'Filters',
            'role' => 'Role',
            'status' => 'Status',
            'all' => 'All',
            'active' => 'Active',
            'blocked' => 'Blocked',
        ],

        'form' => [
            'name' => 'Name',
            'email' => 'Email',
            'password' => 'Password',
            'role' => 'Role',
            'locale' => 'Language',
        ],

        'roles' => [
            'customer' => 'Customer',
            'supplier' => 'Supplier',
            'admin' => 'Administrator',
        ],

        'actions' => [
            'create' => 'Add User',
            'edit' => 'Edit',
            'block' => 'Block',
            'unblock' => 'Unblock',
            'delete' => 'Delete',
            'confirm_delete' => 'Are you sure you want to delete this user?',
            'confirm_block' => 'Are you sure you want to block this user?',
        ],

        'index_empty' => 'No users found',

        'edit_info' => 'Use password reset function to change password',
    ],

    'tenders' => [
        'title' => 'Tenders',
        'index_title' => 'Tender Management',
        'create_title' => 'Create Tender',
        'edit_title' => 'Edit Tender',
        'show_title' => 'Tender Information',

        'table' => [
            'col_title' => 'Title',
            'col_customer' => 'Customer',
            'col_status' => 'Status',
            'col_valid_until' => 'Valid Until',
            'col_created_at' => 'Created',
            'col_items' => 'Items',
        ],

        'filters' => [
            'title' => 'Filters',
            'status' => 'Status',
            'finished' => 'Finished',
            'active' => 'Active',
            'customer' => 'Customer',
            'all' => 'All',
        ],

        'form' => [
            'customer_id' => 'Customer',
            'title' => 'Tender Title',
            'description' => 'Description',
            'hidden_comment' => 'Hidden Comment',
            'valid_until' => 'Valid Until',
            'status' => 'Status',
        ],

        'statuses' => [
            'open' => 'Open',
            'closed' => 'Closed',
            'review' => 'Under Review',
        ],

        'actions' => [
            'create' => 'Create Tender',
            'edit' => 'Edit',
            'show' => 'View',
            'delete' => 'Delete',
            'confirm_delete' => 'Are you sure you want to delete this tender?',
        ],

        'no_items' => 'No items',

        'messages' => [
            'created' => 'Tender created successfully',
            'updated' => 'Tender updated successfully',
            'deleted' => 'Tender deleted',
        ],
    ],

    'content' => [
        'title' => 'Content',
        'index_title' => 'Content Management',
        'pages' => 'Pages',
        'articles' => 'Articles',
        'news' => 'News',
        'actions' => [
            'create' => 'Create',
            'edit' => 'Edit',
            'delete' => 'Delete',
        ],
    ],

    'ai' => [
        'title' => 'AI Tools',
        'index_title' => 'AI Tools',
        'tender_generation' => 'Tender Generation',
        'translation' => 'Translation',
        'actions' => [
            'generate' => 'Generate',
            'translate' => 'Translate',
        ],
    ],
];

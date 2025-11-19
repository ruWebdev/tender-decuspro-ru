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
        'static_pages' => [
            'title' => 'Static Pages',
            'pages' => [
                'user_agreement' => 'User Agreement',
                'privacy_policy' => 'Privacy Policy',
                'procurement_rules' => 'Procurement Regulations',
            ],
            'fields' => [
                'title_ru' => 'Title (RU)',
                'title_en' => 'Title (EN)',
                'title_cn' => 'Title (CN)',
                'body_ru' => 'Content (RU)',
                'body_en' => 'Content (EN)',
                'body_cn' => 'Content (CN)',
                'published' => 'Published',
            ],
            'actions' => [
                'save' => 'Save changes',
            ],
        ],
    ],

    'ai' => [
        'title' => 'AI Tools',
        'index_title' => 'AI Tools',
        'tender_generation' => 'Tender Generation',
        'tender_generation_desc' => 'Generate a new tender using AI based on the configured prompt',
        'translation' => 'Translation',
        'translation_desc' => 'Translate all tenders to English and Chinese',
        'settings' => [
            'title' => 'AI Settings',
            'deepseek_api_key' => 'Deepseek API Key',
            'deepseek_api_key_note' => 'Enter your Deepseek API key to enable AI features',
            'tender_prompt' => 'Tender generation prompt',
            'tender_prompt_note' => 'Configure the template used to auto-generate tender descriptions',
            'save' => 'Save settings',
        ],
        'actions' => [
            'generate' => 'Generate',
            'translate' => 'Translate',
        ],
    ],

    'smtp' => [
        'title' => 'SMTP Settings',
        'fields' => [
            'host' => 'SMTP host',
            'port' => 'SMTP port',
            'encryption' => 'Encryption',
            'encryption_tls' => 'TLS',
            'encryption_ssl' => 'SSL',
            'encryption_none' => 'None',
            'username' => 'Username',
            'password' => 'Password',
            'from_address' => 'From email',
            'from_name' => 'From name',
        ],
    ],
];

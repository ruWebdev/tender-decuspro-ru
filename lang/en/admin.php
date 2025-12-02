<?php

return [
    'panel_title' => 'Admin dashboard',
    'welcome' => 'Welcome, :name! Track core platform metrics and manage operations.',

    'stats' => [
        'total_users' => 'Total users',
        'active_tenders' => 'Active tenders',
        'suppliers' => 'Suppliers in system',
        'suppliers_new' => 'New suppliers',
        'suppliers_active' => 'Active suppliers',
        'suppliers_blocked' => 'Blocked suppliers',
        'average_bids_per_tender' => 'Average bids per tender',
        'budget_savings' => 'Budget savings',
    ],

    'suppliers' => [
        'index_title' => 'Suppliers',
        'index_empty' => 'No suppliers found',
        'card_title' => 'Supplier card',

        'filters' => [
            'requires_moderation' => 'Requires moderation',
        ],

        'table' => [
            'col_document' => 'Document',
            'col_moderation' => 'Moderation status',
        ],

        'actions' => [
            'view_card' => 'Supplier card',
            'approve' => 'Approve',
            'reject' => 'Reject',
            'reject_comment_prompt' => 'Please provide a reason for rejection:',
        ],

        'profile_empty' => 'Company profile has not been filled in yet.',
        'documents_title' => 'Supplier documents',
        'documents_empty' => 'No documents uploaded yet.',

        'moderation' => [
            'waiting_documents' => 'Waiting for documents',
            'in_review' => 'Under review',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'unknown' => 'Status unknown',
        ],

        'activity' => [
            'recent_proposals' => 'Recent tender responses',
            'wins' => 'Won tenders',
            'no_proposals' => 'No responses yet.',
            'no_wins' => 'No won tenders yet.',
            'col_tender' => 'Tender',
            'col_status' => 'Status',
            'col_submitted_at' => 'Submitted at',
            'col_finished_at' => 'Finished at',
        ],
    ],

    'sections' => [
        'users' => 'User Management',
        'tenders' => 'Tender Management',
        'content' => 'Content Management',
        'ai' => 'AI Tools',
        'recent_activity_title' => 'Recent tenders',
        'shortcuts_title' => 'Shortcuts',
    ],

    'menu' => [
        'platform_suppliers' => 'Suppliers (directory)',
        'backups_logs' => 'Backups & Logs',
        'platform_settings' => 'Platform settings',
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
            'col_blocked' => 'Blocked',
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
            'moderator' => 'Moderator',
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
            'col_chat' => 'Chat',
            'col_actions' => 'Actions',
        ],

        'filters' => [
            'title' => 'Filters',
            'status' => 'Status',
            'is_finished' => 'Finished status',
            'finished' => 'Finished',
            'active' => 'Active',
            'customer' => 'Customer',
            'all' => 'All',
        ],

        'form' => [
            'customer_id' => 'Customer',
            'customer' => 'Customer',
            'title' => 'Tender Title',
            'description' => 'Description',
            'hidden_comment' => 'Hidden Comment',
            'valid_until' => 'Valid Until',
            'status' => 'Status',
            'auto_rebid' => 'Automatic rebidding',
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
            'retender' => 'Start rebidding',
            'confirm_retender' => 'Create a new tender round based on this one?',
        ],

        'no_items' => 'No items',
        'messages' => [
            'created' => 'Tender created successfully',
            'updated' => 'Tender updated successfully',
            'deleted' => 'Tender deleted',
        ],

        'round_label' => 'Bidding round',

        'chat' => [
            'button_title' => 'Chat with suppliers',
            'offcanvas_title' => 'Tender chats',
            'list_title' => 'Suppliers',
            'messages_title' => 'Messages',
            'empty' => 'No messages yet.',
            'unread_badge' => 'Unread',
            'translate_to_ru' => 'Translate chat to Russian',
            'input_placeholder' => 'Enter a message to the supplier...',
            'send' => 'Send',
            'supplier_label' => 'Supplier',
            'customer_label' => 'Customer',
        ],
    ],

    'applications' => [
        'index_title' => 'Tender applications',
        'empty' => 'No applications yet.',
        'table' => [
            'col_tender' => 'Tender',
            'col_supplier' => 'Supplier',
            'col_status' => 'Status',
            'col_created_at' => 'Application date',
        ],
        'offcanvas' => [
            'title' => 'Application details',
            'proposal_info' => 'Application information',
            'supplier_info' => 'Supplier information',
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
    'system_logs' => [
        'title' => 'System logs',
        'empty' => 'No logs yet',
        'filters' => [
            'title' => 'Filters',
            'level' => 'Level',
            'code' => 'Event code',
            'from' => 'From date',
            'to' => 'To date',
            'all' => 'All',
        ],
        'table' => [
            'level' => 'Level',
            'code' => 'Code',
            'message' => 'Message',
            'context' => 'Context',
            'created_at' => 'Created at',
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
        'test' => [
            'title' => 'Test',
            'description' => 'Send a test email to verify the current SMTP settings.',
            'email' => 'Test E-mail',
            'email_placeholder' => 'test@example.com',
            'button' => 'Check',
            'subject' => 'SMTP test email',
            'body' => 'This is a test email to verify the SMTP settings of the platform.',
            'success' => 'Test email has been sent. Please check the inbox.',
            'error' => 'Failed to send test email. Please check SMTP settings.',
        ],
        'smtp_bz' => [
            'title' => 'SMTP.bz API key',
            'description' => 'Specify your SMTP.bz API key. If set, emails will be sent via the SMTP.bz API.',
            'api_key' => 'SMTP.bz API key',
            'api_key_placeholder' => 'Enter SMTP.bz API key',
            'button' => 'Save',
            'saved' => 'SMTP.bz key has been saved.',
            'test_response_title' => 'SMTP.bz API response',
        ],
    ],

    'backup' => [
        'title' => 'Backups',
        'empty' => 'No backups found',
        'confirm_run' => 'Create a new backup now?',
        'confirm_delete' => 'Delete the selected backup?',
        'actions' => [
            'run' => 'Create backup',
            'download' => 'Download',
            'delete' => 'Delete',
        ],
        'table' => [
            'name' => 'File',
            'size' => 'Size',
            'created_at' => 'Created at',
            'actions' => 'Actions',
        ],
    ],

    'platform_suppliers' => [
        'index_title' => 'Suppliers (directory)',
        'index_empty' => 'No suppliers found',
        'total_count' => 'Total records',
        'create_title' => 'Create supplier',
        'edit_title' => 'Edit supplier',
        'actions' => [
            'create' => 'Add supplier',
            'confirm_delete' => 'Delete this supplier?',
        ],
        'filters' => [
            'search' => 'Search suppliers',
            'search_placeholder' => 'Search by name, email or phone',
        ],
        'table' => [
            'col_name' => 'Name',
            'col_phone' => 'Phone',
            'col_email' => 'Email',
            'col_website' => 'Website',
            'col_comment' => 'Comment',
            'col_language' => 'Language',
            'col_invitation_sent' => 'Invitation sent',
            'col_actions' => 'Actions',
        ],
        'form' => [
            'name' => 'Name',
            'phone' => 'Phone',
            'email' => 'Email',
            'website' => 'Website',
            'comment' => 'Comment',
            'language' => 'Language',
            'invitation_sent' => 'Invitation sent',
        ],
        'badges' => [
            'yes' => 'Yes',
            'no' => 'No',
        ],
    ],

    'notification_templates' => [
        'index_title' => 'Notification templates',
        'index_empty' => 'No notification templates found',
        'create_title' => 'Create notification template',
        'edit_title' => 'Edit notification template',
        'actions' => [
            'create' => 'Add template',
            'translate' => 'Translate',
        ],
        'table' => [
            'col_name' => 'Name',
            'col_type' => 'Type',
            'col_actions' => 'Actions',
        ],
        'form' => [
            'name' => 'Name',
            'type' => 'Notification type',
            'body_ru' => 'Text (Russian)',
            'body_en' => 'Text (English)',
            'body_cn' => 'Text (Chinese)',
        ],
        'types' => [
            'new_tender' => 'New tender',
            'better_price' => 'Better price appeared',
            'tender_closed' => 'Tender closed',
            'won' => 'You won the tender',
            'lost' => 'You lost the tender',
            'retender' => 'Tender sent to rebidding',
            'platform_invitation' => 'Platform invitation',
        ],
    ],

    'analytics' => [
        'procurement_volume_title' => 'Procurement volume for the period',
        'current_period' => 'current month',
        'top_suppliers_title' => 'Top suppliers by won tenders',
        'top_suppliers' => [
            'col_name' => 'Supplier',
            'col_wins' => 'Won tenders',
        ],
    ],

    'wechat' => [
        'title' => 'WeChat',
        'not_configured' => 'WeChat is not configured. Please set AppID and AppSecret in settings.',
        'empty' => 'No conversations yet',
        'no_messages' => 'No messages',
        'you' => 'You',
        'subscribed' => 'Subscribed',
        'unsubscribed' => 'Unsubscribed',
        'cannot_send' => 'User has unsubscribed. Cannot send messages.',
        'confirm_delete' => 'Delete this conversation?',
        'link_supplier' => 'Link supplier',
        'edit_remark' => 'Edit remark',
        'no_supplier' => '— Not linked —',
        'remark_placeholder' => 'Note for this contact',
        'input_placeholder' => 'Enter message...',
        'translate' => 'Translate',
        'settings' => [
            'title' => 'WeChat Settings',
            'app_id' => 'AppID',
            'app_secret' => 'AppSecret',
            'app_secret_hint' => 'Leave empty to keep current',
            'token' => 'Token',
            'token_hint' => 'Token for server verification',
            'encoding_key' => 'EncodingAESKey',
            'encoding_key_hint' => 'Encryption key (optional)',
            'webhook_url' => 'Webhook URL',
            'webhook_hint' => 'Set this URL in WeChat Official Account settings',
            'is_active' => 'Integration active',
            'test' => 'Test connection',
        ],
        'filters' => [
            'search' => 'Search',
            'search_placeholder' => 'Name or remark...',
            'unread_only' => 'Unread only',
        ],
    ],
];

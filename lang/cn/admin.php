<?php

return [
    'panel_title' => '管理员控制台',
    'welcome' => '欢迎，:name！在此查看核心指标并管理平台运营。',

    'stats' => [
        'total_users' => '用户总数',
        'active_tenders' => '进行中的招标',
        'suppliers' => '系统中的供应商',
    ],

    'sections' => [
        'users' => '用户管理',
        'tenders' => '招标管理',
        'content' => '内容管理',
        'ai' => 'AI工具',
    ],

    'users' => [
        'title' => '用户',
        'index_title' => '用户管理',
        'create_title' => '创建用户',
        'edit_title' => '编辑用户',

        'table' => [
            'col_name' => '姓名',
            'col_email' => '邮箱',
            'col_role' => '角色',
            'col_locale' => '语言',
            'col_status' => '状态',
            'col_created_at' => '创建时间',
            'col_actions' => '操作',
        ],

        'filters' => [
            'title' => '筛选',
            'role' => '角色',
            'status' => '状态',
            'all' => '全部',
            'active' => '活跃',
            'blocked' => '已屏蔽',
        ],

        'form' => [
            'name' => '姓名',
            'email' => '邮箱',
            'password' => '密码',
            'role' => '角色',
            'locale' => '语言',
        ],

        'roles' => [
            'customer' => '客户',
            'supplier' => '供应商',
            'admin' => '管理员',
        ],

        'actions' => [
            'create' => '添加用户',
            'edit' => '编辑',
            'block' => '屏蔽',
            'unblock' => '解除屏蔽',
            'delete' => '删除',
            'confirm_delete' => '您确定要删除此用户吗？',
            'confirm_block' => '您确定要屏蔽此用户吗？',
        ],

        'index_empty' => '未找到用户',

        'edit_info' => '使用密码重置功能更改密码',
    ],

    'tenders' => [
        'title' => '招标',
        'index_title' => '招标管理',
        'create_title' => '创建招标',
        'edit_title' => '编辑招标',
        'show_title' => '招标信息',

        'table' => [
            'col_title' => '标题',
            'col_customer' => '客户',
            'col_status' => '状态',
            'col_valid_until' => '有效期至',
            'col_created_at' => '创建时间',
            'col_items' => '项目',
        ],

        'filters' => [
            'title' => '筛选',
            'status' => '状态',
            'finished' => '已完成',
            'active' => '活跃',
            'customer' => '客户',
            'all' => '全部',
        ],

        'form' => [
            'customer_id' => '客户',
            'title' => '招标标题',
            'description' => '描述',
            'hidden_comment' => '隐藏评论',
            'valid_until' => '有效期至',
            'status' => '状态',
        ],

        'statuses' => [
            'open' => '开放',
            'closed' => '已关闭',
            'review' => '审核中',
        ],

        'actions' => [
            'create' => '创建招标',
            'edit' => '编辑',
            'show' => '查看',
            'delete' => '删除',
            'confirm_delete' => '您确定要删除此招标吗？',
        ],

        'no_items' => '无项目',
    ],

    'content' => [
        'title' => '内容',
        'index_title' => '内容管理',
        'pages' => '页面',
        'articles' => '文章',
        'news' => '新闻',
        'actions' => [
            'create' => '创建',
            'edit' => '编辑',
            'delete' => '删除',
        ],
        'static_pages' => [
            'title' => '静态页面',
            'pages' => [
                'user_agreement' => '用户协议',
                'privacy_policy' => '隐私政策',
                'procurement_rules' => '采购规程',
            ],
            'fields' => [
                'title_ru' => '标题 (RU)',
                'title_en' => '标题 (EN)',
                'title_cn' => '标题 (CN)',
                'body_ru' => '内容 (RU)',
                'body_en' => '内容 (EN)',
                'body_cn' => '内容 (CN)',
                'published' => '已发布',
            ],
            'actions' => [
                'save' => '保存更改',
            ],
        ],
    ],

    'ai' => [
        'title' => 'AI工具',
        'index_title' => 'AI工具',
        'tender_generation' => '招标生成',
        'tender_generation_desc' => '基于已配置的提示，使用AI生成新的招标',
        'translation' => '翻译',
        'translation_desc' => '将所有招标翻译为英文和中文',
        'settings' => [
            'title' => 'AI设置',
            'deepseek_api_key' => 'Deepseek API密钥',
            'deepseek_api_key_note' => '输入您的Deepseek API密钥以启用AI功能',
            'tender_prompt' => '招标生成提示词',
            'tender_prompt_note' => '配置用于自动生成招标描述的模板',
            'save' => '保存设置',
        ],
        'actions' => [
            'generate' => '生成',
            'translate' => '翻译',
        ],
    ],

    'smtp' => [
        'title' => 'SMTP设置',
        'fields' => [
            'host' => 'SMTP主机',
            'port' => 'SMTP端口',
            'encryption' => '加密方式',
            'encryption_tls' => 'TLS',
            'encryption_ssl' => 'SSL',
            'encryption_none' => '无',
            'username' => '用户名',
            'password' => '密码',
            'from_address' => '发件邮箱',
            'from_name' => '发件人名称',
        ],
    ],
];

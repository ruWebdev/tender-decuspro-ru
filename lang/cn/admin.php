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
    ],

    'ai' => [
        'title' => 'AI工具',
        'index_title' => 'AI工具',
        'tender_generation' => '招标生成',
        'translation' => '翻译',
        'actions' => [
            'generate' => '生成',
            'translate' => '翻译',
        ],
    ],
];

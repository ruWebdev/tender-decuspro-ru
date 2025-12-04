<?php

return [
    'panel_title' => '管理员控制台',
    'welcome' => '欢迎，:name！在此查看核心指标并管理平台运营。',

    'stats' => [
        'total_users' => '用户总数',
        'active_tenders' => '进行中的招标',
        'suppliers' => '系统中的供应商',
        'suppliers_new' => '新增供应商',
        'suppliers_active' => '活跃供应商',
        'suppliers_blocked' => '被屏蔽的供应商',
        'average_bids_per_tender' => '每个招标的平均投标数',
        'budget_savings' => '预算节省',
    ],

    'suppliers' => [
        'index_title' => '供应商',
        'index_empty' => '未找到供应商',
        'card_title' => '供应商卡片',

        'filters' => [
            'requires_moderation' => '需要审核',
        ],

        'table' => [
            'col_document' => '资料',
            'col_moderation' => '审核状态',
        ],

        'actions' => [
            'view_card' => '供应商卡片',
            'approve' => '通过',
            'reject' => '拒绝',
            'reject_comment_prompt' => '请输入拒绝原因：',
        ],

        'profile_empty' => '公司资料尚未填写。',
        'documents_title' => '供应商资料',
        'documents_empty' => '尚未上传任何资料。',

        'moderation' => [
            'waiting_documents' => '等待上传资料',
            'in_review' => '审核中',
            'approved' => '已通过',
            'rejected' => '已拒绝',
            'unknown' => '状态未知',
        ],

        'activity' => [
            'recent_proposals' => '最近的投标记录',
            'wins' => '中标记录',
            'no_proposals' => '暂无投标记录。',
            'no_wins' => '暂无中标记录。',
            'col_tender' => '招标',
            'col_status' => '状态',
            'col_submitted_at' => '投标时间',
            'col_finished_at' => '完成时间',
        ],
    ],

    'sections' => [
        'users' => '用户管理',
        'tenders' => '招标管理',
        'content' => '内容管理',
        'ai' => 'AI工具',
        'recent_activity_title' => '最新招标',
        'shortcuts_title' => '快捷链接',
    ],

    'menu' => [
        'platform_suppliers' => '供应商（目录）',
        'backups_logs' => '备份和日志',
        'platform_settings' => '平台设置',
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
            'col_blocked' => '封禁状态',
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
            'moderator' => '版主',
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
            'col_chat' => '聊天',
            'col_actions' => '操作',
        ],

        'filters' => [
            'title' => '筛选',
            'status' => '状态',
            'is_finished' => '完成状态',
            'finished' => '已完成',
            'active' => '活跃',
            'customer' => '客户',
            'all' => '全部',
        ],

        'form' => [
            'customer_id' => '客户',
            'customer' => '客户',
            'title' => '招标标题',
            'description' => '描述',
            'hidden_comment' => '隐藏评论',
            'valid_until' => '有效期至',
            'status' => '状态',
            'auto_rebid' => '自动二次竞价',
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
            'retender' => '发起二次竞价',
            'confirm_retender' => '基于此招标创建新一轮招标吗？',
        ],

        'no_items' => '无项目',
        'round_label' => '竞价轮次',

        'chat' => [
            'button_title' => '与供应商聊天',
            'offcanvas_title' => '招标聊天',
            'list_title' => '供应商',
            'messages_title' => '消息',
            'empty' => '目前还没有消息。',
            'unread_badge' => '未读',
            'translate_to_ru' => '将聊天翻译为俄语',
            'input_placeholder' => '输入发送给供应商的消息…',
            'send' => '发送',
            'supplier_label' => '供应商',
            'customer_label' => '客户',
        ],
    ],

    'applications' => [
        'index_title' => '招标申请',
        'empty' => '暂无申请。',
        'table' => [
            'col_tender' => '招标',
            'col_supplier' => '供应商',
            'col_status' => '状态',
            'col_created_at' => '申请日期',
        ],
        'offcanvas' => [
            'title' => '申请详情',
            'proposal_info' => '申请信息',
            'supplier_info' => '供应商信息',
        ],
    ],

    'proposals' => [
        'price_rub_label' => '卢布',
        'price_cny_label' => '元',
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
                'supplier_terms' => 'LLC «QBS» 供应商通用条款、运营与隐私政策',
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
    'system_logs' => [
        'title' => '系统日志',
        'empty' => '当前没有日志记录',
        'filters' => [
            'title' => '筛选',
            'level' => '级别',
            'code' => '事件代码',
            'from' => '起始日期',
            'to' => '结束日期',
            'all' => '全部',
        ],
        'table' => [
            'level' => '级别',
            'code' => '代码',
            'message' => '消息',
            'context' => '上下文',
            'created_at' => '创建时间',
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
        'test' => [
            'title' => '测试',
            'description' => '向指定邮箱发送测试邮件，以验证当前SMTP设置是否正确。',
            'email' => '测试邮箱',
            'email_placeholder' => 'test@example.com',
            'button' => '检查',
            'subject' => 'SMTP测试邮件',
            'body' => '这是一封用于验证平台SMTP设置的测试邮件。',
            'success' => '测试邮件已发送，请检查收件箱。',
            'error' => '无法发送测试邮件，请检查SMTP设置。',
        ],
        'smtp_bz' => [
            'title' => 'SMTP.bz API密钥',
            'description' => '请输入SMTP.bz API密钥。如已设置，邮件将通过SMTP.bz API发送。',
            'api_key' => 'SMTP.bz API密钥',
            'api_key_placeholder' => '输入SMTP.bz API密钥',
            'button' => '保存',
            'saved' => 'SMTP.bz密钥已保存。',
            'test_response_title' => 'SMTP.bz API响应',
        ],
    ],

    'backup' => [
        'title' => '备份管理',
        'empty' => '暂无备份文件',
        'confirm_run' => '现在创建新的备份吗？',
        'confirm_delete' => '确定要删除所选备份吗？',
        'actions' => [
            'run' => '创建备份',
            'download' => '下载',
            'delete' => '删除',
        ],
        'table' => [
            'name' => '文件',
            'size' => '大小',
            'created_at' => '创建时间',
            'actions' => '操作',
        ],
    ],

    'platform_suppliers' => [
        'index_title' => '供应商（目录）',
        'index_empty' => '未找到供应商',
        'total_count' => '记录总数',
        'create_title' => '创建供应商',
        'edit_title' => '编辑供应商',
        'actions' => [
            'create' => '添加供应商',
            'confirm_delete' => '确定要删除此供应商吗？',
        ],
        'filters' => [
            'search' => '搜索供应商',
            'search_placeholder' => '按名称、邮箱或电话搜索',
        ],
        'table' => [
            'col_name' => '名称',
            'col_phone' => '电话',
            'col_email' => '邮箱',
            'col_website' => '网站',
            'col_comment' => '备注',
            'col_language' => '语言',
            'col_invitation_sent' => '邀请已发送',
            'col_actions' => '操作',
        ],
        'form' => [
            'name' => '名称',
            'phone' => '电话',
            'email' => '邮箱',
            'website' => '网站',
            'comment' => '备注',
            'language' => '语言',
            'invitation_sent' => '邀请已发送',
        ],
        'badges' => [
            'yes' => '是',
            'no' => '否',
        ],
        'languages' => [
            'ru' => '俄语',
            'en' => '英语',
            'cn' => '中文',
        ],
        'invitation' => [
            'send' => '发送',
            'no_email' => '无法发送邀请：供应商邮箱未填写。',
            'no_template' => '未找到“平台邀请”通知模板。',
            'empty_body' => '邀请模板内容为空，无法发送邮件。',
            'sent_success' => '已向供应商发送邀请。',
            'sent_error' => '无法向供应商发送邀请，请检查SMTP设置。',
        ],
    ],

    'notification_templates' => [
        'index_title' => '通知模板',
        'index_empty' => '暂无通知模板',
        'create_title' => '创建通知模板',
        'edit_title' => '编辑通知模板',
        'actions' => [
            'create' => '添加模板',
            'translate' => '翻译',
        ],
        'table' => [
            'col_name' => '名称',
            'col_type' => '类型',
            'col_actions' => '操作',
        ],
        'form' => [
            'name' => '名称',
            'type' => '通知类型',
            'body_ru' => '文本（俄语）',
            'body_en' => '文本（英语）',
            'body_cn' => '文本（中文）',
        ],
        'types' => [
            'new_tender' => '新招标',
            'better_price' => '出现更低报价',
            'tender_closed' => '招标已关闭',
            'won' => '您已中标',
            'lost' => '您未中标',
            'retender' => '招标进入二次竞价',
            'platform_invitation' => '平台邀请',
        ],
        'translate_error' => '无法完成翻译，请重试。',
    ],

    'analytics' => [
        'procurement_volume_title' => '期间内的采购总额',
        'current_period' => '当月',
        'top_suppliers_title' => '中标次数最多的供应商',
        'top_suppliers' => [
            'col_name' => '供应商',
            'col_wins' => '中标次数',
        ],
    ],

    'wechat' => [
        'title' => '微信',
        'not_configured' => '微信未配置。请在设置中填写AppID和AppSecret。',
        'empty' => '暂无对话',
        'no_messages' => '暂无消息',
        'you' => '您',
        'subscribed' => '已关注',
        'unsubscribed' => '已取消关注',
        'cannot_send' => '用户已取消关注，无法发送消息。',
        'confirm_delete' => '删除此对话？',
        'link_supplier' => '关联供应商',
        'edit_remark' => '修改备注',
        'no_supplier' => '— 未关联 —',
        'remark_placeholder' => '此联系人的备注',
        'input_placeholder' => '输入消息...',
        'translate' => '翻译',
        'settings' => [
            'title' => '微信设置',
            'app_id' => 'AppID',
            'app_secret' => 'AppSecret',
            'app_secret_hint' => '留空以保留当前值',
            'token' => 'Token',
            'token_hint' => '服务器验证令牌',
            'encoding_key' => 'EncodingAESKey',
            'encoding_key_hint' => '加密密钥（可选）',
            'webhook_url' => 'Webhook URL',
            'webhook_hint' => '在微信公众号设置中填写此URL',
            'is_active' => '启用集成',
            'test' => '测试连接',
        ],
        'filters' => [
            'search' => '搜索',
            'search_placeholder' => '名称或备注...',
            'unread_only' => '仅未读',
        ],
    ],
];

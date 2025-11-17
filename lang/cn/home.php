<?php

return [
    'title' => '进行中的招标',
    'no_tenders' => '当前没有进行中的招标。',
    'info_title' => '信息',
    'info_text' => '欢迎使用招标平台！在这里您可以创建招标并提交报价。',
    'button_details' => '详细信息',
    'button_participate' => '参与投标',

    'hero' => [
        'kicker' => '面向IT供应商',
        'title_main' => 'QBS招标平台，专为IT设备供应商打造。',
        'title_alt' => '欢迎进入QBS采购门户',
        'subtitle' => '参与招标、获取实时采购需求，成为我们值得信赖的合作伙伴。',
        'primary_cta' => '注册',
        'secondary_cta' => '登录个人中心',
        'points' => [
            'one' => '所有招标与要求集中展示。',
            'two' => '透明的评估标准与明确的时间表。',
            'three' => '采购团队及时提供支持。',
        ],
    ],

    'benefits' => [
        'title' => '为什么通过此平台与QBS合作？',
        'subtitle' => '我们与优秀的IT供应商合作，致力于建立长期的合作关系。',
        'items' => [
            'transparency' => [
                'title' => '透明',
                'text' => '所有招标与要求在同一平台，标准与时限一目了然。',
            ],
            'efficiency' => [
                'title' => '高效',
                'text' => '几步即可提交报价，快速响应我们的需求。',
            ],
            'fair' => [
                'title' => '公平',
                'text' => '每位供应商都享有同等的采购信息访问权限。',
            ],
            'partnership' => [
                'title' => '长期合作',
                'text' => '我们重视与优质供应商建立稳定互利的合作。',
            ],
        ],
    ],

    'steps' => [
        'title' => '成为QBS供应商如此简单',
        'subtitle' => '按照步骤即可轻松完成从注册到签订合同的全过程。',
        'items' => [
            'register' => [
                'title' => '注册与认证',
                'text' => '填写表单并提交公司信息，我们会尽快完成审核。',
            ],
            'requests' => [
                'title' => '接收需求',
                'text' => '在个人中心查看所有正在进行的招标与采购需求。',
            ],
            'participate' => [
                'title' => '参与投标',
                'text' => '直接在平台上提交商业报价。',
            ],
            'contract' => [
                'title' => '签订合同',
                'text' => '中标供应商会立即收到所有签约文件。',
            ],
        ],
    ],

    'tenders' => [
        'title_section' => '最新采购',
        'button_all' => '全部招标',
        'filters' => [
            'search_label' => '搜索招标',
            'search_placeholder' => '输入招标名称或描述',
            'status_label' => '状态',
            'deadline_label' => '提交截止日期',
            'status_filters' => [
                'all' => '全部状态',
                'open' => '接受投标',
                'review' => '审核中',
                'closed' => '已完成',
            ],
        ],
        'empty_filtered' => '没有符合筛选条件的招标。',
        'empty_default' => '当前没有进行中的招标。',
        'status' => [
            'open' => '接受投标',
            'review' => '审核中',
            'closed' => '已完成',
        ],
        'table' => [
            'col_number' => '招标编号',
            'col_name' => '采购名称',
            'col_description' => '简要说明',
            'col_deadline' => '提交截止',
            'col_status' => '状态',
            'col_actions' => '操作',
            'no_description' => '没有提供描述',
        ],
    ],

    'faq' => [
        'kicker' => 'FAQ',
        'title' => '常见问题',
        'subtitle' => '快速解答，帮助您了解流程和要求。',
        'items' => [
            'registration' => [
                'question' => '如何注册？',
                'answer' => '点击“注册”，填写公司资料并完成邮箱确认。',
            ],
            'documents' => [
                'question' => '认证需要哪些文件？',
                'answer' => '需提供公司证照、税号、银行信息及主要联系人。',
            ],
            'criteria' => [
                'question' => '评标标准是什么？',
                'answer' => '我们综合考虑价格、交付周期、质保条款与相关经验。',
            ],
            'contract' => [
                'question' => '在哪里查看合同范本？',
                'answer' => '登录后可在每个招标的文档区查看标准合同。',
            ],
            'contact' => [
                'question' => '有关具体招标向谁咨询？',
                'answer' => '请联系采购经理：Anna Smirnova，procurement@qbs.com，+7 (495) 123-45-67。',
            ],
        ],
    ],

    'contacts' => [
        'kicker' => 'QBS',
        'title' => 'QBS采购部联系方式',
        'technical' => [
            'label' => '平台技术支持：',
            'value' => 'support@qbs.com',
        ],
        'commercial' => [
            'label' => '商务与招标咨询：',
            'value' => 'procurement@qbs.com',
        ],
        'phone' => [
            'label' => '紧急联系电话：',
            'value' => '+7 (495) 123-45-67',
        ],
        'manager' => [
            'label' => '联系人：',
            'value' => 'Anna Smirnova，采购主管',
        ],
        'schedule' => [
            'label' => '工作时间：',
            'value' => '周一至周五 09:00-18:00',
        ],
        'support_title' => '支持与陪伴',
        'support_text' => '我们将及时帮助您完成平台接入并解答任何疑问。',
    ],

    'footer' => [
        'logo_alt' => 'QBS · 招标平台',
        'note' => '版权所有',
        'links' => [
            'user_agreement' => [
                'label' => '用户协议',
                'url' => '/docs/user-agreement',
            ],
            'privacy' => [
                'label' => '隐私政策',
                'url' => '/docs/privacy-policy',
            ],
            'regulations' => [
                'label' => '采购规程',
                'url' => '/docs/procurement-rules',
            ],
        ],
    ],
];

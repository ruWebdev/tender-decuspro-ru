<?php

return [
    'title' => 'Welcome',
    'no_tenders' => 'No active tenders found.',
    'info_title' => 'Information',
    'info_text' => 'Welcome to the tender platform! Here you can create tenders and submit proposals.',
    'button_details' => 'Details',
    'button_participate' => 'Participate',

    'hero' => [
        'kicker' => 'for it suppliers',
        'title_main' => 'QBS Tender Platform. The place for IT equipment suppliers.',
        'title_alt' => 'Welcome to the QBS procurement portal',
        'subtitle' => 'Join tenders, get real-time requests, and become our trusted partner.',
        'primary_cta' => 'Sign up',
        'secondary_cta' => 'Log in to cabinet',
        'points' => [
            'one' => 'All tenders and requirements in one place.',
            'two' => 'Clear evaluation criteria and transparent timing.',
            'three' => 'Responsive procurement support team.',
        ],
    ],

    'benefits' => [
        'title' => 'Why collaborate with QBS via this platform?',
        'subtitle' => 'We work with the best IT suppliers and focus on long-term partnerships.',
        'items' => [
            'transparency' => [
                'title' => 'Transparency',
                'text' => 'Every tender and requirement in one place with clear criteria and deadlines.',
            ],
            'efficiency' => [
                'title' => 'Efficiency',
                'text' => 'Submit proposals in a few clicks and react quickly to new requests.',
            ],
            'fair' => [
                'title' => 'Equal terms',
                'text' => 'Each supplier receives the same access to procurement data.',
            ],
            'partnership' => [
                'title' => 'Long-term partnership',
                'text' => 'We build stable and mutually beneficial relationships with suppliers.',
            ],
        ],
    ],

    'steps' => [
        'title' => 'Becoming a QBS supplier is simple',
        'subtitle' => 'Follow the steps to go from registration to contract signing effortlessly.',
        'items' => [
            'register' => [
                'title' => 'Register and verify',
                'text' => 'Fill out the form, send company details, and we will verify them shortly.',
            ],
            'requests' => [
                'title' => 'Receive requests',
                'text' => 'See every live tender and IT equipment request inside your cabinet.',
            ],
            'participate' => [
                'title' => 'Join tenders',
                'text' => 'Submit commercial offers directly through the platform.',
            ],
            'contract' => [
                'title' => 'Sign the contract',
                'text' => 'Winners instantly receive all paperwork to finalize the deal.',
            ],
        ],
    ],

    'tenders' => [
        'title_section' => 'Live procurements',
        'button_all' => 'All tenders',
        'filters' => [
            'search_label' => 'Search tenders',
            'search_placeholder' => 'Tender name or short description',
            'status_label' => 'Status',
            'deadline_label' => 'Submit before',
            'status_filters' => [
                'all' => 'All statuses',
                'open' => 'Accepting bids',
                'review' => 'Under review',
                'closed' => 'Finished',
            ],
        ],
        'empty_filtered' => 'No tenders match the selected filters.',
        'empty_default' => 'No active tenders found.',
        'status' => [
            'open' => 'Accepting bids',
            'review' => 'Under review',
            'closed' => 'Finished',
        ],
        'table' => [
            'col_number' => 'Tender ID',
            'col_name' => 'Procurement name',
            'col_description' => 'Short description',
            'col_deadline' => 'Submission deadline',
            'col_status' => 'Status',
            'col_actions' => 'Actions',
            'no_description' => 'No description provided',
        ],
    ],

    'faq' => [
        'kicker' => 'FAQ',
        'title' => 'Frequent supplier questions',
        'subtitle' => 'Quick answers help you navigate processes and requirements.',
        'items' => [
            'registration' => [
                'question' => 'How do I register on the platform?',
                'answer' => 'Click “Sign up”, share company data, and confirm the email address.',
            ],
            'documents' => [
                'question' => 'Which documents are required for verification?',
                'answer' => 'Corporate documents, tax IDs, bank details, and a primary contact person.',
            ],
            'criteria' => [
                'question' => 'Which criteria are used to evaluate bids?',
                'answer' => 'We assess price, delivery timing, warranty terms, and relevant supplier experience.',
            ],
            'contract' => [
                'question' => 'Where can I read the contract draft?',
                'answer' => 'A standard contract is available after login in each tender’s documents section.',
            ],
            'contact' => [
                'question' => 'Who can answer questions about a tender?',
                'answer' => 'Contact our procurement manager: Anna Smirnova, procurement@qbs.com, +7 (495) 123-45-67.',
            ],
        ],
    ],

    'contacts' => [
        'kicker' => 'QBS',
        'title' => 'QBS Procurement Department contacts',
        'technical' => [
            'label' => 'Technical support questions:',
            'value' => 'support@qbs.com',
        ],
        'commercial' => [
            'label' => 'Commercial terms and tender questions:',
            'value' => 'procurement@qbs.com',
        ],
        'phone' => [
            'label' => 'Hotline phone:',
            'value' => '+7 (495) 123-45-67',
        ],
        'manager' => [
            'label' => 'Contact person:',
            'value' => 'Anna Smirnova, Head of Procurement',
        ],
        'schedule' => [
            'label' => 'Working hours:',
            'value' => 'Mon–Fri, 09:00–18:00',
        ],
        'support_title' => 'Support & onboarding',
        'support_text' => 'We will quickly help you configure access to the platform and answer any questions.',
    ],

    'footer' => [
        'logo_alt' => 'QBS · Tender platform',
        'note' => 'All rights reserved',
        'links' => [
            'user_agreement' => [
                'label' => 'User agreement',
                'url' => '/docs/user-agreement',
            ],
            'privacy' => [
                'label' => 'Privacy policy',
                'url' => '/docs/privacy-policy',
            ],
            'regulations' => [
                'label' => 'Procurement regulations',
                'url' => '/docs/procurement-rules',
            ],
        ],
    ],
];

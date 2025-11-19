<?php

return [
    'panel_title' => 'Административная панель',
    'welcome' => 'Добро пожаловать, :name! Следите за ключевыми показателями платформы и управляйте процессами.',

    'stats' => [
        'total_users' => 'Всего пользователей',
        'active_tenders' => 'Активные закупки',
        'suppliers' => 'Поставщиков в системе',
    ],

    'sections' => [
        'users' => 'Управление пользователями',
        'tenders' => 'Управление тендерами',
        'content' => 'Управление контентом',
        'ai' => 'ИИ инструменты',
    ],

    'users' => [
        'title' => 'Пользователи',
        'index_title' => 'Управление пользователями',
        'create_title' => 'Создание пользователя',
        'edit_title' => 'Редактирование пользователя',

        'table' => [
            'col_name' => 'Имя',
            'col_email' => 'Email',
            'col_role' => 'Роль',
            'col_locale' => 'Язык',
            'col_status' => 'Статус',
            'col_blocked' => 'Заблокирован',
            'col_created_at' => 'Создан',
            'col_actions' => 'Действия',
        ],

        'filters' => [
            'title' => 'Фильтры',
            'role' => 'Роль',
            'status' => 'Статус',
            'all' => 'Все',
            'active' => 'Активные',
            'blocked' => 'Заблокированные',
        ],

        'form' => [
            'name' => 'Имя',
            'email' => 'Email',
            'password' => 'Пароль',
            'role' => 'Роль',
            'locale' => 'Язык',
        ],

        'roles' => [
            'customer' => 'Заказчик',
            'supplier' => 'Поставщик',
            'admin' => 'Администратор',
        ],

        'actions' => [
            'create' => 'Добавить пользователя',
            'edit' => 'Редактировать',
            'block' => 'Заблокировать',
            'unblock' => 'Разблокировать',
            'delete' => 'Удалить',
            'confirm_delete' => 'Вы уверены, что хотите удалить этого пользователя?',
            'confirm_block' => 'Вы уверены, что хотите заблокировать этого пользователя?',
        ],

        'index_empty' => 'Пользователи не найдены',

        'edit_info' => 'Для изменения пароля используйте функцию сброса пароля',
    ],

    'tenders' => [
        'title' => 'Тендеры',
        'index_title' => 'Управление тендерами',
        'create_title' => 'Создание тендера',
        'edit_title' => 'Редактирование тендера',
        'show_title' => 'Информация о тендере',

        'table' => [
            'col_title' => 'Название',
            'col_customer' => 'Заказчик',
            'col_status' => 'Статус',
            'col_valid_until' => 'Действует до',
            'col_created_at' => 'Создан',
            'col_items' => 'Позиции',
            'col_actions' => 'Действия',
        ],

        'filters' => [
            'title' => 'Фильтры',
            'status' => 'Статус',
            'is_finished' => 'Завершен',
            'finished' => 'Завершен',
            'active' => 'Активный',
            'customer' => 'Заказчик',
            'all' => 'Все',
        ],

        'form' => [
            'customer_id' => 'Заказчик',
            'title' => 'Название тендера',
            'description' => 'Описание',
            'hidden_comment' => 'Скрытый комментарий',
            'valid_until' => 'Действует до',
            'status' => 'Статус',
        ],

        'statuses' => [
            'open' => 'Открыт',
            'closed' => 'Закрыт',
            'review' => 'На рассмотрении',
        ],

        'actions' => [
            'create' => 'Создать тендер',
            'edit' => 'Редактировать',
            'show' => 'Просмотр',
            'delete' => 'Удалить',
            'confirm_delete' => 'Вы уверены, что хотите удалить этот тендер?',
        ],

        'no_items' => 'Нет позиций',
    ],

    'content' => [
        'title' => 'Контент',
        'index_title' => 'Управление контентом',
        'pages' => 'Страницы',
        'articles' => 'Статьи',
        'news' => 'Новости',
        'actions' => [
            'create' => 'Создать',
            'edit' => 'Редактировать',
            'delete' => 'Удалить',
        ],
        'home_editor' => [
            'title' => 'Главная страница — редактирование контента',
            'col_key' => 'Ключ',
            'col_value' => 'Значение',
            'actions' => [
                'save' => 'Сохранить',
                'add_row' => 'Добавить строку',
            ],
        ],
        'static_pages' => [
            'title' => 'Статические страницы',
            'pages' => [
                'user_agreement' => 'Пользовательское соглашение',
                'privacy_policy' => 'Политика конфиденциальности',
                'procurement_rules' => 'Регламент проведения закупок',
            ],
            'fields' => [
                'title_ru' => 'Заголовок (RU)',
                'title_en' => 'Заголовок (EN)',
                'title_cn' => 'Заголовок (CN)',
                'body_ru' => 'Содержимое (RU)',
                'body_en' => 'Содержимое (EN)',
                'body_cn' => 'Содержимое (CN)',
                'published' => 'Опубликовано',
            ],
            'actions' => [
                'save' => 'Сохранить изменения',
            ],
        ],
    ],

    'ai' => [
        'title' => 'ИИ инструменты',
        'index_title' => 'ИИ инструменты',
        'tender_generation' => 'Генерация тендера',
        'tender_generation_desc' => 'Генерация нового тендера с помощью ИИ на основе настроенного промпта',
        'translation' => 'Перевод',
        'translation_desc' => 'Перевод всех тендеров на английский и китайский языки',
        'settings' => [
            'title' => 'Настройки ИИ',
            'deepseek_api_key' => 'Ключ API Deepseek',
            'deepseek_api_key_note' => 'Введите ваш API ключ от Deepseek для доступа к функциям ИИ',
            'tender_prompt' => 'Промпт для создания тендера',
            'tender_prompt_note' => 'Настройте шаблон для автоматической генерации описаний тендеров',
            'save' => 'Сохранить настройки',
        ],
        'actions' => [
            'generate' => 'Сгенерировать',
            'translate' => 'Перевести',
        ],
    ],

    'smtp' => [
        'title' => 'SMTP настройки',
        'fields' => [
            'host' => 'SMTP хост',
            'port' => 'SMTP порт',
            'encryption' => 'Шифрование',
            'encryption_tls' => 'TLS',
            'encryption_ssl' => 'SSL',
            'encryption_none' => 'Без шифрования',
            'username' => 'Имя пользователя',
            'password' => 'Пароль',
            'from_address' => 'Email отправителя',
            'from_name' => 'Имя отправителя',
        ],
    ],
];

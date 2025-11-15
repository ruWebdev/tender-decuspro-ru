# Тендерная площадка

Веб-приложение для управления закупками и подачи предложений от поставщиков.

## Требования

- PHP 8.2+
- Node.js 16+
- Composer
- PostgreSQL или SQLite

## Установка

### 1. Клонирование репозитория

```bash
git clone <repository-url>
cd tender
```

### 2. Установка зависимостей

```bash
composer install
npm install
```

### 3. Конфигурация

Скопируйте файл конфигурации:

```bash
cp .env.example .env
```

Отредактируйте `.env` файл и установите необходимые переменные:

```env
APP_NAME="Tender Platform"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

# База данных
DB_CONNECTION=sqlite
# или для PostgreSQL:
# DB_CONNECTION=pgsql
# DB_HOST=127.0.0.1
# DB_PORT=5432
# DB_DATABASE=tender
# DB_USERNAME=postgres
# DB_PASSWORD=password

# DeepSeek API
DEEPSEEK_API_KEY=your_deepseek_api_key
DEEPSEEK_BASE_URL=https://api.deepseek.com

# Email
MAIL_MAILER=log
# или для реального отправления:
# MAIL_MAILER=smtp
# MAIL_HOST=smtp.mailtrap.io
# MAIL_PORT=2525
# MAIL_USERNAME=your_username
# MAIL_PASSWORD=your_password
```

### 4. Генерация ключа приложения

```bash
php artisan key:generate
```

### 5. Миграция базы данных

```bash
php artisan migrate
```

### 6. Сборка фронтенда

```bash
npm run build
```

## Запуск

### Разработка

```bash
composer run dev
```

Эта команда запустит:
- Laravel сервер на `http://localhost:8000`
- Queue listener для обработки фоновых задач
- Vite dev server для фронтенда
- Логи приложения

### Продакшн

```bash
php artisan serve
php artisan queue:work
npm run build
```

## Функциональность

### Для заказчиков

- Регистрация и вход
- Создание новых закупок с позициями товаров/услуг
- Автозаполнение позиций через DeepSeek API
- Просмотр списка созданных закупок
- Просмотр предложений от поставщиков
- Сравнение предложений
- Выбор победителя тендера
- Получение уведомлений по email

### Для поставщиков

- Регистрация с информацией о компании
- Загрузка документов (PDF/фото)
- Просмотр активных закупок
- Подача и обновление предложений
- Просмотр лучших цен по позициям
- Просмотр статуса своих заявок
- Получение уведомлений при перебивании цены

### Общие функции

- Главная страница с лентой активных закупок
- Поддержка трёх языков (русский, английский, китайский)
- Переводы через DeepSeek API
- Уведомления по email
- Управление профилем

## Архитектура

### Backend

- **Laravel 12** - фреймворк
- **Inertia.js** - SPA архитектура
- **Sanctum** - аутентификация
- **Eloquent ORM** - работа с БД
- **Queues** - фоновые задачи
- **Notifications** - отправка уведомлений

### Frontend

- **Vue.js 3** - фреймворк
- **Composition API** - логика компонентов
- **Inertia.js** - связь с бэкендом
- **Pinia** - управление состоянием
- **PrimeVue** - UI компоненты
- **Tabler** - стили и иконки

### Интеграции

- **DeepSeek API** - автозаполнение позиций и переводы

## Структура проекта

```
├── app/
│   ├── Http/
│   │   ├── Controllers/     # Контроллеры
│   │   ├── Middleware/      # Middleware
│   │   └── Requests/        # Form Requests
│   ├── Models/              # Eloquent модели
│   ├── Jobs/                # Фоновые задачи
│   ├── Notifications/       # Email уведомления
│   ├── Policies/            # Авторизационные политики
│   └── Services/            # Бизнес-логика
├── database/
│   ├── migrations/          # Миграции БД
│   └── factories/           # Фабрики для тестов
├── resources/
│   ├── js/
│   │   ├── Pages/           # Vue страницы
│   │   ├── Components/      # Vue компоненты
│   │   └── Layouts/         # Layouts
│   └── css/                 # Стили
├── routes/
│   ├── web.php              # Web маршруты
│   └── auth.php             # Auth маршруты
└── tests/                   # Тесты
```

## Разработка

### Создание новой миграции

```bash
php artisan make:migration create_table_name
```

### Создание новой модели

```bash
php artisan make:model ModelName -m
```

### Создание нового контроллера

```bash
php artisan make:controller ControllerName
```

### Запуск тестов

```bash
composer test
```

## Лицензия

MIT

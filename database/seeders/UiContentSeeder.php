<?php

namespace Database\Seeders;

use App\Models\UiContent;
use Illuminate\Database\Seeder;

class UiContentSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            // Hero section
            ['key' => 'home.title', 'ru' => 'Главная', 'en' => 'Home', 'cn' => '主页'],
            ['key' => 'home.hero.kicker', 'ru' => 'ПЛАТФОРМА ЗАКУПОК', 'en' => 'PROCUREMENT PLATFORM', 'cn' => '采购平台'],
            ['key' => 'home.hero.title_main', 'ru' => 'Цифровая система закупок', 'en' => 'Digital Procurement System', 'cn' => '数字化采购系统'],
            ['key' => 'home.hero.title_alt', 'ru' => 'для бизнеса любого масштаба', 'en' => 'for businesses of any size', 'cn' => '适用于各类规模的企业'],
            ['key' => 'home.hero.subtitle', 'ru' => 'Прозрачно. Быстро. Эффективно.', 'en' => 'Transparent. Fast. Efficient.', 'cn' => '透明、迅速、高效。'],
            ['key' => 'home.hero.primary_cta', 'ru' => 'Зарегистрироваться', 'en' => 'Sign up', 'cn' => '注册'],
            ['key' => 'home.hero.secondary_cta', 'ru' => 'Войти', 'en' => 'Log in', 'cn' => '登录'],
            ['key' => 'home.hero.points.one', 'ru' => 'Единая площадка для закупок', 'en' => 'Unified procurement hub', 'cn' => '统一采购平台'],
            ['key' => 'home.hero.points.two', 'ru' => 'Простые и понятные процессы', 'en' => 'Simple and clear processes', 'cn' => '流程简洁清晰'],
            ['key' => 'home.hero.points.three', 'ru' => 'Безопасные сделки', 'en' => 'Secure deals', 'cn' => '安全交易'],

            // Benefits
            ['key' => 'home.benefits.title', 'ru' => 'Преимущества', 'en' => 'Benefits', 'cn' => '优势'],
            ['key' => 'home.benefits.subtitle', 'ru' => 'Почему выбирают нас', 'en' => 'Why choose us', 'cn' => '为何选择我们'],
            ['key' => 'home.benefits.items.transparency.title', 'ru' => 'Прозрачность', 'en' => 'Transparency', 'cn' => '透明性'],
            ['key' => 'home.benefits.items.transparency.text', 'ru' => 'Все этапы закупок прозрачны и доступны.', 'en' => 'All procurement stages are transparent and accessible.', 'cn' => '采购各阶段均透明可查。'],
            ['key' => 'home.benefits.items.efficiency.title', 'ru' => 'Эффективность', 'en' => 'Efficiency', 'cn' => '高效'],
            ['key' => 'home.benefits.items.efficiency.text', 'ru' => 'Сокращаем сроки и издержки.', 'en' => 'We reduce time and costs.', 'cn' => '缩短周期，降低成本。'],
            ['key' => 'home.benefits.items.fair.title', 'ru' => 'Справедливость', 'en' => 'Fairness', 'cn' => '公平'],
            ['key' => 'home.benefits.items.fair.text', 'ru' => 'Равные условия для всех.', 'en' => 'Equal conditions for everyone.', 'cn' => '为所有人提供平等条件。'],
            ['key' => 'home.benefits.items.partnership.title', 'ru' => 'Партнерство', 'en' => 'Partnership', 'cn' => '合作伙伴'],
            ['key' => 'home.benefits.items.partnership.text', 'ru' => 'Долгосрочное сотрудничество и поддержка.', 'en' => 'Long-term cooperation and support.', 'cn' => '长期合作与支持。'],

            // Steps
            ['key' => 'home.steps.title', 'ru' => 'Как это работает', 'en' => 'How it works', 'cn' => '如何运作'],
            ['key' => 'home.steps.subtitle', 'ru' => '4 шага к началу работы', 'en' => '4 steps to get started', 'cn' => '四步即可开始'],
            ['key' => 'home.steps.items.register.title', 'ru' => 'Регистрация', 'en' => 'Registration', 'cn' => '注册'],
            ['key' => 'home.steps.items.register.text', 'ru' => 'Создайте учетную запись.', 'en' => 'Create your account.', 'cn' => '创建账户。'],
            ['key' => 'home.steps.items.requests.title', 'ru' => 'Заявки', 'en' => 'Requests', 'cn' => '申请'],
            ['key' => 'home.steps.items.requests.text', 'ru' => 'Подавайте и обрабатывайте заявки.', 'en' => 'Submit and process requests.', 'cn' => '提交并处理申请。'],
            ['key' => 'home.steps.items.participate.title', 'ru' => 'Участие', 'en' => 'Participation', 'cn' => '参与'],
            ['key' => 'home.steps.items.participate.text', 'ru' => 'Участвуйте в закупках.', 'en' => 'Participate in tenders.', 'cn' => '参与招标。'],
            ['key' => 'home.steps.items.contract.title', 'ru' => 'Контракт', 'en' => 'Contract', 'cn' => '合同'],
            ['key' => 'home.steps.items.contract.text', 'ru' => 'Заключайте сделки.', 'en' => 'Close deals.', 'cn' => '达成交易。'],

            // Tenders section
            ['key' => 'home.tenders.title_section', 'ru' => 'Актуальные тендеры', 'en' => 'Current tenders', 'cn' => '当前招标'],
            ['key' => 'home.tenders.button_all', 'ru' => 'Все тендеры', 'en' => 'All tenders', 'cn' => '全部招标'],
            ['key' => 'home.tenders.filters.search_label', 'ru' => 'Поиск', 'en' => 'Search', 'cn' => '搜索'],
            ['key' => 'home.tenders.filters.search_placeholder', 'ru' => 'Название или описание', 'en' => 'Title or description', 'cn' => '名称或描述'],
            ['key' => 'home.tenders.filters.status_label', 'ru' => 'Статус', 'en' => 'Status', 'cn' => '状态'],
            ['key' => 'home.tenders.filters.deadline_label', 'ru' => 'Дедлайн', 'en' => 'Deadline', 'cn' => '截止日期'],
            ['key' => 'home.tenders.empty_filtered', 'ru' => 'По заданным фильтрам ничего не найдено.', 'en' => 'No results for selected filters.', 'cn' => '所选筛选条件无结果。'],
            ['key' => 'home.tenders.empty_default', 'ru' => 'Пока нет активных тендеров.', 'en' => 'No active tenders yet.', 'cn' => '暂时没有活动招标。'],
            ['key' => 'home.tenders.table.col_number', 'ru' => 'Номер', 'en' => 'Number', 'cn' => '编号'],
            ['key' => 'home.tenders.table.col_name', 'ru' => 'Название', 'en' => 'Name', 'cn' => '名称'],
            ['key' => 'home.tenders.table.col_description', 'ru' => 'Описание', 'en' => 'Description', 'cn' => '描述'],
            ['key' => 'home.tenders.table.col_deadline', 'ru' => 'Дедлайн', 'en' => 'Deadline', 'cn' => '截止日期'],
            ['key' => 'home.tenders.table.col_status', 'ru' => 'Статус', 'en' => 'Status', 'cn' => '状态'],
            ['key' => 'home.tenders.table.col_actions', 'ru' => 'Действия', 'en' => 'Actions', 'cn' => '操作'],
            ['key' => 'home.tenders.table.no_description', 'ru' => 'Описание отсутствует', 'en' => 'No description', 'cn' => '无描述'],
            ['key' => 'home.button_details', 'ru' => 'Подробнее', 'en' => 'Details', 'cn' => '详情'],
            ['key' => 'home.button_participate', 'ru' => 'Участвовать', 'en' => 'Participate', 'cn' => '参与'],

            // FAQ
            ['key' => 'home.faq.kicker', 'ru' => 'ВОПРОСЫ И ОТВЕТЫ', 'en' => 'FAQ', 'cn' => '常见问题'],
            ['key' => 'home.faq.title', 'ru' => 'Вопросы и ответы', 'en' => 'Frequently asked questions', 'cn' => '常见问题'],
            ['key' => 'home.faq.subtitle', 'ru' => 'Самое важное — коротко', 'en' => 'The most important — briefly', 'cn' => '要点简述'],
            ['key' => 'home.faq.items.registration.question', 'ru' => 'Как зарегистрироваться?', 'en' => 'How to register?', 'cn' => '如何注册？'],
            ['key' => 'home.faq.items.registration.answer', 'ru' => 'Нажмите «Зарегистрироваться» и следуйте инструкциям.', 'en' => 'Click “Sign up” and follow the instructions.', 'cn' => '点击“注册”并按指引操作。'],
            ['key' => 'home.faq.items.documents.question', 'ru' => 'Какие документы нужны?', 'en' => 'What documents are required?', 'cn' => '需要哪些文件？'],
            ['key' => 'home.faq.items.documents.answer', 'ru' => 'Минимальный набор: данные компании и контактная информация.', 'en' => 'Minimum: company data and contact info.', 'cn' => '最少：公司资料及联系方式。'],
            ['key' => 'home.faq.items.criteria.question', 'ru' => 'Как оцениваются заявки?', 'en' => 'How are requests evaluated?', 'cn' => '如何评估申请？'],
            ['key' => 'home.faq.items.criteria.answer', 'ru' => 'По прозрачным критериям, описанным в тендере.', 'en' => 'By transparent criteria described in the tender.', 'cn' => '依据招标说明中的透明标准。'],
            ['key' => 'home.faq.items.contract.question', 'ru' => 'Как заключается контракт?', 'en' => 'How is the contract signed?', 'cn' => '如何签署合同？'],
            ['key' => 'home.faq.items.contract.answer', 'ru' => 'После выбора победителя стороны подписывают договор.', 'en' => 'After selecting the winner, the parties sign a contract.', 'cn' => '选定中标方后，双方签订合同。'],
            ['key' => 'home.faq.items.contact.question', 'ru' => 'Как связаться с поддержкой?', 'en' => 'How to contact support?', 'cn' => '如何联系支持？'],
            ['key' => 'home.faq.items.contact.answer', 'ru' => 'Используйте раздел контактов ниже.', 'en' => 'Use the contacts section below.', 'cn' => '请使用下方联系方式。'],

            // Contacts
            ['key' => 'home.contacts.kicker', 'ru' => 'КОНТАКТЫ', 'en' => 'CONTACTS', 'cn' => '联系方式'],
            ['key' => 'home.contacts.title', 'ru' => 'Свяжитесь с нами', 'en' => 'Get in touch', 'cn' => '联系我们'],
            ['key' => 'home.contacts.technical.label', 'ru' => 'Техподдержка', 'en' => 'Tech support', 'cn' => '技术支持'],
            ['key' => 'home.contacts.technical.value', 'ru' => 'tech@example.com', 'en' => 'tech@example.com', 'cn' => 'tech@example.com'],
            ['key' => 'home.contacts.commercial.label', 'ru' => 'Коммерческие вопросы', 'en' => 'Sales & commercial', 'cn' => '商务咨询'],
            ['key' => 'home.contacts.commercial.value', 'ru' => 'sales@example.com', 'en' => 'sales@example.com', 'cn' => 'sales@example.com'],
            ['key' => 'home.contacts.phone.label', 'ru' => 'Телефон', 'en' => 'Phone', 'cn' => '电话'],
            ['key' => 'home.contacts.phone.value', 'ru' => '+7 (000) 000-00-00', 'en' => '+7 (000) 000-00-00', 'cn' => '+7 (000) 000-00-00'],
            ['key' => 'home.contacts.manager.label', 'ru' => 'Менеджер', 'en' => 'Manager', 'cn' => '客户经理'],
            ['key' => 'home.contacts.manager.value', 'ru' => 'Иван Иванов', 'en' => 'John Doe', 'cn' => '张伟'],
            ['key' => 'home.contacts.schedule.label', 'ru' => 'График', 'en' => 'Schedule', 'cn' => '时间'],
            ['key' => 'home.contacts.schedule.value', 'ru' => 'Пн-Пт, 9:00-18:00', 'en' => 'Mon-Fri, 9:00-18:00', 'cn' => '周一至周五 9:00-18:00'],
            ['key' => 'home.contacts.support_title', 'ru' => 'Служба поддержки', 'en' => 'Support service', 'cn' => '客服支持'],
            ['key' => 'home.contacts.support_text', 'ru' => 'Мы всегда готовы помочь вам с любыми вопросами.', 'en' => 'We are always ready to help you with any questions.', 'cn' => '我们随时为您的问题提供帮助。'],
        ];

        foreach ($rows as $row) {
            UiContent::updateOrCreate(
                ['key' => $row['key']],
                [
                    'value_ru' => $row['ru'],
                    'value_en' => $row['en'],
                    'value_cn' => $row['cn'],
                ]
            );
        }
    }
}

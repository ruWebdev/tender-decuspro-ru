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
            ['key' => 'home.hero.title_main', 'ru' => 'Прозрачные закупки', 'en' => 'Transparent Procurement', 'cn' => '透明采购'],
            ['key' => 'home.hero.title_alt', 'ru' => 'просто и удобно', 'en' => 'Made Simple', 'cn' => '简单便捷'],
            ['key' => 'home.hero.subtitle', 'ru' => 'Находите возможности, подавайте заявки и развивайте свой бизнес с нашей современной платформой', 'en' => 'Discover opportunities, submit bids, and grow your business with our modern tendering platform', 'cn' => '发现机会，提交投标，通过我们的现代招标平台发展您的业务'],
            ['key' => 'home.hero.view_tenders', 'ru' => 'Смотреть тендеры', 'en' => 'View Live Tenders', 'cn' => '查看招标'],
            ['key' => 'home.hero.submit_bid', 'ru' => 'Подать заявку', 'en' => 'Submit a Bid', 'cn' => '提交投标'],

            // Stats section
            ['key' => 'home.stats.tenders.value', 'ru' => '500+', 'en' => '500+', 'cn' => '500+'],
            ['key' => 'home.stats.tenders.label', 'ru' => 'Активных тендеров', 'en' => 'Active Tenders', 'cn' => '活跃招标'],
            ['key' => 'home.stats.vendors.value', 'ru' => '1200+', 'en' => '1200+', 'cn' => '1200+'],
            ['key' => 'home.stats.vendors.label', 'ru' => 'Зарегистрированных поставщиков', 'en' => 'Registered Vendors', 'cn' => '注册供应商'],
            ['key' => 'home.stats.total_value.value', 'ru' => '¥50M+', 'en' => '¥50M+', 'cn' => '¥50M+'],
            ['key' => 'home.stats.total_value.label', 'ru' => 'Общая стоимость', 'en' => 'Total Value', 'cn' => '总价值'],
            ['key' => 'home.stats.success_rate.value', 'ru' => '98%', 'en' => '98%', 'cn' => '98%'],
            ['key' => 'home.stats.success_rate.label', 'ru' => 'Успешных сделок', 'en' => 'Success Rate', 'cn' => '成功率'],

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
            ['key' => 'home.tenders.title_section', 'ru' => 'Открытые тендеры', 'en' => 'Open Tenders', 'cn' => '公开招标'],
            ['key' => 'home.tenders.subtitle', 'ru' => 'Изучите текущие возможности и найдите идеальное предложение для вашего бизнеса', 'en' => 'Explore current opportunities and find the perfect match for your business', 'cn' => '探索当前机会，为您的业务找到完美匹配'],
            ['key' => 'home.tenders.filters.search_placeholder', 'ru' => 'Поиск тендеров...', 'en' => 'Search tenders...', 'cn' => '搜索招标...'],
            ['key' => 'home.tenders.filters.filter_button', 'ru' => 'Фильтр', 'en' => 'Filter', 'cn' => '筛选'],
            ['key' => 'home.tenders.filters.category_filters.all', 'ru' => 'Все категории', 'en' => 'All Categories', 'cn' => '所有类别'],
            ['key' => 'home.tenders.filters.category_filters.construction', 'ru' => 'Строительство', 'en' => 'Construction', 'cn' => '建筑'],
            ['key' => 'home.tenders.filters.category_filters.technology', 'ru' => 'Технологии', 'en' => 'Technology', 'cn' => '技术'],
            ['key' => 'home.tenders.filters.category_filters.healthcare', 'ru' => 'Здравоохранение', 'en' => 'Healthcare', 'cn' => '医疗'],
            ['key' => 'home.tenders.filters.category_filters.education', 'ru' => 'Образование', 'en' => 'Education', 'cn' => '教育'],
            ['key' => 'home.tenders.filters.status_filters.all', 'ru' => 'Все статусы', 'en' => 'All Statuses', 'cn' => '所有状态'],
            ['key' => 'home.tenders.filters.status_filters.open', 'ru' => 'Открыт', 'en' => 'Open', 'cn' => '开放'],
            ['key' => 'home.tenders.filters.status_filters.review', 'ru' => 'На рассмотрении', 'en' => 'Under Review', 'cn' => '审核中'],
            ['key' => 'home.tenders.filters.status_filters.closed', 'ru' => 'Закрыт', 'en' => 'Closed', 'cn' => '已关闭'],
            ['key' => 'home.tenders.no_results_title', 'ru' => 'Тендеры не найдены', 'en' => 'No tenders found', 'cn' => '未找到招标'],
            ['key' => 'home.tenders.empty_filtered', 'ru' => 'Попробуйте изменить критерии поиска', 'en' => 'Try adjusting your search criteria', 'cn' => '请尝试调整搜索条件'],
            ['key' => 'home.tenders.empty_default', 'ru' => 'Пока нет активных тендеров.', 'en' => 'No active tenders yet.', 'cn' => '暂时没有活动招标。'],
            ['key' => 'home.tenders.table.no_description', 'ru' => 'Описание отсутствует', 'en' => 'No description', 'cn' => '无描述'],
            ['key' => 'home.tenders.card.organization_default', 'ru' => 'Организация', 'en' => 'Organization', 'cn' => '组织'],
            ['key' => 'home.tenders.card.value', 'ru' => 'Стоимость', 'en' => 'Value', 'cn' => '价值'],
            ['key' => 'home.tenders.card.closing', 'ru' => 'Закрытие', 'en' => 'Closing', 'cn' => '截止'],
            ['key' => 'home.button_details', 'ru' => 'Подробнее', 'en' => 'View Details', 'cn' => '查看详情'],
            ['key' => 'home.button_participate', 'ru' => 'Участвовать', 'en' => 'Participate', 'cn' => '参与'],

            // Closing Soon section
            ['key' => 'home.closing_soon.title', 'ru' => 'Скоро закрытие', 'en' => 'Closing Soon', 'cn' => '即将截止'],
            ['key' => 'home.closing_soon.subtitle', 'ru' => 'Не упустите эти срочные возможности', 'en' => 'Don\'t miss these time-sensitive opportunities', 'cn' => '不要错过这些时间敏感的机会'],
            ['key' => 'home.closing_soon.closing_in', 'ru' => 'Закрытие через', 'en' => 'Closing in', 'cn' => '剩余时间'],
            ['key' => 'home.closing_soon.days', 'ru' => 'д ', 'en' => 'd ', 'cn' => '天 '],
            ['key' => 'home.closing_soon.hours', 'ru' => 'ч ', 'en' => 'h ', 'cn' => '时 '],
            ['key' => 'home.closing_soon.minutes', 'ru' => 'м', 'en' => 'm', 'cn' => '分'],

            // CTA section
            ['key' => 'home.cta.title', 'ru' => 'Готовы начать?', 'en' => 'Ready to Start Bidding?', 'cn' => '准备开始投标？'],
            ['key' => 'home.cta.subtitle', 'ru' => 'Присоединяйтесь к тысячам поставщиков, которые доверяют нашей платформе', 'en' => 'Join thousands of vendors who trust our platform for their business growth', 'cn' => '加入数千家信任我们平台的供应商'],
            ['key' => 'home.cta.register_button', 'ru' => 'Зарегистрироваться', 'en' => 'Register Now', 'cn' => '立即注册'],
            ['key' => 'home.cta.learn_more_button', 'ru' => 'Узнать больше', 'en' => 'Learn More', 'cn' => '了解更多'],

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
            ['key' => 'home.contacts.phone.value', 'ru' => '+7 (000) 000-00-00', 'en' => '+1 (555) 123-4567', 'cn' => '+7 (000) 000-00-00'],
            ['key' => 'home.contacts.technical.value', 'ru' => 'info@tenderhub.com', 'en' => 'info@tenderhub.com', 'cn' => 'info@tenderhub.com'],

            // Footer
            ['key' => 'home.footer.description', 'ru' => 'Ведущая платформа для прозрачных и эффективных закупочных процессов.', 'en' => 'The leading platform for transparent and efficient procurement processes.', 'cn' => '领先的透明高效采购流程平台。'],
            ['key' => 'home.footer.quick_links.title', 'ru' => 'Быстрые ссылки', 'en' => 'Quick Links', 'cn' => '快速链接'],
            ['key' => 'home.footer.quick_links.about', 'ru' => 'О нас', 'en' => 'About Us', 'cn' => '关于我们'],
            ['key' => 'home.footer.quick_links.how_it_works', 'ru' => 'Как это работает', 'en' => 'How It Works', 'cn' => '如何运作'],
            ['key' => 'home.footer.quick_links.pricing', 'ru' => 'Цены', 'en' => 'Pricing', 'cn' => '价格'],
            ['key' => 'home.footer.quick_links.support', 'ru' => 'Поддержка', 'en' => 'Support', 'cn' => '支持'],
            ['key' => 'home.footer.legal.title', 'ru' => 'Правовая информация', 'en' => 'Legal', 'cn' => '法律信息'],
            ['key' => 'home.footer.legal.privacy', 'ru' => 'Политика конфиденциальности', 'en' => 'Privacy Policy', 'cn' => '隐私政策'],
            ['key' => 'home.footer.legal.terms', 'ru' => 'Условия использования', 'en' => 'Terms of Service', 'cn' => '服务条款'],
            ['key' => 'home.footer.legal.cookies', 'ru' => 'Политика cookies', 'en' => 'Cookie Policy', 'cn' => 'Cookie政策'],
            ['key' => 'home.footer.legal.compliance', 'ru' => 'Соответствие', 'en' => 'Compliance', 'cn' => '合规'],
            ['key' => 'home.footer.contact.title', 'ru' => 'Контакты', 'en' => 'Contact Info', 'cn' => '联系信息'],
            ['key' => 'home.footer.contact.address', 'ru' => 'ул. Бизнес, 123, офис 100', 'en' => '123 Business Ave, Suite 100', 'cn' => '商业大道123号，100室'],
            ['key' => 'home.footer.rights', 'ru' => 'Все права защищены.', 'en' => 'All rights reserved.', 'cn' => '保留所有权利。'],

            // Navigation
            ['key' => 'nav.home', 'ru' => 'Главная', 'en' => 'Home', 'cn' => '首页'],
            ['key' => 'nav.tenders', 'ru' => 'Тендеры', 'en' => 'Tenders', 'cn' => '招标'],
            ['key' => 'nav.create_tender', 'ru' => 'Создать тендер', 'en' => 'Create Tender', 'cn' => '创建招标'],
            ['key' => 'nav.my_proposals', 'ru' => 'Мои заявки', 'en' => 'My Proposals', 'cn' => '我的投标'],
            ['key' => 'nav.login', 'ru' => 'Войти', 'en' => 'Login', 'cn' => '登录'],
            ['key' => 'nav.register', 'ru' => 'Регистрация', 'en' => 'Register', 'cn' => '注册'],
            ['key' => 'nav.dashboard', 'ru' => 'Панель управления', 'en' => 'Dashboard', 'cn' => '控制面板'],
            ['key' => 'nav.admin_panel', 'ru' => 'Админ панель', 'en' => 'Admin Panel', 'cn' => '管理面板'],
            ['key' => 'nav.supplier_profile', 'ru' => 'Профиль поставщика', 'en' => 'Supplier Profile', 'cn' => '供应商资料'],
            ['key' => 'nav.logout', 'ru' => 'Выйти', 'en' => 'Logout', 'cn' => '退出'],

            // Tender statuses
            ['key' => 'home.tenders.status.open', 'ru' => 'Открыт', 'en' => 'Open', 'cn' => '开放'],
            ['key' => 'home.tenders.status.review', 'ru' => 'На рассмотрении', 'en' => 'Under Review', 'cn' => '审核中'],
            ['key' => 'home.tenders.status.closed', 'ru' => 'Закрыт', 'en' => 'Closed', 'cn' => '已关闭'],
            ['key' => 'home.tenders.status.closing', 'ru' => 'Скоро закрытие', 'en' => 'Closing Soon', 'cn' => '即将截止'],
            ['key' => 'home.tenders.status.urgent', 'ru' => 'Срочно', 'en' => 'Urgent', 'cn' => '紧急'],
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

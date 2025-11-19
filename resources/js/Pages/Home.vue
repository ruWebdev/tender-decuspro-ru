<script setup>
import { computed, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useTranslations } from '@/Composables/useTranslations';

const page = usePage();
const { t } = useTranslations();

const tenders = computed(() => page.props.tenders || []);
const authUser = computed(() => page.props.auth?.user || null);
const isSupplier = computed(() => authUser.value?.role === 'supplier');
const currentLocale = computed(() => page.props.locale || 'ru');

const jsLocale = computed(() => {
  if (currentLocale.value === 'en') {
    return 'en-US';
  }

  if (currentLocale.value === 'cn') {
    return 'zh-CN';
  }

  return 'ru-RU';
});

const formatDate = (value) => {
  if (!value) {
    return '-';
  }

  return new Date(value).toLocaleDateString(jsLocale.value, {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
  });
};

const getTenderDescription = (tender) => {
  if (currentLocale.value === 'en' && tender.description_en) {
    return tender.description_en;
  }

  if (currentLocale.value === 'cn' && tender.description_cn) {
    return tender.description_cn;
  }

  return tender.description || '';
};

const statusFilter = ref('');
const searchQuery = ref('');
const deadlineFilter = ref('');
const faqOpenIndex = ref(0);

const hasFilters = computed(() => Boolean(statusFilter.value || searchQuery.value || deadlineFilter.value));

const filteredTenders = computed(() => {
  return tenders.value.filter((tender) => {
    if (statusFilter.value && tender.status !== statusFilter.value) {
      return false;
    }

    if (searchQuery.value) {
      const term = searchQuery.value.toLowerCase();
      const title = (tender.title || '').toLowerCase();
      const description = getTenderDescription(tender).toLowerCase();
      if (!title.includes(term) && !description.includes(term)) {
        return false;
      }
    }

    if (deadlineFilter.value) {
      if (!tender.valid_until) {
        return false;
      }

      const tenderDeadline = new Date(tender.valid_until);
      const filterDate = new Date(`${deadlineFilter.value}T23:59:59`);
      if (tenderDeadline > filterDate) {
        return false;
      }
    }

    return true;
  });
});

const heroHighlights = computed(() => [
  t('home.hero.points.one'),
  t('home.hero.points.two'),
  t('home.hero.points.three'),
]);

const benefitItems = computed(() => [
  {
    id: 'transparency',
    icon: '🔍',
    title: t('home.benefits.items.transparency.title'),
    text: t('home.benefits.items.transparency.text'),
  },
  {
    id: 'efficiency',
    icon: '⚡',
    title: t('home.benefits.items.efficiency.title'),
    text: t('home.benefits.items.efficiency.text'),
  },
  {
    id: 'fair',
    icon: '⚖️',
    title: t('home.benefits.items.fair.title'),
    text: t('home.benefits.items.fair.text'),
  },
  {
    id: 'partnership',
    icon: '🤝',
    title: t('home.benefits.items.partnership.title'),
    text: t('home.benefits.items.partnership.text'),
  },
]);

const stepItems = computed(() => [
  {
    id: 'register',
    title: t('home.steps.items.register.title'),
    text: t('home.steps.items.register.text'),
  },
  {
    id: 'requests',
    title: t('home.steps.items.requests.title'),
    text: t('home.steps.items.requests.text'),
  },
  {
    id: 'participate',
    title: t('home.steps.items.participate.title'),
    text: t('home.steps.items.participate.text'),
  },
  {
    id: 'contract',
    title: t('home.steps.items.contract.title'),
    text: t('home.steps.items.contract.text'),
  },
]);

const faqItems = computed(() => [
  {
    id: 'registration',
    question: t('home.faq.items.registration.question'),
    answer: t('home.faq.items.registration.answer'),
  },
  {
    id: 'documents',
    question: t('home.faq.items.documents.question'),
    answer: t('home.faq.items.documents.answer'),
  },
  {
    id: 'criteria',
    question: t('home.faq.items.criteria.question'),
    answer: t('home.faq.items.criteria.answer'),
  },
  {
    id: 'contract',
    question: t('home.faq.items.contract.question'),
    answer: t('home.faq.items.contract.answer'),
  },
  {
    id: 'contact',
    question: t('home.faq.items.contact.question'),
    answer: t('home.faq.items.contact.answer'),
  },
]);

const contactRows = computed(() => [
  {
    label: t('home.contacts.technical.label'),
    value: t('home.contacts.technical.value'),
    href: `mailto:${t('home.contacts.technical.value')}`,
  },
  {
    label: t('home.contacts.commercial.label'),
    value: t('home.contacts.commercial.value'),
    href: `mailto:${t('home.contacts.commercial.value')}`,
  },
  {
    label: t('home.contacts.phone.label'),
    value: t('home.contacts.phone.value'),
    href: `tel:${t('home.contacts.phone.value').replace(/[^+\d]/g, '')}`,
  },
  {
    label: t('home.contacts.manager.label'),
    value: t('home.contacts.manager.value'),
  },
  {
    label: t('home.contacts.schedule.label'),
    value: t('home.contacts.schedule.value'),
  },
]);

const tenderStatusLabel = (status) => {
  if (!status) {
    return '';
  }

  const key = `home.tenders.status.${status}`;
  const translation = t(key);
  return translation === key ? status : translation;
};

const statusBadgeClass = (status) => {
  if (status === 'open') {
    return 'bg-success text-white';
  }

  if (status === 'review') {
    return 'bg-warning text-dark';
  }

  if (status === 'closed') {
    return 'bg-secondary';
  }

  return 'bg-dark';
};

const toggleFaq = (index) => {
  faqOpenIndex.value = faqOpenIndex.value === index ? null : index;
};

const statusOptions = computed(() => [
  {
    value: '',
    label: t('home.tenders.filters.status_filters.all'),
  },
  {
    value: 'open',
    label: t('home.tenders.filters.status_filters.open'),
  },
  {
    value: 'review',
    label: t('home.tenders.filters.status_filters.review'),
  },
  {
    value: 'closed',
    label: t('home.tenders.filters.status_filters.closed'),
  },
]);

</script>

<template>
  <AppLayout>
    <div class="home-page pb-5">
      <section class="hero-banner py-5">
        <div class="container">
          <div class="row align-items-center">
            <div class="col-lg-7">
              <p class="hero-kicker text-uppercase mb-2">{{ t('home.hero.kicker') }}</p>
              <h1 class="hero-title mb-3">
                {{ t('home.hero.title_main') }}
                <span class="d-block text-accent mt-4">{{ t('home.hero.title_alt') }}</span>
              </h1>
              <p class="hero-subtitle mb-4">{{ t('home.hero.subtitle') }}</p>
              <div class="d-flex flex-wrap gap-3 mb-4">
                <Link :href="route('register')" class="btn btn-primary btn-lg px-4">
                {{ t('home.hero.primary_cta') }}
                </Link>
                <Link :href="route('login')" class="btn btn-outline-light btn-lg px-4 text-white">
                {{ t('home.hero.secondary_cta') }}
                </Link>
              </div>
              <ul class="hero-list">
                <li v-for="(item, index) in heroHighlights" :key="index">{{ item }}</li>
              </ul>
            </div>
            <div class="col-lg-5 mt-4 mt-lg-0">
              <div class="hero-card p-4">
                <p class="text-uppercase small text-muted mb-3">{{ t('home.title') }}</p>
                <div class="hero-metric" v-for="metric in benefitItems" :key="metric.id">
                  <span class="hero-metric-icon">{{ metric.icon }}</span>
                  <div>
                    <p class="mb-0 fw-semibold text-dark">{{ metric.title }}</p>
                    <small class="text-muted">{{ metric.text }}</small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="section-padding">
        <div class="container">
          <div class="section-heading text-center mb-5">
            <h2 class="h3 mb-3">{{ t('home.benefits.title') }}</h2>
            <p class="text-muted mb-0">{{ t('home.benefits.subtitle') }}</p>
          </div>
          <div class="row g-4">
            <div v-for="benefit in benefitItems" :key="benefit.id" class="col-md-6 col-xl-3">
              <div class="benefit-card h-100 p-4">
                <div class="benefit-icon">{{ benefit.icon }}</div>
                <h3 class="h5 mt-3 mb-2">{{ benefit.title }}</h3>
                <p class="text-muted mb-0">{{ benefit.text }}</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="section-padding bg-light">
        <div class="container">
          <div class="section-heading text-center mb-5">
            <h2 class="h3 mb-3">{{ t('home.steps.title') }}</h2>
            <p class="text-muted mb-0">{{ t('home.steps.subtitle') }}</p>
          </div>
          <div class="row g-4">
            <div v-for="(step, index) in stepItems" :key="step.id" class="col-md-6 col-xl-3">
              <div class="step-card h-100 p-4">
                <div class="step-number">0{{ index + 1 }}</div>
                <h3 class="h5 mb-2">{{ step.title }}</h3>
                <p class="text-muted mb-0">{{ step.text }}</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="section-padding">
        <div class="container">
          <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
              <h2 class="h3 mb-0">{{ t('home.tenders.title_section') }}</h2>
            </div>
            <Link :href="route('tenders.index')" class="btn btn-outline-primary">
            {{ t('home.tenders.button_all') }}
            </Link>
          </div>

          <div class="row g-3 mb-4">
            <div class="col-md-4">
              <label class="form-label">{{ t('home.tenders.filters.search_label') }}</label>
              <input v-model="searchQuery" type="text" class="form-control"
                :placeholder="t('home.tenders.filters.search_placeholder')">
            </div>
            <div class="col-md-4">
              <label class="form-label">{{ t('home.tenders.filters.status_label') }}</label>
              <select v-model="statusFilter" class="form-select">
                <option v-for="option in statusOptions" :key="option.value" :value="option.value">
                  {{ option.label }}
                </option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">{{ t('home.tenders.filters.deadline_label') }}</label>
              <input v-model="deadlineFilter" type="date" class="form-control">
            </div>
          </div>

          <div v-if="filteredTenders.length === 0" class="alert alert-info">
            {{ hasFilters ? t('home.tenders.empty_filtered') : t('home.tenders.empty_default') }}
          </div>

          <div v-else class="table-responsive">
            <table class="table align-middle">
              <thead>
                <tr>
                  <th>{{ t('home.tenders.table.col_number') }}</th>
                  <th>{{ t('home.tenders.table.col_name') }}</th>
                  <th>{{ t('home.tenders.table.col_description') }}</th>
                  <th>{{ t('home.tenders.table.col_deadline') }}</th>
                  <th>{{ t('home.tenders.table.col_status') }}</th>
                  <th class="text-end">{{ t('home.tenders.table.col_actions') }}</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="tender in filteredTenders" :key="tender.id">
                  <td class="text-muted">{{ tender.id }}</td>
                  <td>
                    <p class="mb-1 fw-semibold">{{ tender.title }}</p>
                    <small class="text-muted">{{ t('tenders.field_created_at') }}: {{ formatDate(tender.created_at)
                    }}</small>
                  </td>
                  <td>
                    <p class="mb-0 text-muted">{{ getTenderDescription(tender) || t('home.tenders.table.no_description')
                    }}</p>
                  </td>
                  <td>{{ formatDate(tender.valid_until) }}</td>
                  <td>
                    <span class="badge" :class="statusBadgeClass(tender.status)">
                      {{ tenderStatusLabel(tender.status) }}
                    </span>
                  </td>
                  <td>
                    <div class="d-flex justify-content-end gap-2">
                      <Link :href="route('tenders.show', { tender: tender.id })" class="btn btn-sm btn-outline-primary">
                      {{ t('home.button_details') }}
                      </Link>
                      <Link v-if="isSupplier" :href="route('proposals.participate', { tender: tender.id })"
                        class="btn btn-sm btn-success">
                      {{ t('home.button_participate') }}
                      </Link>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <section class="section-padding bg-light">
        <div class="container">
          <div class="row g-4 align-items-center">
            <div class="col-lg-5">
              <div class="section-heading mb-4">
                <p class="text-uppercase text-muted small mb-2">{{ t('home.faq.kicker') }}</p>
                <h2 class="h3 mb-3">{{ t('home.faq.title') }}</h2>
                <p class="text-muted mb-0">{{ t('home.faq.subtitle') }}</p>
              </div>
            </div>
            <div class="col-lg-7">
              <div class="faq-list">
                <div v-for="(item, index) in faqItems" :key="item.id" class="faq-item">
                  <button class="faq-question" type="button" @click="toggleFaq(index)"
                    :aria-expanded="faqOpenIndex === index">
                    <span>{{ item.question }}</span>
                    <span class="faq-icon" :class="{ 'is-open': faqOpenIndex === index }">+</span>
                  </button>
                  <div v-if="faqOpenIndex === index" class="faq-answer">
                    <p class="mb-0">{{ item.answer }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="section-padding">
        <div class="container">
          <div class="row g-4">
            <div class="col-lg-6">
              <div class="contact-card h-100 p-4">
                <p class="text-uppercase text-muted small mb-2">{{ t('home.contacts.kicker') }}</p>
                <h2 class="h3 mb-4">{{ t('home.contacts.title') }}</h2>
                <div class="contact-row" v-for="row in contactRows" :key="row.label">
                  <div>
                    <p class="mb-1 fw-semibold">{{ row.label }}</p>
                    <p class="mb-0 text-muted" v-if="!row.href">{{ row.value }}</p>
                    <a v-else :href="row.href" class="text-decoration-none">{{ row.value }}</a>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="contact-support h-100 p-4">
                <h3 class="h4 mb-3">{{ t('home.contacts.support_title') }}</h3>
                <p class="text-muted">{{ t('home.contacts.support_text') }}</p>
                <ul class="list-unstyled mb-0">
                  <li class="d-flex align-items-center gap-2 mb-2">
                    <span class="support-icon">☎</span>
                    <span>{{ t('home.contacts.phone.value') }}</span>
                  </li>
                  <li class="d-flex align-items-center gap-2 mb-2">
                    <span class="support-icon">✉</span>
                    <span>{{ t('home.contacts.technical.value') }}</span>
                  </li>
                  <li class="d-flex align-items-center gap-2">
                    <span class="support-icon">👤</span>
                    <span>{{ t('home.contacts.manager.value') }}</span>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </section>

    </div>
  </AppLayout>
</template>

<style scoped>
.home-page {
  background-color: #f8f9fb;
  margin-top: -3rem;
  padding-top: 1.5rem;
}

.hero-banner {
  background: radial-gradient(circle at top right, rgba(13, 110, 253, 0.15), transparent 55%),
    linear-gradient(135deg, #0d6efd, #6610f2);
  color: #fff;
}

.hero-title {
  font-size: 2rem;
  font-weight: 700;
}

.hero-subtitle {
  max-width: 540px;
  font-size: 1.1rem;
}

.hero-kicker {
  letter-spacing: 0.2em;
  font-size: 0.8rem;
}

.hero-list {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.hero-list li {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.hero-list li::before {
  content: '•';
  color: #ffdd57;
  font-size: 1.5rem;
}

.hero-card {
  background: #fff;
  border-radius: 1rem;
  box-shadow: 0 20px 60px rgba(14, 23, 38, 0.2);
}

.hero-metric {
  display: flex;
  gap: 0.75rem;
  padding: 0.75rem 0;
  border-bottom: 1px solid #f0f2f5;
}

.hero-metric:last-child {
  border-bottom: none;
}

.hero-metric-icon {
  font-size: 1.5rem;
}

.section-padding {
  padding: 4rem 0;
}

.section-heading {
  max-width: 640px;
  margin: 0 auto;
}

.benefit-card {
  background: #fff;
  border-radius: 1rem;
  border: 1px solid #e9ecef;
  height: 100%;
}

.benefit-icon {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: rgba(13, 110, 253, 0.1);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
}

.step-card {
  background: #fff;
  border: 1px solid #e9ecef;
  border-radius: 1rem;
}

.step-number {
  font-size: 2rem;
  font-weight: 700;
  color: #0d6efd;
  margin-bottom: 1rem;
}

.faq-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.faq-item {
  border-radius: 1rem;
  background: #fff;
  border: 1px solid #e9ecef;
}

.faq-question {
  width: 100%;
  background: transparent;
  border: none;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.25rem 1.5rem;
  font-weight: 600;
}

.faq-icon {
  transition: transform 0.2s ease;
}

.faq-icon.is-open {
  transform: rotate(45deg);
}

.faq-answer {
  padding: 0 1.5rem 1.25rem;
  color: #6c757d;
}

.contact-card,
.contact-support {
  border-radius: 1rem;
  background: #fff;
  border: 1px solid #e9ecef;
}

.contact-row {
  border-bottom: 1px solid #f0f2f5;
  padding: 1rem 0;
}

.contact-row:last-child {
  border-bottom: none;
}

.contact-row a {
  color: #0d6efd;
}

.support-icon {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: rgba(13, 110, 253, 0.1);
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.text-accent {
  color: #ffdd57;
}

@media (max-width: 767.98px) {
  .hero-title {
    font-size: 2rem;
  }

  .section-padding {
    padding: 3rem 0;
  }
}
</style>

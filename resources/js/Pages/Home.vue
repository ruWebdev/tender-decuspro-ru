<script setup>
import { computed, ref } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useTranslations } from '@/Composables/useTranslations';

const page = usePage();
const { t } = useTranslations();

const tenders = computed(() => page.props.tenders || []);
const currentLocale = computed(() => page.props.locale || 'ru');
const siteSettings = computed(() => page.props.site_settings || {});

const jsLocale = computed(() => {
  if (currentLocale.value === 'en') return 'en-US';
  if (currentLocale.value === 'cn') return 'zh-CN';
  return 'ru-RU';
});

const formatDate = (value) => {
  if (!value) return '-';
  return new Date(value).toLocaleDateString(jsLocale.value, {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
  });
};

const getTenderDescription = (tender) => {
  if (currentLocale.value === 'en' && tender.description_en) return tender.description_en;
  if (currentLocale.value === 'cn' && tender.description_cn) return tender.description_cn;
  return tender.description || '';
};

const searchQuery = ref('');
const faqOpenIndex = ref(null);

const hasFilters = computed(() => Boolean(searchQuery.value));

const filteredTenders = computed(() => {
  return tenders.value.filter((tender) => {
    if (searchQuery.value) {
      const term = searchQuery.value.toLowerCase();
      const title = (tender.title || '').toLowerCase();
      const description = getTenderDescription(tender).toLowerCase();
      if (!title.includes(term) && !description.includes(term)) return false;
    }
    return true;
  });
});

const displayedTenders = computed(() => filteredTenders.value.slice(0, 6));

const faqItems = computed(() => [
  { id: 'registration', question: t('home.faq.items.registration.question'), answer: t('home.faq.items.registration.answer') },
  { id: 'documents', question: t('home.faq.items.documents.question'), answer: t('home.faq.items.documents.answer') },
  { id: 'criteria', question: t('home.faq.items.criteria.question'), answer: t('home.faq.items.criteria.answer') },
  { id: 'contract', question: t('home.faq.items.contract.question'), answer: t('home.faq.items.contract.answer') },
  { id: 'contact', question: t('home.faq.items.contact.question'), answer: t('home.faq.items.contact.answer') },
]);

const statsItems = computed(() => [
  { value: siteSettings.value.stats_tenders || '500+', label: t('home.stats.tenders.label') },
  { value: siteSettings.value.stats_vendors || '1200+', label: t('home.stats.vendors.label') },
  { value: siteSettings.value.stats_total_value || '¥50M+', label: t('home.stats.total_value.label') },
  { value: siteSettings.value.stats_success_rate || '98%', label: t('home.stats.success_rate.label') },
]);


const getStatusClass = (status) => {
  if (status === 'open') return 'status-open';
  if (status === 'review' || status === 'closing') return 'status-closing';
  if (status === 'closed' || status === 'urgent') return 'status-urgent';
  return 'status-open';
};

const getStatusLabel = (status) => {
  const key = `home.tenders.status.${status}`;
  const translation = t(key);
  return translation === key ? status : translation;
};

const toggleFaq = (index) => {
  faqOpenIndex.value = faqOpenIndex.value === index ? null : index;
};

const isClosingSoon = (closingDate) => {
  if (!closingDate) return false;
  const now = new Date();
  const closing = new Date(closingDate);
  const diffTime = closing.getTime() - now.getTime();
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  return diffDays <= 5 && diffDays > 0;
};

const closingSoonTenders = computed(() => {
  return tenders.value.filter(tender => isClosingSoon(tender.valid_until));
});

const getCountdown = (targetDate) => {
  const now = new Date().getTime();
  const target = new Date(targetDate).getTime();
  const difference = target - now;
  if (difference > 0) {
    return {
      days: Math.floor(difference / (1000 * 60 * 60 * 24)),
      hours: Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)),
      minutes: Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60)),
    };
  }
  return { days: 0, hours: 0, minutes: 0 };
};
</script>

<template>
  <AppLayout>

    <Head :title="t('home.title')" />
    <div class="home-page">
      <!-- Hero Section -->
      <section class="hero-section">
        <div class="hero-overlay"></div>
        <div class="container position-relative">
          <div class="row justify-content-center">
            <div class="col-lg-10 text-center text-white py-5">
              <h1 class="hero-title mb-4">
                {{ t('home.hero.title_main') }}
                <span class="d-block hero-title-accent">{{ t('home.hero.title_alt') }}</span>
              </h1>
              <p class="hero-subtitle mx-auto mb-5">{{ t('home.hero.subtitle') }}</p>
              <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                <Link :href="route('tenders.index')" class="btn btn-light btn-lg px-5 py-3 fw-semibold">
                {{ t('home.hero.view_tenders') }}
                </Link>
                <Link :href="route('register')" class="btn btn-outline-light btn-lg px-5 py-3 fw-semibold">
                {{ t('home.hero.submit_bid') }}
                </Link>
              </div>
            </div>
          </div>
        </div>
        <!-- Stats Bar -->
        <div class="hero-stats-bar">
          <div class="container">
            <div class="row text-center text-white">
              <div v-for="(stat, index) in statsItems" :key="index" class="col-6 col-md-3 py-4">
                <div class="stat-value">{{ stat.value }}</div>
                <div class="stat-label">{{ stat.label }}</div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Open Tenders Section -->
      <section class="tenders-section py-5">
        <div class="container">
          <div class="text-center mb-5">
            <h2 class="section-title">{{ t('home.tenders.title_section') }}</h2>
            <p class="section-subtitle">{{ t('home.tenders.subtitle') }}</p>
          </div>

          <!-- Search -->
          <div class="search-filter-bar mb-5">
            <div class="row justify-content-center">
              <div class="col-md-8 col-lg-6">
                <div class="search-input-wrapper">
                  <span class="search-icon">🔍</span>
                  <input v-model="searchQuery" type="text" class="form-control search-input"
                    :placeholder="t('home.tenders.filters.search_placeholder')" />
                </div>
              </div>
            </div>
          </div>

          <!-- No Results -->
          <div v-if="filteredTenders.length === 0" class="no-results text-center py-5">
            <div class="no-results-icon mb-3">📄</div>
            <h5 class="mb-2">{{ t('home.tenders.no_results_title') }}</h5>
            <p class="text-muted">{{ hasFilters ? t('home.tenders.empty_filtered') : t('home.tenders.empty_default') }}
            </p>
          </div>

          <!-- Tender Cards Grid -->
          <div v-else class="row g-4">
            <div v-for="tender in displayedTenders" :key="tender.id" class="col-md-6 col-lg-4">
              <div class="tender-card">
                <div class="tender-card-header">
                  <span class="status-badge" :class="getStatusClass(tender.status)">
                    {{ getStatusLabel(tender.status) }}
                  </span>
                </div>
                <h5 class="tender-card-title">{{ tender.title }}</h5>
                <p class="tender-card-desc">{{ getTenderDescription(tender) || t('home.tenders.table.no_description') }}
                </p>
                <div class="tender-card-footer">
                  <div class="tender-card-date">
                    <span class="date-icon">📅</span>
                    <span>{{ t('home.tenders.card.closing') }}: {{ formatDate(tender.valid_until) }}</span>
                  </div>
                  <Link :href="route('tenders.show', { tender: tender.id })" class="btn btn-primary btn-sm">
                  {{ t('home.button_details') }}
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Closing Soon Section -->
      <section v-if="closingSoonTenders.length > 0" class="closing-soon-section py-5">
        <div class="container">
          <div class="text-center mb-5">
            <h2 class="section-title">
              <span class="me-2">⏰</span>{{ t('home.closing_soon.title') }}
            </h2>
            <p class="section-subtitle">{{ t('home.closing_soon.subtitle') }}</p>
          </div>
          <div class="closing-soon-scroll">
            <div class="closing-soon-cards">
              <div v-for="tender in closingSoonTenders" :key="tender.id" class="closing-soon-card">
                <div class="tender-card">
                  <div class="tender-card-header">
                    <span class="status-badge status-urgent">{{ getStatusLabel(tender.status) }}</span>
                  </div>
                  <h5 class="tender-card-title">{{ tender.title }}</h5>
                  <p class="tender-card-desc">{{ getTenderDescription(tender) || t('home.tenders.table.no_description')
                    }}</p>
                  <div class="countdown-box">
                    <div class="countdown-label">
                      <span class="me-1">⏱️</span>{{ t('home.closing_soon.closing_in') }}:
                    </div>
                    <div class="countdown-value">
                      {{ getCountdown(tender.valid_until).days }}{{ t('home.closing_soon.days') }}
                      {{ getCountdown(tender.valid_until).hours }}{{ t('home.closing_soon.hours') }}
                      {{ getCountdown(tender.valid_until).minutes }}{{ t('home.closing_soon.minutes') }}
                    </div>
                  </div>
                  <Link :href="route('tenders.show', { tender: tender.id })" class="btn btn-primary w-100 mt-3">
                  {{ t('home.button_details') }}
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- FAQ Section -->
      <section class="faq-section py-5">
        <div class="container">
          <div class="faq-wrapper">
            <div class="text-center mb-5">
              <h2 class="section-title">{{ t('home.faq.title') }}</h2>
              <p class="section-subtitle">{{ t('home.faq.subtitle') }}</p>
            </div>
            <div class="faq-list">
              <div v-for="(item, index) in faqItems" :key="item.id" class="faq-item">
                <button class="faq-question" type="button" @click="toggleFaq(index)"
                  :aria-expanded="faqOpenIndex === index">
                  <span class="faq-question-text">{{ item.question }}</span>
                  <span class="faq-icon" :class="{ 'is-open': faqOpenIndex === index }">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M12 5v14M5 12h14" />
                    </svg>
                  </span>
                </button>
                <div v-if="faqOpenIndex === index" class="faq-answer">
                  <p>{{ item.answer }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- CTA Section -->
      <section class="cta-section py-5">
        <div class="container">
          <div class="cta-content text-center text-white py-4">
            <h2 class="cta-title mb-3">{{ t('home.cta.title') }}</h2>
            <p class="cta-subtitle mb-4">{{ t('home.cta.subtitle') }}</p>
            <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
              <Link :href="route('register')" class="btn btn-light btn-lg px-5 py-3 fw-semibold">
              {{ t('home.cta.register_button') }}
              </Link>
              <Link :href="route('tenders.index')" class="btn btn-outline-light btn-lg px-5 py-3 fw-semibold">
              {{ t('home.cta.learn_more_button') }}
              </Link>
            </div>
          </div>
        </div>
      </section>
    </div>
  </AppLayout>
</template>

<style scoped>
/* Base */
.home-page {
  background-color: #f9fafb;
}

/* Hero Section */
.hero-section {
  background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 50%, #3730a3 100%);
  position: relative;
  overflow: hidden;
}

.hero-overlay {
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.2);
}

.hero-title {
  font-size: 2.5rem;
  font-weight: 700;
  line-height: 1.2;
}

.hero-title-accent {
  color: #93c5fd;
}

.hero-subtitle {
  font-size: 1.25rem;
  max-width: 700px;
  opacity: 0.9;
}

.hero-stats-bar {
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
}

.stat-value {
  font-size: 2rem;
  font-weight: 700;
}

.stat-label {
  font-size: 0.875rem;
  color: rgba(255, 255, 255, 0.8);
}

/* Section Titles */
.section-title {
  font-size: 2rem;
  font-weight: 700;
  color: #111827;
  margin-bottom: 0.5rem;
}

.section-subtitle {
  font-size: 1.125rem;
  color: #6b7280;
  max-width: 600px;
  margin: 0 auto;
}

/* Tenders Section */
.tenders-section {
  background: #fff;
}

/* Search and Filter */
.search-input-wrapper {
  position: relative;
}

.search-input-wrapper .search-icon {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  font-size: 1rem;
}

.search-input {
  padding-left: 2.75rem;
  height: 48px;
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
}

.search-input:focus {
  border-color: #2563eb;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.filter-select {
  height: 48px;
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
}

.filter-btn {
  height: 48px;
  border: 1px solid #e5e7eb;
}

/* No Results */
.no-results-icon {
  font-size: 4rem;
  opacity: 0.3;
}

/* Tender Cards */
.tender-card {
  background: #fff;
  border-radius: 0.75rem;
  padding: 1.5rem;
  border: 1px solid #f3f4f6;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
  height: 100%;
  display: flex;
  flex-direction: column;
}

.tender-card:hover {
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  border-color: #bfdbfe;
  transform: translateY(-4px);
}

.tender-card-header {
  margin-bottom: 0.75rem;
}

.tender-card-title {
  font-size: 1.125rem;
  font-weight: 600;
  color: #111827;
  margin: 0.5rem 0;
  line-height: 1.4;
}

.tender-card-org {
  font-size: 0.875rem;
  color: #6b7280;
  margin-bottom: 0.75rem;
}

.tender-card-desc {
  font-size: 0.875rem;
  color: #6b7280;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  flex-grow: 1;
  margin-bottom: 1rem;
}

.tender-card-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: auto;
  padding-top: 1rem;
  border-top: 1px solid #f3f4f6;
}

.tender-card-date {
  font-size: 0.875rem;
  color: #6b7280;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

/* Status Badges */
.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.status-open {
  background-color: #d1fae5;
  color: #065f46;
  border: 1px solid #bbf7d0;
}

.status-closing {
  background-color: #ffedd5;
  color: #9a3412;
  border: 1px solid #fed7aa;
}

.status-urgent {
  background-color: #fee2e2;
  color: #991b1b;
  border: 1px solid #fecaca;
}

/* Closing Soon Section */
.closing-soon-section {
  background: #fef2f2;
}

.closing-soon-scroll {
  overflow-x: auto;
  padding-bottom: 1rem;
}

.closing-soon-cards {
  display: flex;
  gap: 1.5rem;
  width: max-content;
}

.closing-soon-card {
  width: 320px;
  flex-shrink: 0;
}

.countdown-box {
  background: #fef2f2;
  border: 1px solid #fecaca;
  border-radius: 0.5rem;
  padding: 0.75rem;
}

.countdown-label {
  font-size: 0.75rem;
  color: #dc2626;
  font-weight: 500;
  margin-bottom: 0.25rem;
}

.countdown-value {
  font-size: 1rem;
  font-weight: 600;
  color: #dc2626;
}

/* FAQ Section */
.faq-section {
  background: #f9fafb;
}

.faq-wrapper {
  max-width: 800px;
  margin: 0 auto;
}

.faq-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.faq-item {
  background: #fff;
  border-radius: 0.75rem;
  border: 1px solid #e5e7eb;
  overflow: hidden;
}

.faq-question {
  width: 100%;
  background: transparent;
  border: none;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.25rem 1.5rem;
  text-align: left;
  cursor: pointer;
  transition: background 0.2s;
}

.faq-question:hover {
  background: #f9fafb;
}

.faq-question-text {
  font-weight: 600;
  color: #111827;
  font-size: 1rem;
}

.faq-icon {
  color: #6b7280;
  transition: transform 0.2s ease;
  flex-shrink: 0;
}

.faq-icon.is-open {
  transform: rotate(45deg);
}

.faq-answer {
  padding: 0 1.5rem 1.25rem;
}

.faq-answer p {
  color: #6b7280;
  margin: 0;
  line-height: 1.6;
}

/* CTA Section */
.cta-section {
  background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 50%, #3730a3 100%);
}

.cta-title {
  font-size: 2rem;
  font-weight: 700;
}

.cta-subtitle {
  font-size: 1.125rem;
  color: rgba(255, 255, 255, 0.8);
  max-width: 600px;
  margin: 0 auto 1.5rem;
}

/* Responsive */
@media (min-width: 768px) {
  .hero-title {
    font-size: 3.5rem;
  }

  .hero-subtitle {
    font-size: 1.5rem;
  }
}

@media (max-width: 767.98px) {
  .hero-title {
    font-size: 2rem;
  }

  .stat-value {
    font-size: 1.5rem;
  }

  .section-title {
    font-size: 1.5rem;
  }
}
</style>

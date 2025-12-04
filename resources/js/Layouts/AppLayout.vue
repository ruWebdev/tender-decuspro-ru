<script setup>
import { computed, ref, watch, onMounted, onUpdated, nextTick } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { useTranslations } from '@/Composables/useTranslations';

const page = usePage();
const { t } = useTranslations();

const siteSettings = computed(() => page.props.site_settings || {});
const siteName = computed(() => siteSettings.value.site_name || 'QBS Tenders');

const authUser = computed(() => page.props.auth?.user || null);
const userRoles = computed(() => authUser.value?.role_names || []);
const hasRole = (role) => userRoles.value.includes(role);
const isCustomer = computed(() => hasRole('customer'));
const isSupplier = computed(() => hasRole('supplier'));
const isAdmin = computed(() => hasRole('admin'));
const canAccessAdmin = computed(() => hasRole('admin') || hasRole('moderator'));
const currentLocale = computed(() => page.props.locale || 'ru');
const locale = ref(currentLocale.value);

watch(currentLocale, (val) => {
  locale.value = val;
});

const changeLocale = () => {
  router.get(route('lang.switch', { locale: locale.value }));
};

const logout = () => {
  router.post(route('logout'));
};

const currentYear = new Date().getFullYear();

onMounted(() => {
  nextTick(() => { if (window.tabler) { try { window.tabler.init(); } catch { } } });
});

onUpdated(() => {
  nextTick(() => { if (window.tabler) { try { window.tabler.init(); } catch { } } });
});
</script>

<template>
  <div class="app-layout d-flex flex-column min-vh-100">
    <header class="site-header sticky-top">
      <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
        <div class="container">
          <Link href="/" class="navbar-brand d-flex align-items-center">
          <div class="brand-icon">
            <span class="brand-icon-text">📄</span>
          </div>
          <span class="brand-text ms-2 fw-bold">{{ siteName }}</span>
          </Link>

          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
            aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>

          <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav mx-auto mb-2 mb-md-0">
              <li class="nav-item">
                <Link href="/" class="nav-link">{{ t('nav.home') }}</Link>
              </li>

              <li class="nav-item">
                <Link :href="route('tenders.index')" class="nav-link">{{ t('nav.tenders') }}</Link>
              </li>

              <li v-if="isCustomer" class="nav-item">
                <Link :href="route('tenders.create')" class="nav-link">{{ t('nav.create_tender') }}</Link>
              </li>

              <li v-if="isSupplier" class="nav-item">
                <Link :href="route('proposals.index')" class="nav-link">{{ t('nav.my_proposals') }}</Link>
              </li>
            </ul>

            <div class="d-flex align-items-center gap-3">
              <select v-model="locale" @change="changeLocale" class="form-select form-select-sm locale-select">
                <option value="cn">中文</option>
                <option value="en">English</option>
                <option value="ru">Русский</option>
              </select>

              <div v-if="authUser" class="dropdown">
                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button"
                  data-bs-toggle="dropdown">
                  {{ authUser.name }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li>
                    <Link :href="route('dashboard')" class="dropdown-item">{{ t('nav.dashboard') }}</Link>
                  </li>
                  <li v-if="canAccessAdmin">
                    <Link :href="route('admin.dashboard')" class="dropdown-item">{{ t('nav.admin_panel') }}</Link>
                  </li>
                  <li v-if="isSupplier">
                    <Link :href="route('profile.supplier')" class="dropdown-item">{{ t('nav.supplier_profile') }}</Link>
                  </li>
                  <li>
                    <hr class="dropdown-divider">
                  </li>
                  <li>
                    <button @click="logout" class="dropdown-item">{{ t('nav.logout') }}</button>
                  </li>
                </ul>
              </div>
              <div v-else class="d-flex align-items-center gap-2">
                <Link :href="route('login')" class="btn btn-link btn-sm text-dark">{{ t('nav.login') }}</Link>
                <Link :href="route('register')" class="btn btn-primary btn-sm px-3">{{ t('nav.register') }}</Link>
              </div>
            </div>
          </div>
        </div>
      </nav>
    </header>

    <main class="flex-fill pt-4 pt-md-5">
      <slot />
    </main>

    <footer class="site-footer bg-dark text-white">
      <div class="container py-5">
        <div class="row g-4">
          <div class="col-lg-4">
            <div class="d-flex align-items-center mb-3">
              <div class="brand-icon brand-icon-footer">
                <span class="brand-icon-text">📄</span>
              </div>
              <span class="brand-text ms-2 fw-bold fs-5 text-light">{{ siteName }}</span>
            </div>
            <p class="text-white-50 mb-4">{{ t('home.footer.description') }}</p>
            <div class="d-flex gap-3" style="display:none !important;">
              <a href="#" class="footer-social-link">
                <span>🐦</span>
              </a>
              <a href="#" class="footer-social-link">
                <span>💼</span>
              </a>
            </div>
          </div>

          <div class="col-6 col-lg-4">
            <h6 class="fw-semibold mb-3">{{ t('home.footer.links_title', t('home.footer.logo_alt')) }}</h6>
            <ul class="list-unstyled footer-links">
              <li>
                <Link :href="route('docs.show', { slug: 'supplier-terms' })">
                {{ t('home.footer.links.supplier_terms.label') }}
                </Link>
              </li>
            </ul>
          </div>

          <div class="col-lg-4">
            <h6 class="fw-semibold mb-3">{{ t('home.footer.contact.title') }}</h6>
            <ul class="list-unstyled footer-contact">
              <li class="d-flex align-items-center mb-2">
                <span class="footer-contact-icon me-2">✉️</span>
                <span>{{ siteSettings.site_email || t('home.contacts.technical.value') }}</span>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <div class="border-top border-secondary">
        <div class="container py-3">
          <p class="text-white-50 text-center mb-0">© {{ currentYear }} {{ siteName }}. {{ t('home.footer.rights') }}
          </p>
        </div>
      </div>
    </footer>
  </div>
</template>

<style scoped>
.site-header {
  z-index: 1030;
}

.brand-icon {
  width: 32px;
  height: 32px;
  background: #2563eb;
  border-radius: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.brand-icon-text {
  font-size: 1rem;
  filter: grayscale(1) brightness(10);
}

.brand-icon-footer {
  background: #3b82f6;
}

.brand-text {
  font-size: 1.125rem;
  color: #111827;
}

.locale-select {
  width: auto;
  min-width: 90px;
}

.navbar .nav-link {
  color: #374151;
  font-weight: 500;
  transition: color 0.2s;
}

.navbar .nav-link:hover {
  color: #2563eb;
}

.site-footer {
  margin-top: auto;
}

.footer-links li {
  margin-bottom: 0.5rem;
}

.footer-links a {
  color: rgba(255, 255, 255, 0.6);
  text-decoration: none;
  transition: color 0.2s;
}

.footer-links a:hover {
  color: #fff;
}

.footer-contact {
  color: rgba(255, 255, 255, 0.6);
}

.footer-social-link {
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 50%;
  color: rgba(255, 255, 255, 0.6);
  text-decoration: none;
  transition: all 0.2s;
}

.footer-social-link:hover {
  background: rgba(255, 255, 255, 0.2);
  color: #fff;
}

.text-white-50 {
  color: rgba(255, 255, 255, 0.5) !important;
}
</style>

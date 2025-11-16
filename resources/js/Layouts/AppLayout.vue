<script setup>
import { computed, ref, watch } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { useTranslations } from '@/Composables/useTranslations';

const page = usePage();
const { t } = useTranslations();

const authUser = computed(() => page.props.auth?.user || null);
const isCustomer = computed(() => authUser.value?.role === 'customer');
const isSupplier = computed(() => authUser.value?.role === 'supplier');
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
</script>

<template>
  <div class="app-layout d-flex flex-column min-vh-100">
    <header>
      <nav class="navbar navbar-expand-md navbar-light bg-white border-bottom mb-4">
        <div class="container">
          <div style="display:none">
            <Link href="/" class="navbar-brand d-flex align-items-center">
            Tender
            </Link>
          </div>


          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
            aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>

          <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-md-0">
              <li class="nav-item">
                <Link href="/" class="nav-link">{{ t('nav.home') }}</Link>
              </li>

              <li v-if="isCustomer" class="nav-item">
                <Link href="/tenders" class="nav-link">{{ t('nav.my_tenders') }}</Link>
              </li>

              <li v-if="isSupplier" class="nav-item">
                <Link href="/" class="nav-link">{{ t('nav.active_tenders') }}</Link>
              </li>

              <li v-if="isSupplier" class="nav-item">
                <Link href="/proposals" class="nav-link">{{ t('nav.my_proposals') }}</Link>
              </li>
            </ul>

            <div class="d-flex align-items-center gap-2">
              <select v-model="locale" @change="changeLocale" class="form-select form-select-sm">
                <option value="ru">Русский</option>
                <option value="en">English</option>
                <option value="cn">中文</option>
              </select>

              <div v-if="authUser" class="dropdown">
                <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                  {{ authUser.name }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li>
                    <Link :href="route('dashboard')" class="dropdown-item">{{ t('nav.dashboard') }}</Link>
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
              <div v-else>
                <Link :href="route('login')" class="btn btn-primary btn-sm">{{ t('nav.login') }}</Link>
              </div>
            </div>
          </div>
        </div>
      </nav>
    </header>

    <main class="flex-fill">
      <slot />
    </main>

    <footer class="border-top py-3 mt-4">
      <div class="container d-flex justify-content-between align-items-center small text-muted">
        <span>© {{ new Date().getFullYear() }} Tender Platform</span>
        <span>{{ t('nav.current_language') }}: {{ locale }}</span>
      </div>
    </footer>
  </div>
</template>

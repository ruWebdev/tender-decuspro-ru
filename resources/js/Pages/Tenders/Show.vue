<script setup>
import { computed } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useTranslations } from '@/Composables/useTranslations';

const page = usePage();
const { t } = useTranslations();

const tender = computed(() => page.props.tender);
const authUser = computed(() => page.props.auth?.user || null);
const authUserId = computed(() => authUser.value?.id || null);
const roleNames = computed(() => authUser.value?.role_names || []);
const hasRole = (role) => roleNames.value.includes(role);
const isSupplier = computed(() => hasRole('supplier'));
const isCustomer = computed(() => hasRole('customer'));
const isGuest = computed(() => !authUser.value);

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

const localeOptions = [
  { value: 'ru', labelKey: 'auth.register_locale_option_ru' },
  { value: 'en', labelKey: 'auth.register_locale_option_en' },
  { value: 'cn', labelKey: 'auth.register_locale_option_cn' },
];

const registerForm = useForm({
  name: '',
  email: '',
  role: 'supplier',
  locale: currentLocale.value,
  password: '',
  password_confirmation: '',
  terms: false,
});

const submitSupplierRegistration = () => {
  registerForm.post(route('register'), {
    onFinish: () => registerForm.reset('password', 'password_confirmation'),
  });
};

const tenderDescription = computed(() => {
  if (!tender.value) {
    return '';
  }

  if (currentLocale.value === 'en') {
    return tender.value.description_en || tender.value.description || '';
  }

  if (currentLocale.value === 'cn') {
    return tender.value.description_cn || tender.value.description || '';
  }

  return tender.value.description || '';
});

const itemTitle = (item) => {
  if (currentLocale.value === 'en') {
    return item.name_en || item.title;
  }

  if (currentLocale.value === 'cn') {
    return item.name_cn || item.title;
  }

  return item.title;
};

const formatDate = (value) => {
  if (!value) {
    return '-';
  }
  return new Date(value).toLocaleString(jsLocale.value);
};

const formatTime = (tender) => {
  const time = tender?.valid_until_time;
  if (time && /^\d{2}:\d{2}$/.test(time)) return time;
  const d = tender?.valid_until ? new Date(tender.valid_until) : null;
  if (!d) return '';
  return d.toLocaleTimeString(jsLocale.value, { hour: '2-digit', minute: '2-digit' });
};

const formatDateOnly = (value) => {
  if (!value) return '-';
  return new Date(value).toLocaleDateString(jsLocale.value, { day: '2-digit', month: '2-digit', year: 'numeric' });
};

// Q&A
const questions = computed(() => page.props.questions || []);
const questionForm = useForm({
  question: '',
});
const submitQuestion = () => {
  if (!tender.value) return;
  questionForm.post(route('tenders.questions.store', { tender: tender.value.id }), {
    preserveScroll: true,
    onSuccess: () => {
      questionForm.reset('question');
    },
  });
};
const myProposal = computed(() => page.props.my_proposal || null);

const chat = computed(() => page.props.chat || null);

const chatForm = useForm({
  body: '',
});

const submitChatMessage = () => {
  if (!tender.value) return;
  if (!isSupplier.value) return;
  if (!chatForm.body || !chatForm.body.trim()) return;

  chatForm.post(route('tenders.chat.messages.store', { tender: tender.value.id }), {
    preserveScroll: true,
    onSuccess: () => {
      chatForm.reset('body');
    },
  });
};

</script>

<template>
  <AppLayout>

    <Head :title="t('tenders.show_title')" />
    <div class="container mb-4">
      <div class="row g-4">
        <div class="col-12 col-lg-9">
          <h1 class="h2">{{ t('tenders.show_title') }}</h1>
        </div>
        <div class="col-12 col-lg-9">

          <div v-if="tender" class="card mb-4">
            <div class="card-body">
              <h2 class="h4 mb-3">{{ tender.title }}</h2>



              <div class="row mb-3">
                <div class="col-md-6">
                  <p class="mb-1">
                    <strong>{{ t('tenders.field_status') }}</strong>
                    <span v-if="tender.is_finished" class="badge bg-success text-light">
                      {{ t('tenders.status_finished') }}
                    </span>
                    <span v-else class="badge bg-primary text-light ms-1">
                      {{ t('tenders.status_open') }}
                    </span>
                  </p>
                  <p class="mb-1">
                    <strong>{{ t('tenders.field_created_at') }}</strong> {{ formatDate(tender.created_at) }}
                  </p>
                  <p class="mb-1">
                    <strong>{{ t('tenders.field_valid_until') }}</strong>
                    {{ formatDateOnly(tender.valid_until) }}
                    <span v-if="formatTime(tender)" class="text-muted">{{ ' ' + formatTime(tender) }}</span>
                  </p>
                </div>

                <div class="col-md-6">
                  <p v-if="tender.is_finished" class="text-success">
                    <strong>{{ t('tenders.status_finished_label') }}</strong>
                  </p>
                  <p v-else class="text-info">
                    <strong>{{ t('tenders.status_open_label') }}</strong>
                  </p>
                </div>
              </div>

              <div class="mb-3">
                <strong>{{ t('tenders.field_description') }}</strong>
                <p>{{ tenderDescription || t('tenders.no_description') }}</p>
              </div>
            </div>
          </div>

          <div class="card mb-4">
            <div class="card-header">
              <h3 class="h5 mb-0">{{ t('tenders.positions_title') }}</h3>
            </div>
            <div class="table-responsive">
              <div class="table-wrapper" :class="{ 'table-blur': isGuest }">
                <table class="table card-table table-vcenter">
                  <thead>
                    <tr>
                      <th>{{ t('tenders.col_item_title') }}</th>
                      <th>{{ t('tenders.col_quantity') }}</th>
                      <th>{{ t('tenders.col_unit') }}</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="item in tender.items" :key="item.id">
                      <td>{{ itemTitle(item) }}</td>
                      <td>{{ item.quantity }}</td>
                      <td>{{ item.unit || '-' }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div v-if="isGuest" class="table-overlay">
                <Link :href="route('register')" class="btn btn-warning btn-lg text-uppercase fw-bold">
                {{ t('auth.register_free_cta') }}
                </Link>
              </div>
            </div>
          </div>

          <!-- Q&A: Questions to customer -->
          <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h3 class="h5 mb-0">{{ t('tenders.qa_title') }}</h3>
            </div>
            <div class="card-body">
              <div v-if="questions.length === 0" class="text-muted mb-3">{{ t('tenders.qa_empty') }}</div>

              <div v-for="(q, idx) in questions" :key="q.id" class="mb-3 pb-3 border-bottom">
                <div class="fw-semibold mb-1">
                  {{ t('tenders.qa_participant_prefix', 'Участник') }}{{ idx + 1 }}:
                  {{ q.question }}
                </div>
                <div v-if="q.answer" class="ms-3 text-muted">
                  <span class="fw-semibold">{{ t('tenders.qa_customer_answer') }}:</span>
                  {{ q.answer }}
                </div>
              </div>

              <!-- Ask form for suppliers when tender is open -->
              <div v-if="isSupplier && !tender.is_finished" class="mt-3">
                <label class="form-label">{{ t('tenders.qa_ask_label') }}</label>
                <textarea v-model="questionForm.question" class="form-control" rows="3"
                  :placeholder="t('tenders.qa_ask_placeholder')"
                  :class="{ 'is-invalid': questionForm.errors.question }"></textarea>
                <div v-if="questionForm.errors.question" class="invalid-feedback">{{ questionForm.errors.question
                  }}</div>
                <div class="mt-2 d-flex justify-content-end">
                  <button class="btn btn-primary" :disabled="questionForm.processing || !questionForm.question.trim()"
                    @click="submitQuestion">
                    <span v-if="questionForm.processing" class="spinner-border spinner-border-sm me-2"></span>
                    {{ t('tenders.qa_ask_submit') }}
                  </button>
                </div>
                <div class="form-text mt-2">
                  {{ t('tenders.qa_moderation_note') }}
                </div>
              </div>
            </div>
          </div>

          <div class="mb-3 d-flex gap-2 flex-wrap">
            <Link :href="route('tenders.index')" class="btn btn-secondary">
            {{ t('tenders.back_to_list') }}
            </Link>

            <Link v-if="isSupplier && !tender.is_finished && !myProposal"
              :href="route('proposals.participate', { tender: tender.id })" class="btn btn-success">
            {{ t('tenders.action_participate') }}
            </Link>
            <Link v-if="isSupplier && !tender.is_finished && myProposal && myProposal.status === 'draft'"
              :href="route('proposals.participate', { tender: tender.id })" class="btn btn-outline-secondary">
            {{ t('proposals.action_edit', 'Редактировать') }}
            </Link>
            <form v-if="isSupplier && !tender.is_finished && myProposal && myProposal.status === 'submitted'"
              :action="route('proposals.withdraw', { proposal: myProposal.id })" method="post" class="d-inline">
              <input type="hidden" name="_method" value="POST" />
              <button type="submit" class="btn btn-outline-danger">{{ t('proposals.action_withdraw', 'Отозвать')
                }}</button>
            </form>

            <Link v-if="isCustomer" :href="route('tenders.comparison', { tender: tender.id })"
              class="btn btn-outline-primary">
            {{ t('tenders.action_compare_tender') }}
            </Link>

            <Link v-if="isCustomer" :href="route('proposals.index.customer', { tender: tender.id })"
              class="btn btn-outline-secondary">
            {{ t('tenders.action_view_proposals') }}
            </Link>
          </div>
        </div>

        <div class="col-12 col-lg-3">
          <div v-if="isSupplier && tender && !tender.is_finished" class="card mb-4">
            <div class="card-header">
              <h3 class="h5 mb-0">{{ t('tenders.chat.title') }}</h3>
            </div>
            <div class="card-body">
              <div v-if="!chat || !chat.messages || chat.messages.length === 0" class="text-muted small mb-3">
                {{ t('tenders.chat.empty') }}
              </div>

              <div v-else class="mb-3">
                <div v-for="message in chat.messages" :key="message.id" class="mb-2">
                  <div class="small fw-semibold mb-1">
                    <span v-if="message.sender_id === authUserId">
                      {{ t('tenders.chat.me_label') }}
                    </span>
                    <span v-else>
                      {{ t('tenders.chat.customer_label') }}
                    </span>
                  </div>
                  <div>
                    <span v-if="message.sender_id === authUserId">
                      {{ message.body }}
                    </span>
                    <span v-else>
                      {{ message.translated_body_supplier || message.body }}
                    </span>
                  </div>
                </div>
              </div>

              <div class="mt-2">
                <label class="form-label small">{{ t('tenders.chat.input_label') }}</label>
                <textarea v-model="chatForm.body" class="form-control mb-2" rows="3"
                  :placeholder="t('tenders.chat.input_placeholder')" :disabled="chatForm.processing"></textarea>
                <div class="d-flex justify-content-end">
                  <button type="button" class="btn btn-primary btn-sm"
                    :disabled="chatForm.processing || !chatForm.body || !chatForm.body.trim()"
                    @click="submitChatMessage">
                    <span v-if="chatForm.processing" class="spinner-border spinner-border-sm me-2"></span>
                    {{ t('tenders.chat.send') }}
                  </button>
                </div>
              </div>
            </div>
          </div>

          <div v-if="isGuest" class="card mb-4">
            <div class="card-body">
              <h3 class="h5 mb-3">
                {{ t('auth.register_heading') }} — {{ t('auth.register_role_supplier') }}
              </h3>
              <form @submit.prevent="submitSupplierRegistration" autocomplete="off" novalidate>
                <div class="mb-3">
                  <label class="form-label">{{ t('auth.register_name_label') }}</label>
                  <input type="text" class="form-control" :placeholder="t('auth.register_name_placeholder')"
                    v-model="registerForm.name" :class="{ 'is-invalid': registerForm.errors.name }" required />
                  <div class="invalid-feedback" v-if="registerForm.errors.name">
                    {{ registerForm.errors.name }}
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label">{{ t('auth.register_email_label') }}</label>
                  <input type="email" class="form-control" :placeholder="t('auth.register_email_placeholder')"
                    v-model="registerForm.email" :class="{ 'is-invalid': registerForm.errors.email }" required />
                  <div class="invalid-feedback" v-if="registerForm.errors.email">
                    {{ registerForm.errors.email }}
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label">{{ t('auth.register_locale_label') }}</label>
                  <select class="form-control" v-model="registerForm.locale"
                    :class="{ 'is-invalid': registerForm.errors.locale }">
                    <option v-for="option in localeOptions" :key="option.value" :value="option.value">
                      {{ t(option.labelKey) }}
                    </option>
                  </select>
                  <div class="invalid-feedback" v-if="registerForm.errors.locale">
                    {{ registerForm.errors.locale }}
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label">{{ t('auth.register_password_label') }}</label>
                  <input type="password" class="form-control" :placeholder="t('auth.register_password_placeholder')"
                    v-model="registerForm.password" :class="{ 'is-invalid': registerForm.errors.password }" required />
                  <div class="invalid-feedback" v-if="registerForm.errors.password">
                    {{ registerForm.errors.password }}
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label">{{ t('auth.register_password_confirm_label') }}</label>
                  <input type="password" class="form-control"
                    :placeholder="t('auth.register_password_confirm_placeholder')"
                    v-model="registerForm.password_confirmation"
                    :class="{ 'is-invalid': registerForm.errors.password_confirmation }" required />
                  <div class="invalid-feedback" v-if="registerForm.errors.password_confirmation">
                    {{ registerForm.errors.password_confirmation }}
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-check">
                    <input type="checkbox" class="form-check-input" v-model="registerForm.terms"
                      :class="{ 'is-invalid': registerForm.errors.terms }" required />
                    <span class="form-check-label">
                      {{ t('auth.register_terms_label') }}
                      <a href="#" tabindex="-1">{{ t('auth.register_terms_link') }}</a>
                    </span>
                    <div class="invalid-feedback" v-if="registerForm.errors.terms">
                      {{ registerForm.errors.terms }}
                    </div>
                  </label>
                </div>

                <div class="form-footer">
                  <button type="submit" class="btn btn-primary w-100" :disabled="registerForm.processing">
                    <span v-if="registerForm.processing" class="spinner-border spinner-border-sm me-2"
                      role="status"></span>
                    {{ t('auth.register_button') }}
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
.table-wrapper {
  position: relative;
}

.table-wrapper.table-blur table {
  filter: blur(4px);
  pointer-events: none;
}

.table-overlay {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(255, 255, 255, 0.5);
  backdrop-filter: blur(0px);
  text-align: center;
  padding: 2rem;
}
</style>

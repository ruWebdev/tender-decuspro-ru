<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useTranslations } from '@/Composables/useTranslations';

const { t } = useTranslations();
const page = usePage();
const currentLocale = page.props.locale || 'ru';

const localeOptions = [
    { value: 'ru', labelKey: 'auth.register_locale_option_ru' },
    { value: 'en', labelKey: 'auth.register_locale_option_en' },
    { value: 'cn', labelKey: 'auth.register_locale_option_cn' },
];

const form = useForm({
    name: '',
    email: '',
    role: 'supplier',
    locale: currentLocale,
    password: '',
    password_confirmation: '',
    terms: false,
});

const showPassword = ref(false);
const showPasswordConfirm = ref(false);

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>

        <Head :title="t('auth.register_title')" />

        <div class="card card-md">
            <div class="card-body">
                <h2 class="h2 text-center mb-4">{{ t('auth.register_heading') }}</h2>

                <form @submit.prevent="submit" autocomplete="off" novalidate>
                    <div class="mb-3">
                        <label class="form-label">{{ t('auth.register_name_label') }}</label>
                        <input type="text" class="form-control" :placeholder="t('auth.register_name_placeholder')"
                            autocomplete="off" v-model="form.name" :class="{ 'is-invalid': form.errors.name }" required
                            autofocus>
                        <div class="invalid-feedback" v-if="form.errors.name">
                            {{ form.errors.name }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ t('auth.register_email_label') }}</label>
                        <input type="email" class="form-control" :placeholder="t('auth.register_email_placeholder')"
                            autocomplete="off" v-model="form.email" :class="{ 'is-invalid': form.errors.email }"
                            required>
                        <div class="invalid-feedback" v-if="form.errors.email">
                            {{ form.errors.email }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ t('auth.register_locale_label') }}</label>
                        <select class="form-control" v-model="form.locale"
                            :class="{ 'is-invalid': form.errors.locale }">
                            <option v-for="option in localeOptions" :key="option.value" :value="option.value">
                                {{ t(option.labelKey) }}
                            </option>
                        </select>
                        <div class="invalid-feedback" v-if="form.errors.locale">
                            {{ form.errors.locale }}
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">{{ t('auth.register_password_label') }}</label>
                        <div class="input-group input-group-flat">
                            <input :type="showPassword ? 'text' : 'password'" class="form-control"
                                :placeholder="t('auth.register_password_placeholder')" autocomplete="off"
                                v-model="form.password" :class="{ 'is-invalid': form.errors.password }" required>
                            <span class="input-group-text">
                                <a href="#" class="link-secondary" data-bs-toggle="tooltip" @click.prevent="showPassword = !showPassword">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                        <path
                                            d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                    </svg>
                                </a>
                            </span>
                            <div class="invalid-feedback" v-if="form.errors.password">
                                {{ form.errors.password }}
                            </div>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">{{ t('auth.register_password_confirm_label') }}</label>
                        <div class="input-group input-group-flat">
                            <input :type="showPasswordConfirm ? 'text' : 'password'" class="form-control"
                                :placeholder="t('auth.register_password_confirm_placeholder')" autocomplete="off"
                                v-model="form.password_confirmation"
                                :class="{ 'is-invalid': form.errors.password_confirmation }" required>
                            <span class="input-group-text">
                                <a href="#" class="link-secondary" title="Показать пароль" data-bs-toggle="tooltip" @click.prevent="showPasswordConfirm = !showPasswordConfirm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                        <path
                                            d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                    </svg>
                                </a>
                            </span>
                            <div class="invalid-feedback" v-if="form.errors.password_confirmation">
                                {{ form.errors.password_confirmation }}
                            </div>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-check">
                            <input type="checkbox" class="form-check-input" v-model="form.terms"
                                :class="{ 'is-invalid': form.errors.terms }" required />
                            <span class="form-check-label">
                                {{ t('auth.register_terms_label') }} <a href="#" tabindex="-1">{{
                                    t('auth.register_terms_link') }}</a>
                            </span>
                            <div class="invalid-feedback" v-if="form.errors.terms">
                                {{ form.errors.terms }}
                            </div>
                        </label>
                    </div>

                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary w-100" :disabled="form.processing">
                            <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"
                                role="status"></span>
                            {{ t('auth.register_button') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="text-center text-muted mt-3">
            {{ t('auth.register_already_have') }}
            <Link :href="route('login')" tabindex="-1">{{ t('auth.register_login_link') }}</Link>
        </div>
    </GuestLayout>
</template>

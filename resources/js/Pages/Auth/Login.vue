<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>

        <Head title="Вход" />

        <div class="card card-md">
            <div class="card-body">
                <h2 class="h2 text-center mb-4">Вход в аккаунт</h2>
                <div v-if="status" class="alert alert-success" role="alert">{{ status }}</div>

                <form @submit.prevent="submit" autocomplete="off" novalidate>
                    <div class="mb-3">
                        <label class="form-label">Электронная почта</label>
                        <input type="email" class="form-control" placeholder="email@example.com" autocomplete="off"
                            v-model="form.email" :class="{ 'is-invalid': form.errors.email }" required autofocus>
                        <div class="invalid-feedback" v-if="form.errors.email">
                            {{ form.errors.email }}
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">
                            Пароль
                        </label>
                        <div class="input-group input-group-flat">
                            <input type="password" class="form-control" placeholder="Ваш пароль" autocomplete="off"
                                v-model="form.password" :class="{ 'is-invalid': form.errors.password }" required>
                            <span class="input-group-text">
                                <a href="#" class="link-secondary" title="Показать пароль" data-bs-toggle="tooltip">
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

                    <div class="mb-2 mt-4">
                        <label class="form-check">
                            <input type="checkbox" class="form-check-input" v-model="form.remember" />
                            <span class="form-check-label">Запомнить меня на этом устройстве</span>
                        </label>
                    </div>

                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary w-100" :disabled="form.processing">
                            <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"
                                role="status"></span>
                            Войти
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="text-center text-muted mt-3">
            Еще нет аккаунта?
            <Link :href="route('register')" tabindex="-1">Зарегистрироваться</Link>
        </div>
    </GuestLayout>
</template>

<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    role: 'customer',
    locale: '',
    password: '',
    password_confirmation: '',
    terms: false,
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Регистрация" />
        <div class="text-center mb-4">
            <a href="/" class="navbar-brand navbar-brand-autodark">
                <img src="/tabler/static/logo.svg" width="110" height="32" alt="Tabler" class="navbar-brand-image">
            </a>
        </div>

        <div class="card card-md">
            <div class="card-body">
                <h2 class="h2 text-center mb-4">Создание аккаунта</h2>
                    
                    <form @submit.prevent="submit" autocomplete="off" novalidate>
                        <div class="mb-3">
                            <label class="form-label">Имя</label>
                            <input 
                                type="text" 
                                class="form-control" 
                                placeholder="Ваше имя"
                                autocomplete="off"
                                v-model="form.name"
                                :class="{ 'is-invalid': form.errors.name }"
                                required
                                autofocus
                            >
                            <div class="invalid-feedback" v-if="form.errors.name">
                                {{ form.errors.name }}
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Электронная почта</label>
                            <input 
                                type="email" 
                                class="form-control" 
                                placeholder="email@example.com" 
                                autocomplete="off"
                                v-model="form.email"
                                :class="{ 'is-invalid': form.errors.email }"
                                required
                            >
                            <div class="invalid-feedback" v-if="form.errors.email">
                                {{ form.errors.email }}
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Роль</label>
                            <select
                                class="form-control"
                                v-model="form.role"
                                :class="{ 'is-invalid': form.errors.role }"
                                required
                            >
                                <option value="customer">Заказчик</option>
                                <option value="supplier">Поставщик</option>
                            </select>
                            <div class="invalid-feedback" v-if="form.errors.role">
                                {{ form.errors.role }}
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Язык интерфейса</label>
                            <input
                                type="text"
                                class="form-control"
                                placeholder="ru"
                                autocomplete="off"
                                v-model="form.locale"
                                :class="{ 'is-invalid': form.errors.locale }"
                            >
                            <div class="invalid-feedback" v-if="form.errors.locale">
                                {{ form.errors.locale }}
                            </div>
                        </div>
                        
                        <div class="mb-2">
                            <label class="form-label">Пароль</label>
                            <div class="input-group input-group-flat">
                                <input 
                                    type="password" 
                                    class="form-control" 
                                    placeholder="Ваш пароль" 
                                    autocomplete="off"
                                    v-model="form.password"
                                    :class="{ 'is-invalid': form.errors.password }"
                                    required
                                >
                                <span class="input-group-text">
                                    <a href="#" class="link-secondary" title="Показать пароль" data-bs-toggle="tooltip">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                            <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                        </svg>
                                    </a>
                                </span>
                                <div class="invalid-feedback" v-if="form.errors.password">
                                    {{ form.errors.password }}
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-2">
                            <label class="form-label">Подтверждение пароля</label>
                            <div class="input-group input-group-flat">
                                <input 
                                    type="password" 
                                    class="form-control" 
                                    placeholder="Подтвердите пароль" 
                                    autocomplete="off"
                                    v-model="form.password_confirmation"
                                    :class="{ 'is-invalid': form.errors.password_confirmation }"
                                    required
                                >
                                <span class="input-group-text">
                                    <a href="#" class="link-secondary" title="Показать пароль" data-bs-toggle="tooltip">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                            <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
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
                                <input 
                                    type="checkbox" 
                                    class="form-check-input"
                                    v-model="form.terms"
                                    :class="{ 'is-invalid': form.errors.terms }"
                                    required
                                />
                                <span class="form-check-label">
                                    Я соглашаюсь с <a href="#" tabindex="-1">условиями и политикой</a>
                                </span>
                                <div class="invalid-feedback" v-if="form.errors.terms">
                                    {{ form.errors.terms }}
                                </div>
                            </label>
                        </div>
                        
                        <div class="form-footer">
                            <button 
                                type="submit" 
                                class="btn btn-primary w-100" 
                                :disabled="form.processing"
                            >
                                <span v-if="form.processing" class="spinner-border spinner-border-sm me-2" role="status"></span>
                                Создать аккаунт
                            </button>
                        </div>
                    </form>
            </div>
        </div>

        <div class="text-center text-muted mt-3">
            Уже есть аккаунт? 
            <Link :href="route('login')" tabindex="-1">Войти</Link>
        </div>
    </GuestLayout>
</template>

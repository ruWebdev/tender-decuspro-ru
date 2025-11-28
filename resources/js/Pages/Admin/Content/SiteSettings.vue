<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const settings = computed(() => page.props.settings || {});

const form = useForm({
    site_name: settings.value.site_name || '',
    site_phone: settings.value.site_phone || '',
    site_email: settings.value.site_email || '',
    site_address: settings.value.site_address || '',
    stats_tenders: settings.value.stats_tenders || '',
    stats_vendors: settings.value.stats_vendors || '',
    stats_total_value: settings.value.stats_total_value || '',
    stats_success_rate: settings.value.stats_success_rate || '',
});

const save = () => {
    form.post(route('admin.content.site_settings.save'));
};
</script>

<template>
    <AdminLayout>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Настройки сайта</h1>
            <button class="btn btn-success" :disabled="form.processing" @click="save">
                <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
                Сохранить
            </button>
        </div>

        <div class="row g-4">
            <!-- Основные настройки -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Основные настройки</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Название сайта</label>
                            <input v-model="form.site_name" type="text" class="form-control"
                                placeholder="QBS Tenders" />
                            <div class="form-text">Отображается в шапке и подвале сайта</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Телефон</label>
                            <input v-model="form.site_phone" type="text" class="form-control"
                                placeholder="+7 (000) 000-00-00" />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input v-model="form.site_email" type="email" class="form-control"
                                placeholder="info@example.com" />
                        </div>

                        <div class="mb-0">
                            <label class="form-label">Адрес</label>
                            <input v-model="form.site_address" type="text" class="form-control"
                                placeholder="ул. Примерная, 123" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Статистика на главной -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Статистика на главной странице</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Активных тендеров</label>
                            <input v-model="form.stats_tenders" type="text" class="form-control" placeholder="500+" />
                            <div class="form-text">Например: 500+, 1000+</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Зарегистрированных поставщиков</label>
                            <input v-model="form.stats_vendors" type="text" class="form-control" placeholder="1200+" />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Общая стоимость</label>
                            <input v-model="form.stats_total_value" type="text" class="form-control"
                                placeholder="$50M+" />
                        </div>

                        <div class="mb-0">
                            <label class="form-label">Успешных сделок</label>
                            <input v-model="form.stats_success_rate" type="text" class="form-control"
                                placeholder="98%" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="alert alert-info mt-4">
            <strong>Примечание:</strong> После сохранения настроек изменения отобразятся на сайте в течение нескольких
            минут.
        </div>
    </AdminLayout>
</template>

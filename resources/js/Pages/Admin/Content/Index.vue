<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { useTranslations } from '@/Composables/useTranslations';

const page = usePage();
const { t } = useTranslations();

const items = computed(() => page.props.items || []);

const form = useForm({
    items: items.value.map((i) => ({
        key: i.key,
        value_ru: i.value_ru || '',
        value_en: i.value_en || '',
        value_cn: i.value_cn || '',
    })),
});

const activeTab = ref('ru');
const tabs = [
    { value: 'ru', label: 'Русский' },
    { value: 'en', label: 'English' },
    { value: 'cn', label: '中文' },
];

const addRow = () => {
    form.items.push({ key: 'home.', value_ru: '', value_en: '', value_cn: '' });
};

const save = () => {
    form.post(route('admin.content.home.save'));
};
</script>

<template>
    <AdminLayout>
        <div class="container py-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="h3 mb-0">{{ t('admin.content.home_editor.title') }}</h1>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-secondary" type="button" @click="addRow">
                        {{ t('admin.content.home_editor.actions.add_row') }}
                    </button>
                    <div class="btn-group" role="group">
                        <button v-for="tab in tabs" :key="tab.value" type="button" class="btn"
                            :class="activeTab === tab.value ? 'btn-primary' : 'btn-outline-primary'"
                            @click="activeTab = tab.value">
                            {{ tab.label }}
                        </button>
                    </div>
                    <button class="btn btn-success" :disabled="form.processing" @click="save">
                        <span v-if="form.processing" class="spinner-border spinner-border-sm me-2" role="status"></span>
                        {{ t('admin.content.home_editor.actions.save') }}
                    </button>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th style="width: 30%">{{ t('admin.content.home_editor.col_key') }}</th>
                                    <th>{{ t('admin.content.home_editor.col_value') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(row, idx) in form.items" :key="row.key">
                                    <td>
                                        <input v-model="row.key" class="form-control form-control-sm"
                                            placeholder="home.key.path" />
                                    </td>
                                    <td>
                                        <template v-if="activeTab === 'ru'">
                                            <textarea v-model="row.value_ru" rows="2" class="form-control"></textarea>
                                        </template>
                                        <template v-else-if="activeTab === 'en'">
                                            <textarea v-model="row.value_en" rows="2" class="form-control"></textarea>
                                        </template>
                                        <template v-else>
                                            <textarea v-model="row.value_cn" rows="2" class="form-control"></textarea>
                                        </template>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="text-end">
                        <button class="btn btn-primary" :disabled="form.processing" @click="save">
                            <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"
                                role="status"></span>
                            {{ t('admin.content.home_editor.actions.save') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.btn-group .btn {
    min-width: 110px;
}

textarea.form-control {
    min-height: 42px;
}
</style>

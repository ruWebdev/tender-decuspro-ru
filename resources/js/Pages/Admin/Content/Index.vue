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

const removeRow = (idx) => {
    form.items.splice(idx, 1);
};

const search = ref('');

const groups = computed(() => {
    const list = form.items.map((row, index) => ({ index, row }));
    const filtered = search.value.trim().toLowerCase();
    const byGroup = new Map();

    for (const it of list) {
        const k = it.row.key || '';
        if (filtered) {
            const hay = [k, it.row.value_ru, it.row.value_en, it.row.value_cn].join(' ').toLowerCase();
            if (!hay.includes(filtered)) continue;
        }
        const parts = k.split('.');
        let grp = 'home.other';
        if (parts.length >= 2) {
            grp = `${parts[0]}.${parts[1]}`;
        }
        if (!byGroup.has(grp)) byGroup.set(grp, []);
        byGroup.get(grp).push(it);
    }

    return Array.from(byGroup.entries()).map(([group, items]) => ({ group, items }));
});

const addInGroup = (group) => {
    const prefix = group.endsWith('.') ? group : `${group}.`;
    form.items.push({ key: `${prefix}new_key`, value_ru: '', value_en: '', value_cn: '' });
};

const save = () => {
    form.post(route('admin.content.home.save'));
};
</script>

<template>
    <AdminLayout>
        <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0">{{ t('admin.content.home_editor.title') }}</h1>
            <div class="d-flex gap-2 align-items-center">
                <div class="input-group">
                    <input v-model="search" type="text" class="form-control" :placeholder="t('common.search', 'Поиск')">
                </div>
                <div class="btn-group" role="group">
                    <button v-for="tab in tabs" :key="tab.value" type="button" class="btn"
                        :class="activeTab === tab.value ? 'btn-primary' : 'btn-outline-primary'"
                        @click="activeTab = tab.value">
                        {{ tab.label }}
                    </button>
                </div>
                <button class="btn btn-success" :disabled="form.processing" @click="save"
                    style="width: 260px !important;">
                    <span v-if="form.processing" class="spinner-border spinner-border-sm me-2" role="status"></span>
                    {{ t('admin.content.home_editor.actions.save') }}
                </button>
            </div>
        </div>

        <div class="accordion" id="homeContentAccordion">
            <div class="accordion-item" v-for="section in groups" :key="section.group">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        :data-bs-target="`#grp-${section.group}`">
                        {{ section.group }}
                    </button>
                </h2>
                <div :id="`grp-${section.group}`" class="accordion-collapse collapse"
                    data-bs-parent="#homeContentAccordion">
                    <div class="accordion-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <button class="btn btn-outline-secondary btn-sm" type="button"
                                @click="addInGroup(section.group)">
                                {{ t('admin.content.home_editor.actions.add_row') }}
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th style="width: 34%">{{ t('admin.content.home_editor.col_key') }}</th>
                                        <th>{{ t('admin.content.home_editor.col_value') }}</th>
                                        <th style="width: 1%"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="it in section.items" :key="it.index">
                                        <td>
                                            <input v-model="form.items[it.index].key"
                                                class="form-control form-control-sm" placeholder="home.key.path" />
                                        </td>
                                        <td>
                                            <template v-if="activeTab === 'ru'">
                                                <textarea v-model="form.items[it.index].value_ru" rows="2"
                                                    class="form-control"></textarea>
                                            </template>
                                            <template v-else-if="activeTab === 'en'">
                                                <textarea v-model="form.items[it.index].value_en" rows="2"
                                                    class="form-control"></textarea>
                                            </template>
                                            <template v-else>
                                                <textarea v-model="form.items[it.index].value_cn" rows="2"
                                                    class="form-control"></textarea>
                                            </template>
                                        </td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-outline-danger btn-icon"
                                                @click="removeRow(it.index)">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M4 7l16 0" />
                                                    <path d="M10 11l0 6" />
                                                    <path d="M14 11l0 6" />
                                                    <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                    <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
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

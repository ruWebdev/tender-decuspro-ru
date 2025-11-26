<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useTranslations } from '@/Composables/useTranslations';

const page = usePage();
const { t } = useTranslations();

const props = defineProps({
    templates: {
        type: Array,
        default: () => [],
    },
});

const hasTemplates = computed(() => props.templates && props.templates.length > 0);

const typeLabel = (type) => {
    const key = `admin.notification_templates.types.${type}`;
    const translation = t(key);
    return translation === key ? type : translation;
};
</script>

<template>
    <AdminLayout>
        <div class="admin-notification-templates">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">{{ t('admin.notification_templates.index_title') }}</h1>
                <Link :href="route('admin.notification_templates.create')" class="btn btn-primary">
                {{ t('admin.notification_templates.actions.create') }}
                </Link>
            </div>

            <div class="card">
                <div class="table-responsive">
                    <table class="table table-vcenter mb-0">
                        <thead>
                            <tr>
                                <th>{{ t('admin.notification_templates.table.col_name') }}</th>
                                <th>{{ t('admin.notification_templates.table.col_type') }}</th>
                                <th class="w-150 text-end">{{ t('admin.notification_templates.table.col_actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="!hasTemplates">
                                <td colspan="3" class="text-center text-muted py-4">
                                    {{ t('admin.notification_templates.index_empty') }}
                                </td>
                            </tr>
                            <tr v-for="template in templates" :key="template.id">
                                <td>{{ template.name }}</td>
                                <td>{{ typeLabel(template.type) }}</td>
                                <td class="text-end">
                                    <div class="btn-list flex-nowrap justify-content-end">
                                        <Link :href="route('admin.notification_templates.edit', template.id)"
                                            class="btn btn-sm btn-ghost-primary">
                                        {{ t('common.edit', 'Редактировать') }}
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.w-150 {
    width: 150px;
}
</style>

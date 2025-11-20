<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { useTranslations } from '@/Composables/useTranslations';

const page = usePage();
const { t } = useTranslations();

const props = defineProps({
    backups: Array,
});

const formatDate = (value) => {
    if (!value) {
        return '-';
    }

    const date = new Date(value);
    return date.toLocaleString(page.props.locale || 'ru');
};

const runBackup = () => {
    if (!confirm(t('admin.backup.confirm_run', 'Создать новую резервную копию сейчас?'))) {
        return;
    }

    router.post(route('admin.backup.run'));
};

const deleteBackup = (name) => {
    if (!confirm(t('admin.backup.confirm_delete', 'Удалить выбранную резервную копию?'))) {
        return;
    }

    router.delete(route('admin.backup.destroy', name));
};
</script>

<template>
    <AdminLayout>
        <div class="admin-backup">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">{{ t('admin.backup.title') }}</h1>
                <button type="button" class="btn btn-primary" @click="runBackup">
                    {{ t('admin.backup.actions.run', 'Создать бэкап') }}
                </button>
            </div>

            <div v-if="$page.props.errors?.backup" class="alert alert-danger">
                {{ $page.props.errors.backup }}
            </div>
            <div v-if="$page.props.flash?.success" class="alert alert-success">
                {{ $page.props.flash.success }}
            </div>

            <div class="card">
                <div class="card-body">
                    <div v-if="!backups || backups.length === 0" class="text-center text-muted py-4">
                        {{ t('admin.backup.empty', 'Резервные копии не найдены') }}
                    </div>
                    <div v-else class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>{{ t('admin.backup.table.name', 'Файл') }}</th>
                                    <th>{{ t('admin.backup.table.size', 'Размер') }}</th>
                                    <th>{{ t('admin.backup.table.created_at', 'Создан') }}</th>
                                    <th class="text-end">{{ t('admin.backup.table.actions', 'Действия') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="backup in backups" :key="backup.name">
                                    <td>{{ backup.name }}</td>
                                    <td>{{ backup.size_human }}</td>
                                    <td>{{ formatDate(backup.created_at) }}</td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <Link :href="route('admin.backup.download', backup.name)"
                                                class="btn btn-outline-primary">
                                            {{ t('admin.backup.actions.download', 'Скачать') }}
                                            </Link>
                                            <button type="button" class="btn btn-outline-danger"
                                                @click="deleteBackup(backup.name)">
                                                {{ t('admin.backup.actions.delete', 'Удалить') }}
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.admin-backup .btn-group .btn {
    min-width: 90px;
}
</style>

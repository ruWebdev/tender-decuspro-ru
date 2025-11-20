<script setup>
import { ref } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useTranslations } from '@/Composables/useTranslations';

const { t } = useTranslations();

const props = defineProps({
    tender: Object,
    customers: Array,
});

const form = useForm({
    customer_id: props.tender.customer_id,
    title: props.tender.title,
    description: props.tender.description || '',
    hidden_comment: props.tender.hidden_comment || '',
    valid_until: props.tender.valid_until,
    valid_until_time: props.tender.valid_until_time || '',
    status: props.tender.status,
    items: props.tender.items?.map(item => ({
        title: item.title,
        quantity: item.quantity,
        unit: item.unit || '',
    })) || [{ title: '', quantity: '', unit: '' }],
});

const addItem = () => {
    form.items.push({ title: '', quantity: '', unit: '' });
};

const removeItem = (index) => {
    if (form.items.length <= 1) {
        return;
    }
    form.items.splice(index, 1);
};

const submit = () => {
    form.put(route('admin.tenders.update', props.tender.id));
};
</script>

<template>
    <AdminLayout>
        <div class="admin-tenders-edit">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">{{ t('admin.tenders.edit_title') }}</h1>
                <Link :href="route('admin.tenders.index')" class="btn btn-outline-secondary">
                {{ t('common.back', 'Назад') }}
                </Link>
            </div>

            <form @submit.prevent="submit" class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ t('admin.tenders.form.customer') }}</label>
                            <select v-model="form.customer_id" class="form-select"
                                :class="{ 'is-invalid': form.errors.customer_id }" required>
                                <option value="">{{ t('common.select', 'Выберите') }}...</option>
                                <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                                    {{ customer.name }} ({{ customer.email }})
                                </option>
                            </select>
                            <div v-if="form.errors.customer_id" class="invalid-feedback">
                                {{ form.errors.customer_id }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ t('admin.tenders.form.status') }}</label>
                            <select v-model="form.status" class="form-select"
                                :class="{ 'is-invalid': form.errors.status }" required>
                                <option value="open">{{ t('admin.tenders.statuses.open') }}</option>
                                <option value="closed">{{ t('admin.tenders.statuses.closed') }}</option>
                                <option value="review">{{ t('admin.tenders.statuses.review') }}</option>
                            </select>
                            <div v-if="form.errors.status" class="invalid-feedback">
                                {{ form.errors.status }}
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">{{ t('admin.tenders.form.title') }}</label>
                            <input type="text" v-model="form.title" class="form-control"
                                :class="{ 'is-invalid': form.errors.title }" required>
                            <div v-if="form.errors.title" class="invalid-feedback">
                                {{ form.errors.title }}
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">{{ t('admin.tenders.form.description') }}</label>
                            <textarea v-model="form.description" class="form-control" rows="3"
                                :class="{ 'is-invalid': form.errors.description }"></textarea>
                            <div v-if="form.errors.description" class="invalid-feedback">
                                {{ form.errors.description }}
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">{{ t('admin.tenders.form.hidden_comment') }}</label>
                            <textarea v-model="form.hidden_comment" class="form-control" rows="2"
                                :class="{ 'is-invalid': form.errors.hidden_comment }"></textarea>
                            <div v-if="form.errors.hidden_comment" class="invalid-feedback">
                                {{ form.errors.hidden_comment }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ t('admin.tenders.form.valid_until') }}</label>
                            <input type="date" v-model="form.valid_until" class="form-control"
                                :class="{ 'is-invalid': form.errors.valid_until }" required>
                            <div v-if="form.errors.valid_until" class="invalid-feedback">
                                {{ form.errors.valid_until }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ t('tenders.label_valid_until_time', 'Время окончания')
                                }}</label>
                            <input type="time" v-model="form.valid_until_time" class="form-control"
                                :class="{ 'is-invalid': form.errors.valid_until_time }">
                            <div v-if="form.errors.valid_until_time" class="invalid-feedback">
                                {{ form.errors.valid_until_time }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-header">
                    <h2 class="m-0">{{ t('tenders.positions_title', 'Позиции тендера') }}</h2>
                </div>
                <div class="card-body">

                    <!-- Позиции тендера -->
                    <div class="mt-4">
                        <div v-if="form.errors.items" class="alert alert-danger">
                            {{ form.errors.items }}
                        </div>

                        <div class="table-responsive mb-3">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>{{ t('tenders.col_item_title') }}</th>
                                        <th>{{ t('tenders.col_quantity') }}</th>
                                        <th>{{ t('tenders.col_unit') }}</th>
                                        <th class="w-10"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in form.items" :key="index">
                                        <td>
                                            <input type="text" v-model="item.title" class="form-control form-control-sm"
                                                :class="{ 'is-invalid': form.errors[`items.${index}.title`] }" required>
                                            <div v-if="form.errors[`items.${index}.title`]" class="invalid-feedback">
                                                {{ form.errors[`items.${index}.title`] }}
                                            </div>
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" min="0" v-model="item.quantity"
                                                class="form-control form-control-sm"
                                                :class="{ 'is-invalid': form.errors[`items.${index}.quantity`] }"
                                                required>
                                            <div v-if="form.errors[`items.${index}.quantity`]" class="invalid-feedback">
                                                {{ form.errors[`items.${index}.quantity`] }}
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" v-model="item.unit" class="form-control form-control-sm"
                                                :class="{ 'is-invalid': form.errors[`items.${index}.unit`] }">
                                            <div v-if="form.errors[`items.${index}.unit`]" class="invalid-feedback">
                                                {{ form.errors[`items.${index}.unit`] }}
                                            </div>
                                        </td>
                                        <td>
                                            <button type="button" @click="removeItem(index)"
                                                class="btn btn-sm btn-ghost-danger" :disabled="form.items.length <= 1">
                                                ✕
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <button type="button" @click="addItem" class="btn btn-sm btn-secondary">
                            {{ t('tenders.button_add_item', 'Добавить позицию') }}
                        </button>
                    </div>
                </div>

                <div class="card-footer text-end">
                    <Link :href="route('admin.tenders.index')" class="btn btn-outline-secondary me-2">
                    {{ t('common.cancel', 'Отмена') }}
                    </Link>
                    <button type="submit" class="btn btn-primary" :disabled="form.processing">
                        <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
                        {{ t('common.save', 'Сохранить') }}
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>

<style scoped>
.btn-ghost-danger {
    color: #ef4444;
    background-color: transparent;
    border-color: transparent;
}

.btn-ghost-danger:hover {
    color: #fff;
    background-color: #ef4444;
}

.w-10 {
    width: 40px;
}
</style>

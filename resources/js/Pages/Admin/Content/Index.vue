<script setup>
import { Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useTranslations } from '@/Composables/useTranslations';

const { t } = useTranslations();
</script>

<template>
    <AdminLayout>
        <div class="admin-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">{{ t('admin.content.title') }}</h1>
            </div>

            <div class="row g-4">
                <!-- Страницы -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div
                                    class="avatar avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                                    <i class="ti ti-file-text"></i>
                                </div>
                                <h5 class="card-title mb-0">{{ t('admin.content.pages') }}</h5>
                            </div>
                            <p class="text-muted mb-3">Управление статическими страницами сайта</p>
                            <Link href="/admin/content/pages" class="btn btn-primary w-100">
                            {{ t('admin.content.actions.edit') }}
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Статьи -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div
                                    class="avatar avatar-sm bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                                    <i class="ti ti-article"></i>
                                </div>
                                <h5 class="card-title mb-0">{{ t('admin.content.articles') }}</h5>
                            </div>
                            <p class="text-muted mb-3">Создание и редактирование статей</p>
                            <Link href="/admin/content/articles" class="btn btn-success w-100">
                            {{ t('admin.content.actions.edit') }}
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Новости -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div
                                    class="avatar avatar-sm bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                                    <i class="ti ti-news"></i>
                                </div>
                                <h5 class="card-title mb-0">{{ t('admin.content.news') }}</h5>
                            </div>
                            <p class="text-muted mb-3">Управление новостями и анонсами</p>
                            <Link href="/admin/content/news" class="btn btn-info w-100">
                            {{ t('admin.content.actions.edit') }}
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Статистика -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Статистика контента</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="h2 text-primary mb-1">12</div>
                                <div class="text-muted">Всего страниц</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="h2 text-success mb-1">45</div>
                                <div class="text-muted">Всего статей</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="h2 text-info mb-1">28</div>
                                <div class="text-muted">Всего новостей</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="h2 text-warning mb-1">7</div>
                                <div class="text-muted">Черновиков</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.avatar {
    width: 40px;
    height: 40px;
    font-size: 18px;
}

.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
</style>

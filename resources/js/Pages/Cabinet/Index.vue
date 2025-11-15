<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const isCustomer = computed(() => user.value?.role === 'customer');
const isSupplier = computed(() => user.value?.role === 'supplier');
</script>

<template>
  <AppLayout>
    <div class="container mb-4">
      <h1 class="h2 mb-3">Личный кабинет</h1>

      <div class="row">
        <div class="col-md-8">
          <div class="card mb-4">
            <div class="card-header">
              <h5 class="mb-0">Профиль</h5>
            </div>
            <div class="card-body">
              <p><strong>Имя:</strong> {{ user?.name }}</p>
              <p><strong>Email:</strong> {{ user?.email }}</p>
              <p><strong>Роль:</strong>
                <span v-if="isCustomer" class="badge bg-primary">Заказчик</span>
                <span v-else-if="isSupplier" class="badge bg-success">Поставщик</span>
              </p>
            </div>
          </div>

          <div v-if="isCustomer" class="card mb-4">
            <div class="card-header">
              <h5 class="mb-0">Мои закупки</h5>
            </div>
            <div class="card-body">
              <p class="text-muted mb-3">Управляйте своими закупками и просматривайте предложения от поставщиков.</p>
              <Link href="/tenders" class="btn btn-primary">
              Перейти к закупкам
              </Link>
            </div>
          </div>

          <div v-if="isSupplier" class="card mb-4">
            <div class="card-header">
              <h5 class="mb-0">Мои предложения</h5>
            </div>
            <div class="card-body">
              <p class="text-muted mb-3">Просматривайте и управляйте своими предложениями по закупкам.</p>
              <Link href="/proposals" class="btn btn-primary">
              Перейти к предложениям
              </Link>
            </div>
          </div>

          <div v-if="isSupplier" class="card mb-4">
            <div class="card-header">
              <h5 class="mb-0">Профиль компании</h5>
            </div>
            <div class="card-body">
              <p class="text-muted mb-3">Обновите информацию о вашей компании.</p>
              <Link href="/profile/supplier" class="btn btn-primary">
              Редактировать профиль
              </Link>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card">
            <div class="card-header">
              <h5 class="mb-0">Быстрые ссылки</h5>
            </div>
            <div class="list-group list-group-flush">
              <Link href="/" class="list-group-item list-group-item-action">
              Главная страница
              </Link>
              <Link href="/proposals" class="list-group-item list-group-item-action">
              Все предложения
              </Link>
              <a href="#" class="list-group-item list-group-item-action">
                Справка
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

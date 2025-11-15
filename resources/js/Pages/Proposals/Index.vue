<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
</script>

<template>
  <AppLayout>
    <div class="container mb-4">
      <h1 class="h2 mb-3">Предложения</h1>

      <div class="alert alert-info">
        <p class="mb-0">Для просмотра ваших предложений, перейдите в
          <Link href="/dashboard">личный кабинет</Link>.
        </p>
      </div>

      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Активные закупки</h5>
          <p class="card-text text-muted">Здесь вы можете просматривать активные закупки и подавать свои предложения.
          </p>
          <Link href="/" class="btn btn-primary">
          Перейти к закупкам
          </Link>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { onMounted, onUnmounted } from 'vue';

onMounted(() => {
  document.body.classList.add('layout-fluid');
});

onUnmounted(() => {
  document.body.classList.remove('layout-fluid');
});
</script>

<template>
  <div class="page">
    <header class="navbar navbar-expand-md navbar-light d-print-none">
      <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
          <span class="navbar-toggler-icon"></span>
        </button>
        <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
          <Link :href="route('dashboard')">{{ $page.props.app?.name || 'RecipeMania' }}</Link>
        </h1>

        <div class="collapse navbar-collapse" id="navbar-menu">
          <div class="d-flex flex-column flex-md-row flex-fill">
            <ul class="navbar-nav">
              <li class="nav-item">
                <Link class="nav-link" :href="route('dashboard')">
                <span class="nav-link-title">Панель</span>
                </Link>
              </li>
              <li class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" role="button"
                  aria-expanded="false">
                  <span class="nav-link-title">Рецепты</span>
                </a>
                <div class="dropdown-menu">
                  <Link class="dropdown-item" href="/recipes">Рецепты</Link>
                  <Link class="dropdown-item" href="/recipe-reviews">Комментарии рецептов</Link>
                  <div class="dropdown-divider"></div>
                  <Link class="dropdown-item" href="/ingredients">Ингредиенты</Link>
                  <Link class="dropdown-item" href="/ingredient-categories">Категории ингредиентов</Link>
                  <div class="dropdown-divider"></div>
                  <Link class="dropdown-item" href="/cuisines">Кухни</Link>
                  <Link class="dropdown-item" href="/dietary-restrictions">Диетические ограничения</Link>
                  <Link class="dropdown-item" href="/tags">Теги</Link>
                </div>
              </li>
              <li class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" role="button"
                  aria-expanded="false">
                  <span class="nav-link-title">Пользователи</span>
                </a>
                <div class="dropdown-menu">
                  <Link class="dropdown-item" href="/users">Все пользователи</Link>
                  <Link class="dropdown-item" href="/users/achievements">Достижения</Link>
                  <Link class="dropdown-item" href="/users/categories">Категории</Link>
                  <Link class="dropdown-item" href="/users/points">Баллы</Link>
                </div>
              </li>
              <li class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" role="button"
                  aria-expanded="false">
                  <span class="nav-link-title">АПИ</span>
                </a>
                <div class="dropdown-menu">
                  <Link class="dropdown-item" href="/api/smtp">SMTP</Link>
                  <Link class="dropdown-item" href="/api/social-networks">Соцсети</Link>
                  <Link class="dropdown-item" href="/api/payment-systems">Платежные системы</Link>
                </div>
              </li>
              <li class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" role="button"
                  aria-expanded="false">
                  <span class="nav-link-title">Доставка</span>
                </a>
                <div class="dropdown-menu">
                  <Link class="dropdown-item" href="/delivery-services">Сервисы доставки</Link>
                  <Link class="dropdown-item" href="/delivery/user-settings">Настройки пользователей</Link>
                  <Link class="dropdown-item" href="/delivery/recipe-options">Опции по рецептам</Link>
                </div>
              </li>
              <li class="nav-item">
                <Link class="nav-link" href="/notifications">
                  <span class="nav-link-title">Уведомления</span>
                </Link>
              </li>
              <li class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" role="button" aria-expanded="false">
                  <span class="nav-link-title">Подписки</span>
                </a>
                <div class="dropdown-menu">
                  <Link class="dropdown-item" href="/subscriptions">Подписки</Link>
                  <Link class="dropdown-item" href="/subscription-plans">Тарифы подписок</Link>
                </div>
              </li>
              <li class="nav-item">
                <Link class="nav-link" href="/meal-offers">
                  <span class="nav-link-title">Объявления</span>
                </Link>
              </li>
              <li class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" role="button"
                  aria-expanded="false">
                  <span class="nav-link-title">Новости</span>
                </a>
                <div class="dropdown-menu">
                  <Link class="dropdown-item" href="/news">Лента</Link>
                  <Link class="dropdown-item" href="/news/comments">Комментарии</Link>
                  <Link class="dropdown-item" href="/news/likes">Лайки</Link>
                  <Link class="dropdown-item" href="/news/types">Типы</Link>
                  <Link class="dropdown-item" href="/news/categories">Категории</Link>
                </div>
              </li>
              <li class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" role="button"
                  aria-expanded="false">
                  <span class="nav-link-title">Онбординг</span>
                </a>
                <div class="dropdown-menu">
                  <Link class="dropdown-item" href="/onboarding/steps">Шаги</Link>
                  <Link class="dropdown-item" href="/onboarding/user">Пользователи</Link>
                </div>
              </li>
              <li class="nav-item">
                <Link class="nav-link" href="/moderation-logs">
                  <span class="nav-link-title">Логи модерации</span>
                </Link>
              </li>
              <li class="nav-item">

              </li>
              <slot name="nav" />
            </ul>
          </div>
        </div>

        <div class="navbar-nav flex-row order-md-last">
          <div class="nav-item dropdown">
            <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown">
              <span class="avatar" style="background-image: url(/tabler/static/avatars/000m.jpg)"></span>
              <div class="d-none d-xl-block ps-2">
                <div>{{ $page.props.auth?.user?.name }}</div>
                <div class="mt-1 small text-muted">&nbsp;</div>
              </div>
            </a>
            <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
              <Link class="dropdown-item" :href="route('profile.edit')">Профиль</Link>
              <div class="dropdown-divider"></div>
              <Link class="dropdown-item" :href="route('logout')" method="post" as="button">Выйти</Link>
            </div>
          </div>
        </div>
      </div>
    </header>

    <div class="page-wrapper">
      <div v-if="$slots.header" class="page-header d-print-none">
        <div class="container-xl">
          <div class="row g-2 align-items-center">
            <div class="col">
              <slot name="header" />
            </div>
          </div>
        </div>
      </div>

      <div class="page-body">
        <div class="container-xl">
          <slot />
        </div>
      </div>
    </div>
  </div>
</template>

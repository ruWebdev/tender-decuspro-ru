<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useTranslations } from '@/Composables/useTranslations';

const { t } = useTranslations();
const page = usePage();

const proposals = computed(() => page.props.proposals || { data: [], links: [] });

const formatDate = (value) => {
  if (!value) return '-';
  const locale = page.props.locale || 'ru';
  return new Date(value).toLocaleDateString(locale, { day: '2-digit', month: '2-digit', year: 'numeric' });
};
</script>

<template>
  <AppLayout>

    <Head :title="t('proposals.index_title')" />
    <div class="container mb-4">
      <h1 class="h2 mb-3">{{ t('proposals.index_title') }}</h1>

      <div class="card">
        <div class="table-responsive">
          <table class="table table-vcenter card-table">
            <thead>
              <tr>
                <th>{{ t('tenders.col_title') }}</th>
                <th>{{ t('tenders.col_valid_until') }}</th>
                <th>{{ t('tenders.col_status') }}</th>
                <th>{{ t('tenders.col_items') }}</th>
                <th class="w-25">{{ t('tenders.col_actions') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="!proposals.data || proposals.data.length === 0">
                <td colspan="5" class="text-center text-muted py-4">{{ t('common.no_data', 'Нет данных') }}</td>
              </tr>
              <tr v-for="p in proposals.data" :key="p.id">
                <td>
                  <div class="text-truncate" style="max-width: 480px;">
                    <strong class="text-reset">{{ p.tender?.title }}</strong>
                  </div>
                </td>
                <td>{{ formatDate(p.tender?.valid_until) }}</td>
                <td>
                  <span v-if="p.status === 'submitted'" class="badge bg-primary text-light">{{
                    t('proposals.status_submitted', 'Отправлено') }}</span>
                  <span v-else-if="p.status === 'draft'" class="badge bg-secondary text-light">{{
                    t('proposals.status_draft', 'Черновик') }}</span>
                  <span v-else-if="p.status === 'withdrawn'" class="badge bg-warning text-dark">{{
                    t('proposals.status_withdrawn', 'Отозвано') }}</span>
                  <span v-else class="badge bg-secondary text-light">{{ p.status }}</span>
                </td>
                <td>{{ p.items_count ?? 0 }}</td>
                <td>
                  <div class="btn-list flex-nowrap">
                    <Link :href="route('tenders.show', p.tender_id)" class="btn btn-sm btn-ghost-secondary">{{
                      t('tenders.action_open') }}</Link>
                    <Link :href="route('proposals.participate', { tender: p.tender_id })"
                      class="btn btn-sm btn-ghost-primary">{{ t('proposals.action_edit', 'Редактировать') }}</Link>
                    <form v-if="p.status === 'submitted'" :action="route('proposals.withdraw', { proposal: p.id })"
                      method="post" class="d-inline">
                      <input type="hidden" name="_method" value="POST" />
                      <button type="submit" class="btn btn-sm btn-ghost-danger">{{ t('proposals.action_withdraw',
                        'Отозвать') }}</button>
                    </form>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="proposals.links && proposals.links.length > 3" class="card-footer">
          <nav class="d-flex justify-content-center">
            <ul class="pagination mb-0">
              <li v-for="(link, index) in proposals.links" :key="index" class="page-item"
                :class="{ active: link.active, disabled: !link.url }">
                <Link v-if="link.url" :href="link.url" class="page-link" v-html="link.label" />
                <span v-else class="page-link" v-html="link.label" />
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

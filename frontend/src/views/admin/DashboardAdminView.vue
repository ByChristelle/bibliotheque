<script setup>
import { onMounted, computed, ref } from 'vue'
import { useThemeStore } from '@/stores/theme'
import { useRouter } from 'vue-router'
import { useDepositRequestsStore } from '@/stores/depositRequests'
import api from '@/services/axios'
import { Loader2 } from 'lucide-vue-next'

const theme        = useThemeStore()
const router       = useRouter()
const depositStore = useDepositRequestsStore()

const stats   = ref(null)
const loading = ref(true)

onMounted(async () => {
  await depositStore.fetchAll()
  try {
    const res = await api.get('/stats')
    stats.value = res.data.stats
  } catch (e) {
    console.error('Erreur stats', e)
  } finally {
    loading.value = false
  }
})

const pendingCount = computed(() => depositStore.pendingCount)

const statCards = computed(() => {
  if (!stats.value) return []
  return [
    {
      label: 'Références publiées',
      value: stats.value.references,
      icon: 'book',
      color: 'text-[#7A0026]',
      bg: 'bg-[#7A0026]/10',
      to: '/admin/references',
    },
    {
      label: 'Utilisateurs',
      value: stats.value.users,
      icon: 'users',
      color: 'text-blue-500',
      bg: 'bg-blue-500/10',
      to: '/admin/utilisateurs',
    },
    {
      label: 'Catégories',
      value: stats.value.categories,
      icon: 'tags',
      color: 'text-purple-500',
      bg: 'bg-purple-500/10',
      to: '/admin/categories',
    },
    {
      label: 'Téléchargements',
      value: stats.value.downloads,
      icon: 'download',
      color: 'text-green-500',
      bg: 'bg-green-500/10',
      to: null,
    },
    {
      label: 'Demandes soumises',
      value: stats.value.total_requests,
      icon: 'send',
      color: 'text-orange-500',
      bg: 'bg-orange-500/10',
      to: '/admin/demandes',
    },
    {
      label: 'En cours d\'affectation',
      value: stats.value.affectations,
      icon: 'people',
      color: 'text-cyan-500',
      bg: 'bg-cyan-500/10',
      to: '/admin/affectations',
    },
    {
      label: 'Références publiées via dépôt',
      value: stats.value.published,
      icon: 'check-circle',
      color: 'text-emerald-500',
      bg: 'bg-emerald-500/10',
      to: '/admin/demandes',
    },
    {
      label: 'Dépôts en attente',
      value: pendingCount.value,
      icon: 'clock',
      color: 'text-yellow-500',
      bg: 'bg-yellow-500/10',
      to: '/admin/affectations',
    },
  ]
})
</script>

<template>
  <div>
    <!-- En-tête -->
    <div class="mb-8">
      <h1 :class="['text-2xl font-bold', theme.isDark ? 'text-white' : 'text-[#2D2D2D]']">
        Tableau de bord
      </h1>
      <p class="text-sm text-[#94A3B8] mt-0.5">Vue d'ensemble de la plateforme</p>
    </div>

    <!-- Loader -->
    <div v-if="loading" class="flex items-center justify-center py-16">
      <Loader2 class="w-8 h-8 animate-spin text-[#7A0026]" />
      <span class="ml-3 text-sm text-[#94A3B8]">Chargement...</span>
    </div>

    <template v-else>
      <!-- Grille de stats -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div
          v-for="card in statCards" :key="card.label"
          :class="['rounded-2xl p-5 border transition-all duration-200',
            card.to ? 'cursor-pointer hover:-translate-y-0.5 hover:shadow-md' : '',
            theme.isDark ? 'bg-white/5 border-white/[0.07]' : 'bg-white border-[#ECECEC] shadow-sm']"
          @click="card.to && router.push(card.to)"
        >
          <!-- Icône -->
          <div class="flex items-center justify-between mb-3">
            <div :class="['w-10 h-10 rounded-xl flex items-center justify-center', card.bg]">
              <!-- SVG dynamique selon icon -->
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" :class="['w-5 h-5', card.color]">
                <path v-if="card.icon === 'book'" stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                <path v-else-if="card.icon === 'users'" stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                <path v-else-if="card.icon === 'tags'" stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581a1.947 1.947 0 0 0 2.754 0l4.318-4.318a1.947 1.947 0 0 0 0-2.754L11.16 3.659A2.25 2.25 0 0 0 9.568 3ZM6 6h.008v.008H6V6Z" />
                <path v-else-if="card.icon === 'download'" stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                <path v-else-if="card.icon === 'send'" stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                <path v-else-if="card.icon === 'people'" stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                <path v-else-if="card.icon === 'check-circle'" stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                <path v-else-if="card.icon === 'clock'" stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
              </svg>
            </div>
            <i v-if="card.to" class="pi pi-arrow-up-right text-xs text-[#94A3B8]"></i>
          </div>

          <!-- Valeur -->
          <div :class="['text-3xl font-bold mb-0.5', theme.isDark ? 'text-white' : 'text-[#2D2D2D]']">
            {{ card.value ?? '—' }}
          </div>
          <p class="text-xs text-[#94A3B8] leading-tight">{{ card.label }}</p>
        </div>
      </div>

      <!-- Cadre dépôts en attente -->
      <div
        :class="['rounded-2xl p-6 cursor-pointer transition-all hover:-translate-y-0.5 border-2',
          theme.isDark ? 'bg-yellow-500/10 border-yellow-500/30 hover:bg-yellow-500/15' : 'bg-yellow-50 border-yellow-200 hover:bg-yellow-100']"
        @click="router.push('/admin/affectations')"
      >
        <div class="flex items-center justify-between">
          <div>
            <p :class="['text-xs font-semibold uppercase tracking-widest mb-1', theme.isDark ? 'text-yellow-400' : 'text-yellow-700']">
              Dépôts en attente d'affectation
            </p>
            <div class="flex items-end gap-2">
              <span :class="['text-4xl font-bold', theme.isDark ? 'text-yellow-300' : 'text-yellow-800']">
                {{ pendingCount }}
              </span>
              <span :class="['text-sm mb-1', theme.isDark ? 'text-yellow-500' : 'text-yellow-600']">
                demande{{ pendingCount > 1 ? 's' : '' }} à affecter
              </span>
            </div>
          </div>
          <div :class="['w-14 h-14 rounded-2xl flex items-center justify-center', theme.isDark ? 'bg-yellow-500/20' : 'bg-yellow-200']">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" :class="['w-7 h-7', theme.isDark ? 'text-yellow-400' : 'text-yellow-700']">
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 0 1 2.008 1.24l.885 1.77a2.25 2.25 0 0 0 2.007 1.24h1.98a2.25 2.25 0 0 0 2.007-1.24l.885-1.77a2.25 2.25 0 0 1 2.007-1.24h3.86M2.25 13.5V6a2.25 2.25 0 0 1 2.25-2.25h15A2.25 2.25 0 0 1 21.75 6v7.5" />
            </svg>
          </div>
        </div>
        <p :class="['text-xs mt-3 flex items-center gap-1', theme.isDark ? 'text-yellow-500' : 'text-yellow-600']">
          Cliquez pour affecter les demandes
        <i class="pi pi-arrow-right text-xs"></i>
        </p>
      </div>
    </template>
  </div>
</template>

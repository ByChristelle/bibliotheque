<script setup>
import { onMounted, computed } from 'vue'
import { useThemeStore } from '@/stores/theme'
import { useRouter } from 'vue-router'
import { useDepositRequestsStore } from '@/stores/depositRequests'

const theme = useThemeStore()
const router = useRouter()
const depositStore = useDepositRequestsStore()

onMounted(async () => {
  await depositStore.fetchAll()
})

const pendingCount = computed(() => depositStore.pendingCount)

const shortcuts = [
  { label: 'Gérer les références', icon: 'pi pi-book', to: '/admin/references' },
  { label: 'Gérer les utilisateurs', icon: 'pi pi-users', to: '/admin/utilisateurs' },
  { label: 'Catégories', icon: 'pi pi-tags', to: '/admin/categories' },
  { label: 'Voir les logs', icon: 'pi pi-history', to: '/admin/logs' },
]
</script>

<template>
  <div>
    <div class="mb-6">
      <h1 :class="['text-2xl font-bold', theme.isDark ? 'text-bordeaux-100' : 'text-bordeaux-900']">
        Tableau de bord
      </h1>
      <p :class="['text-sm', theme.isDark ? 'text-bordeaux-400' : 'text-bordeaux-600']">
        Vue d'ensemble de la plateforme
      </p>
    </div>

    <!-- Cadre dépôts en attente -->
    <div
      :class="['rounded-2xl p-6 mb-8 cursor-pointer transition-all hover:-translate-y-1 border-2',
        theme.isDark
          ? 'bg-yellow-500/10 border-yellow-500/30 hover:bg-yellow-500/15'
          : 'bg-yellow-50 border-yellow-300 hover:bg-yellow-100']"
      @click="router.push('/admin/affectations')"
    >
      <div class="flex items-center justify-between">
        <div>
          <p :class="['text-xs font-semibold uppercase tracking-widest mb-1',
            theme.isDark ? 'text-yellow-400' : 'text-yellow-700']">
            Dépôts en attente d'affectation
          </p>
          <div class="flex items-end gap-2">
            <span :class="['text-4xl font-bold', theme.isDark ? 'text-yellow-300' : 'text-yellow-800']">
              {{ depositStore.loading ? '...' : pendingCount }}
            </span>
            <span :class="['text-sm mb-1', theme.isDark ? 'text-yellow-500' : 'text-yellow-600']">
              demande{{ pendingCount > 1 ? 's' : '' }} en attente
            </span>
          </div>
        </div>
        <div :class="['w-14 h-14 rounded-2xl flex items-center justify-center',
          theme.isDark ? 'bg-yellow-500/20' : 'bg-yellow-200']">
          <i :class="['pi pi-inbox text-2xl', theme.isDark ? 'text-yellow-400' : 'text-yellow-700']"></i>
        </div>
      </div>
      <p :class="['text-xs mt-3 flex items-center gap-1',
        theme.isDark ? 'text-yellow-500' : 'text-yellow-600']">
        Cliquez pour affecter les demandes
        <i class="pi pi-arrow-right text-xs"></i>
      </p>
    </div>

    <!-- Raccourcis -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <div
        v-for="s in shortcuts" :key="s.to"
        :class="['rounded-2xl p-5 backdrop-blur-xl border cursor-pointer transition-all hover:-translate-y-1',
          theme.isDark ? 'bg-white/5 border-white/10 hover:bg-white/10' : 'bg-white/30 border-white/50 hover:bg-white/50']"
        @click="router.push(s.to)">
        <i :class="[s.icon, 'text-xl mb-2 block', theme.isDark ? 'text-bordeaux-400' : 'text-bordeaux-600']"></i>
        <span :class="['text-sm font-medium', theme.isDark ? 'text-bordeaux-300' : 'text-bordeaux-700']">
          {{ s.label }}
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import { useThemeStore } from '@/stores/theme'
import { useRouter } from 'vue-router'
import { useDepositRequestsStore } from '@/stores/depositRequests'
import { useAuthStore } from '@/stores/auth'
import Button from 'primevue/button'
import { Loader2 } from 'lucide-vue-next'

const theme        = useThemeStore()
const router       = useRouter()
const depositStore = useDepositRequestsStore()
const auth         = useAuthStore()

onMounted(async () => {
  await depositStore.fetchMyAssigned()
})

const total     = computed(() => depositStore.assignedRequests.length)
const pending   = computed(() => depositStore.assignedRequests.filter(d => d.status === 'pending').length)
const validated = computed(() => depositStore.assignedRequests.filter(d => d.status === 'approved_by_manager').length)
const rejected  = computed(() => depositStore.assignedRequests.filter(d => d.status === 'rejected_by_manager').length)

const recent = computed(() => depositStore.assignedRequests.slice(0, 3))

const statusLabel = {
  pending:             'En attente',
  approved_by_manager: 'Validée',
  rejected_by_manager: 'Refusée',
  published:           'Publiée',
}
const statusClass = {
  pending:             'bg-yellow-100 text-yellow-700',
  approved_by_manager: 'bg-green-100 text-green-700',
  rejected_by_manager: 'bg-red-100 text-red-700',
  published:           'bg-blue-100 text-blue-700',
}
</script>

<template>
  <div>
    <!-- En-tête -->
    <div class="mb-6">
      <h1 :class="['text-2xl font-bold', theme.isDark ? 'text-white' : 'text-[#2D2D2D]']">
        Bonjour, {{ auth.user?.first_name }} 👋
      </h1>
      <p class="text-sm text-[#94A3B8] mt-0.5">Demandes en attente de votre examen</p>
    </div>

    <!-- Loader -->
    <div v-if="depositStore.loading" class="flex items-center justify-center py-12">
      <Loader2 class="w-8 h-8 animate-spin text-[#7A0026]" />
      <span class="ml-3 text-sm text-[#94A3B8]">Chargement...</span>
    </div>

    <template v-else>
      <!-- Stats -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div v-for="stat in [
          { label: 'Demandes affectées', value: total,     icon: 'inbox',        color: 'text-[#7A0026]',  bg: 'bg-[#7A0026]/10' },
          { label: 'En attente',          value: pending,   icon: 'clock',        color: 'text-yellow-500', bg: 'bg-yellow-500/10' },
          { label: 'Validées',            value: validated, icon: 'check-circle', color: 'text-green-500',  bg: 'bg-green-500/10' },
          { label: 'Refusées',            value: rejected,  icon: 'x-circle',     color: 'text-red-500',    bg: 'bg-red-500/10' },
        ]" :key="stat.label"
          :class="['rounded-2xl p-5 border transition-all', theme.isDark ? 'bg-white/5 border-white/[0.07]' : 'bg-white border-[#ECECEC] shadow-sm']">
          <div :class="['w-10 h-10 rounded-xl flex items-center justify-center mb-3', stat.bg]">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" :class="['w-5 h-5', stat.color]">
              <path v-if="stat.icon === 'inbox'" stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 0 1 2.008 1.24l.885 1.77a2.25 2.25 0 0 0 2.007 1.24h1.98a2.25 2.25 0 0 0 2.007-1.24l.885-1.77a2.25 2.25 0 0 1 2.007-1.24h3.86M2.25 13.5V6a2.25 2.25 0 0 1 2.25-2.25h15A2.25 2.25 0 0 1 21.75 6v7.5" />
              <path v-else-if="stat.icon === 'clock'" stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
              <path v-else-if="stat.icon === 'check-circle'" stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
              <path v-else-if="stat.icon === 'x-circle'" stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
          </div>
          <div :class="['text-3xl font-bold mb-0.5', theme.isDark ? 'text-white' : 'text-[#2D2D2D]']">
            {{ stat.value }}
          </div>
          <p class="text-xs text-[#94A3B8]">{{ stat.label }}</p>
        </div>
      </div>

      <!-- Demandes récentes -->
      <div :class="['rounded-2xl p-6 border', theme.isDark ? 'bg-white/5 border-white/[0.07]' : 'bg-white border-[#ECECEC] shadow-sm']">
        <div class="flex items-center justify-between mb-4">
          <h2 :class="['font-semibold text-sm', theme.isDark ? 'text-white' : 'text-[#2D2D2D]']">
            Demandes récentes
          </h2>
          <Button label="Voir tout" text size="small" @click="router.push('/responsable/demandes')" />
        </div>

        <!-- Vide -->
        <div v-if="!recent.length" class="text-center py-8">
          <i class="pi pi-inbox text-3xl text-[#94A3B8] mb-2 block"></i>
          <p class="text-sm text-[#94A3B8]">Aucune demande affectée pour le moment</p>
        </div>

        <div v-else class="flex flex-col gap-2.5">
          <div v-for="d in recent" :key="d.id"
            :class="['flex items-center justify-between p-3 rounded-xl transition-all',
              theme.isDark ? 'bg-white/5 hover:bg-white/10' : 'bg-[#F8F6F6] hover:bg-[#F0ECEC]']">
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium truncate" :class="theme.isDark ? 'text-white' : 'text-[#2D2D2D]'">
                {{ d.title }}
              </p>
              <p class="text-xs text-[#94A3B8] mt-0.5">
                {{ d.applicant ? `${d.applicant.first_name} ${d.applicant.last_name}` : '—' }}
                · {{ new Date(d.created_at).toLocaleDateString('fr-FR') }}
              </p>
            </div>
            <div class="flex items-center gap-2 ml-3 shrink-0">
              <span :class="['text-xs px-2 py-0.5 rounded-full font-medium', statusClass[d.status] || 'bg-gray-100 text-gray-600']">
                {{ statusLabel[d.status] || d.status }}
              </span>
              <Button label="Examiner" size="small" @click="router.push('/responsable/demandes')" />
            </div>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

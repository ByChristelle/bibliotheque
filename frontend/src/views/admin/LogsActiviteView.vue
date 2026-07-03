<script setup>
import { ref, onMounted } from 'vue'
import { useThemeStore } from '@/stores/theme'
import { useRoute } from 'vue-router'
import api from '@/services/axios'
import { Loader2 } from 'lucide-vue-next'

const theme = useThemeStore()
const route = useRoute()

const logs = ref([])
const loading = ref(false)
const currentPage = ref(1)
const lastPage = ref(1)

function getLogType(action) {
  if (action.includes('approuvée') || action.includes('publiée') || action.includes('validée')) return 'success'
  if (action.includes('rejetée') || action.includes('refusée')) return 'danger'
  return 'info'
}

function formatDateTime(dateStr) {
  if (!dateStr) return ''
  const date = new Date(dateStr)
  return date.toLocaleDateString('fr-FR', {
    day: '2-digit', month: 'short', year: 'numeric',
    hour: '2-digit', minute: '2-digit'
  })
}

async function fetchLogs() {
  loading.value = true
  try {
    // Check if we have a depositRequestId in route query (coming from GestionDemandesView)
    const depositRequestId = route.query.depositRequestId

    if (depositRequestId) {
      const response = await api.get(`/activity-logs/deposit-request/${depositRequestId}`)
      logs.value = response.data.logs
    } else {
      const response = await api.get('/activity-logs', {
        params: { page: currentPage.value }
      })
      logs.value = response.data.logs
      lastPage.value = response.data.last_page
    }
  } catch (error) {
    console.error('Erreur lors de la récupération des logs', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchLogs()
})
</script>

<template>
  <div>
    <div class="mb-6">
      <h1 :class="['text-2xl font-bold', theme.isDark ? 'text-bordeaux-100' : 'text-bordeaux-900']">
        Journaux d'activité
      </h1>
      <p :class="['text-sm', theme.isDark ? 'text-bordeaux-400' : 'text-bordeaux-600']">
        {{ route.query.depositRequestId ? "Historique de la demande" : "Historique des actions effectuées sur la plateforme" }}
      </p>
    </div>

    <!-- Loader -->
    <div v-if="loading" class="flex items-center justify-center py-16">
      <Loader2 class="w-10 h-10 animate-spin text-bordeaux-600" />
      <span class="ml-3 text-sm text-[#94A3B8]">Chargement...</span>
    </div>

    <!-- Logs list -->
    <div v-else-if="logs.length > 0" :class="['rounded-2xl backdrop-blur-xl border overflow-hidden',
      theme.isDark ? 'bg-white/5 border-white/10' : 'bg-white/30 border-white/50']">
      <div
        v-for="(log, index) in logs" :key="log.id"
        :class="['flex items-start gap-4 p-4 transition-colors',
          index !== logs.length - 1 && (theme.isDark ? 'border-b border-white/5' : 'border-b border-bordeaux-100/50'),
          theme.isDark ? 'hover:bg-white/5' : 'hover:bg-bordeaux-50/30']">
        <i :class="[
          getLogType(log.action) === 'success' ? 'pi pi-check-circle text-green-500' :
          getLogType(log.action) === 'danger' ? 'pi pi-times-circle text-red-500' :
          'pi pi-info-circle text-blue-500',
          'text-xl mt-0.5 shrink-0'
        ]"></i>
        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-2 flex-wrap">
            <span :class="['text-sm font-semibold', theme.isDark ? 'text-bordeaux-200' : 'text-bordeaux-800']">
              {{ log.user ? `${log.user.first_name} ${log.user.last_name}` : 'Système' }}
            </span>
            <span :class="['text-sm', theme.isDark ? 'text-bordeaux-400' : 'text-bordeaux-600']">
              — {{ log.action }}
            </span>
          </div>
          <p v-if="log.target_table" :class="['text-xs', theme.isDark ? 'text-bordeaux-500' : 'text-bordeaux-400']">
            {{ log.target_table === 'deposit_requests' ? `Demande #${log.target_id}` : log.target_table }}
          </p>
        </div>
        <span :class="['text-xs shrink-0', theme.isDark ? 'text-bordeaux-500' : 'text-bordeaux-400']">
          {{ formatDateTime(log.created_at) }}
        </span>
      </div>
    </div>

    <!-- Empty state -->
    <div v-else class="flex flex-col items-center justify-center py-16">
      <i class="pi pi-inbox text-5xl text-[#94A3B8] mb-3 block"></i>
      <p class="font-medium text-[#94A3B8]">Aucun journal d'activité trouvé</p>
    </div>
  </div>
</template>

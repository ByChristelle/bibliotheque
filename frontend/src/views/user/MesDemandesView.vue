<script setup>
import { computed, onMounted, ref } from 'vue'
import { useThemeStore } from '@/stores/theme'
import { useDepositRequestsStore } from '@/stores/depositRequests'
import { Loader2 } from 'lucide-vue-next'
import Button from 'primevue/button'
import DemandeDetailDialog from '@/components/DemandeDetailDialog.vue'

const theme         = useThemeStore()
const requestsStore = useDepositRequestsStore()

const myRequests = computed(() => requestsStore.myRequests)
onMounted(async () => { await requestsStore.fetchMyRequests() })

const showDetailDialog = ref(false)
const selectedRequest  = ref(null)

function voirDetail(req) { selectedRequest.value = req; showDetailDialog.value = true }

const statusLabel = { pending:'En attente', assigned:'Affectée', approved_by_manager:'Validée', rejected_by_manager:'Refusée', published:'Publiée', rejected:'Rejetée' }
const statusClass = { pending:'bg-yellow-100 text-yellow-700', assigned:'bg-blue-100 text-blue-700', approved_by_manager:'bg-green-100 text-green-700', rejected_by_manager:'bg-red-100 text-red-700', published:'bg-blue-100 text-blue-700', rejected:'bg-red-100 text-red-700' }
</script>

<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 :class="['text-2xl font-bold', theme.isDark ? 'text-bordeaux-100' : 'text-bordeaux-900']">
          Mes demandes de dépôt
        </h1>
        <p :class="['text-sm', theme.isDark ? 'text-bordeaux-400' : 'text-bordeaux-600']">
          Suivez l'avancement de vos demandes
        </p>
      </div>
    </div>

    <!-- Loader -->
    <div class="flex items-center justify-center z-50 py-12" v-if="requestsStore.loading">
      <Loader2 class="w-10 h-10 text-primary-500 animate-spin" />
      <span class="ml-3 text-sm">Chargement des demandes...</span>
    </div>

    <!-- Liste -->
    <div v-else-if="myRequests.length > 0" class="space-y-4">
      <div v-for="req in myRequests" :key="req.id"
        :class="['rounded-2xl p-5 border flex items-center justify-between',
          theme.isDark ? 'bg-white/5 border-white/10' : 'bg-white border-[#ECECEC] shadow-sm']">
        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-3 mb-2 flex-wrap">
            <p :class="['font-semibold truncate', theme.isDark ? 'text-white' : 'text-[#2D2D2D]']">{{ req.title }}</p>
            <span :class="['px-2.5 py-0.5 rounded-full text-xs font-medium shrink-0', statusClass[req.status] || 'bg-gray-100 text-gray-600']">
              {{ statusLabel[req.status] || req.status }}
            </span>
          </div>
          <div class="flex items-center gap-4 text-xs text-[#94A3B8]">
            <span>{{ new Date(req.created_at).toLocaleDateString('fr-FR') }}</span>
            <span v-if="req.assignedManager">
              Responsable : {{ req.assignedManager.first_name }} {{ req.assignedManager.last_name }}
            </span>
          </div>
        </div>
        <button @click="voirDetail(req)"
          class="ml-4 flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 text-[#60a5fa] bg-[#60a5fa]/10 hover:bg-[#60a5fa]/20 shrink-0">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
          </svg>
          Détails
        </button>
      </div>
    </div>

    <!-- Vide -->
    <div v-else class="text-center py-12">
      <i class="pi pi-inbox text-4xl text-[#94A3B8] mb-3 block"></i>
      <p :class="['text-lg font-medium', theme.isDark ? 'text-bordeaux-300' : 'text-bordeaux-700']">
        Aucune demande pour le moment
      </p>
    </div>

    <DemandeDetailDialog v-model:visible="showDetailDialog" :demande="selectedRequest" />
  </div>
</template>

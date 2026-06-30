<script setup>
import { computed, onMounted, ref } from 'vue'
import { useThemeStore } from '@/stores/theme'
import { useDepositRequestsStore } from '@/stores/depositRequests'
import { Loader2 } from 'lucide-vue-next'
import { useToast } from 'primevue'
import Button from 'primevue/button'
import Dialog from 'primevue/dialog'

const toast = useToast()
const theme = useThemeStore()
const requestsStore = useDepositRequestsStore()

// Pour l'affichage
const myRequests = computed(() => requestsStore.myRequests)
onMounted(async () => {
  await requestsStore.fetchMyRequests()
})

// Pour voir le détail d'une demande
const showDetailDialog = ref(false)
const selectedRequest = ref(null)

function voirDetail(req) {
  selectedRequest.value = req
  showDetailDialog.value = true
}

// Fonction pour afficher le statut en français
function getStatusLabel(status) {
  const labels = {
    pending: 'En attente',
    assigned: 'Affectée',
    in_review: 'En revue',
    approved: 'Approuvée',
    rejected: 'Rejetée'
  }
  return labels[status] || status
}

// Fonction pour la couleur du badge statut
function getStatusClass(status) {
  switch (status) {
    case 'pending': return 'bg-yellow-100 text-yellow-800'
    case 'assigned': return 'bg-blue-100 text-blue-800'
    case 'in_review': return 'bg-purple-100 text-purple-800'
    case 'approved': return 'bg-green-100 text-green-800'
    case 'rejected': return 'bg-red-100 text-red-800'
    default: return 'bg-gray-100 text-gray-800'
  }
}
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

    <!-- Loader pendant le chargement -->
    <div class="flex items-center justify-center z-50 py-12" v-if="requestsStore.loading">
      <Loader2 class="w-10 h-10 text-primary-500 animate-spin" />
      <span class="ml-3 text-sm">Chargement des demandes...</span>
    </div>

    <!-- Liste des demandes -->
    <div v-else-if="myRequests.length > 0" class="space-y-4">
      <div v-for="req in myRequests" :key="req.id"
        :class="['rounded-2xl p-5 backdrop-blur-xl border flex items-center justify-between',
          theme.isDark ? 'bg-white/5 border-white/10' : 'bg-white/30 border-white/50']">
        <div class="flex-1">
          <div class="flex items-center gap-3 mb-2">
            <p :class="['font-semibold', theme.isDark ? 'text-bordeaux-200' : 'text-bordeaux-800']">{{ req.title }}</p>
            <span :class="['px-3 py-1 rounded-full text-xs font-medium', getStatusClass(req.status)]">
              {{ getStatusLabel(req.status) }}
            </span>
          </div>
          <p :class="['text-sm mb-2', theme.isDark ? 'text-bordeaux-400' : 'text-bordeaux-600']">
            {{ req.description || 'Aucune description' }}
          </p>
          <div class="flex items-center gap-4 text-xs" :class="theme.isDark ? 'text-bordeaux-500' : 'text-bordeaux-500'">
            <span>Créée le {{ new Date(req.created_at).toLocaleDateString('fr-FR') }}</span>
            <span v-if="req.assignedManager">
              Affectée à : {{ req.assignedManager.first_name }} {{ req.assignedManager.last_name }}
            </span>
          </div>
        </div>
        <div>
          <button title="Voir le détail" @click="voirDetail(req)"
            class="w-8 h-8 rounded-lg flex items-center justify-center text-[#94A3B8] hover:text-[#60a5fa] hover:bg-[#60a5fa]/10 transition-all duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-4 h-4">
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Aucune demande -->
    <div v-else class="text-center py-12">
      <p :class="['text-lg font-medium', theme.isDark ? 'text-bordeaux-300' : 'text-bordeaux-700']">
        Aucune demande pour le moment
      </p>
    </div>

    <!-- Modal de détail -->
    <Dialog v-model:visible="showDetailDialog" header="Détail de la demande" :modal="true" :style="{ width: '500px' }">
      <div v-if="selectedRequest" class="flex flex-col gap-4">
        <div class="flex items-center gap-3">
          <span :class="['font-semibold text-lg', theme.isDark ? 'text-bordeaux-200' : 'text-bordeaux-800']">{{ selectedRequest.title }}</span>
          <span :class="['px-3 py-1 rounded-full text-xs font-medium', getStatusClass(selectedRequest.status)]">
            {{ getStatusLabel(selectedRequest.status) }}
          </span>
        </div>

        <div class="flex flex-col gap-1">
          <span class="text-xs font-semibold uppercase tracking-widest text-[#94A3B8]">Description</span>
          <span class="text-sm">{{ selectedRequest.description || '—' }}</span>
        </div>

        <div v-if="selectedRequest.proposed_file" class="flex flex-col gap-1">
          <span class="text-xs font-semibold uppercase tracking-widest text-[#94A3B8]">Fichier proposé</span>
          <a :href="`http://localhost:8000/storage/${selectedRequest.proposed_file}`" target="_blank"
            class="text-sm text-blue-600 hover:underline">
            Voir le fichier
          </a>
        </div>

        <div class="flex flex-col gap-1">
          <span class="text-xs font-semibold uppercase tracking-widest text-[#94A3B8]">Responsable</span>
          <span class="text-sm">
            {{ selectedRequest.assignedManager 
              ? `${selectedRequest.assignedManager.first_name} ${selectedRequest.assignedManager.last_name}` 
              : 'En attente d\'affectation' }}
          </span>
        </div>

        <div class="flex flex-col gap-1">
          <span class="text-xs font-semibold uppercase tracking-widest text-[#94A3B8]">Créée le</span>
          <span class="text-sm">{{ new Date(selectedRequest.created_at).toLocaleDateString('fr-FR') }}</span>
        </div>
      </div>
      <template #footer>
        <Button label="Fermer" text @click="showDetailDialog = false" />
      </template>
    </Dialog>
  </div>
</template>
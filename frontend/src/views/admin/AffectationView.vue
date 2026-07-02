<script setup>
import { onMounted, computed, ref } from 'vue'
import { useThemeStore } from '@/stores/theme'
import { useDepositRequestsStore } from '@/stores/depositRequests'
import { useUsersStore } from '@/stores/users'
import { useToast } from 'primevue/usetoast'
import Dialog from 'primevue/dialog'
import Select from 'primevue/select'
import Button from 'primevue/button'
import { Loader2 } from 'lucide-vue-next'

const theme        = useThemeStore()
const depositStore = useDepositRequestsStore()
const usersStore   = useUsersStore()
const toast        = useToast()

onMounted(async () => {
  await Promise.all([
    depositStore.fetchAll(),
    usersStore.fetchUsers(),
  ])
})

const pendingRequests = computed(() =>
  depositStore.requests.filter(r => r.status === 'pending')
)

const responsablesOptions = computed(() =>
  usersStore.users
    .filter(u => {
      const isResponsable = u.role === 'responsable_demande' && (u.status === 'active' || u.status === 'actif')
      const isNotAlreadyAssigned = !depositStore.selectedRequest || u.id !== depositStore.selectedRequest.assigned_manager_id
      return isResponsable && isNotAlreadyAssigned
    })
    .map(u => ({ label: `${u.first_name} ${u.last_name}`, value: u.id }))
)

const selectedManagerId = ref(null)

function openDialog(request) {
  selectedManagerId.value = null
  depositStore.openAssignDialog(request)
}

async function confirmerAffectation() {
  if (!selectedManagerId.value) return

  const success = await depositStore.assign(
    depositStore.selectedRequest.id,
    selectedManagerId.value
  )

  if (success) {
    toast.add({
      severity: 'success',
      summary: 'Affectée',
      detail: depositStore.message,
      life: 3000,
    })
  } else {
    toast.add({
      severity: 'error',
      summary: 'Erreur',
      detail: "L'affectation a échoué.",
      life: 3000,
    })
  }
}

function formatDate(dateStr) {
  return new Date(dateStr).toLocaleDateString('fr-FR', {
    day: '2-digit', month: 'short', year: 'numeric',
    hour: '2-digit', minute: '2-digit',
  })
}
</script>

<template>
  <div>
    <div class="mb-6">
      <h1 :class="['text-2xl font-bold', theme.isDark ? 'text-bordeaux-100' : 'text-bordeaux-900']">
        Affectations
      </h1>
      <p :class="['text-sm', theme.isDark ? 'text-bordeaux-400' : 'text-bordeaux-600']">
        Demandes en attente d'affectation à un responsable
      </p>
    </div>

    <!-- Loader -->
    <div v-if="depositStore.loading" class="flex items-center justify-center py-16">
      <Loader2 class="w-10 h-10 animate-spin text-bordeaux-600" />
      <span class="ml-3 text-sm">Chargement...</span>
    </div>

    <!-- État vide -->
    <div v-else-if="pendingRequests.length === 0"
      :class="['rounded-2xl p-12 text-center border',
        theme.isDark ? 'bg-white/5 border-white/10' : 'bg-white/30 border-white/50']">
      <i class="pi pi-check-circle text-4xl text-green-500 mb-3 block"></i>
      <p :class="['font-semibold', theme.isDark ? 'text-bordeaux-200' : 'text-bordeaux-800']">
        Aucune demande en attente
      </p>
      <p :class="['text-sm mt-1', theme.isDark ? 'text-bordeaux-400' : 'text-bordeaux-500']">
        Toutes les demandes ont été traitées.
      </p>
    </div>

    <!-- Liste -->
    <div v-else class="flex flex-col gap-4">
      <div
        v-for="request in pendingRequests" :key="request.id"
        :class="['rounded-2xl p-5 border flex items-center justify-between gap-4',
          theme.isDark ? 'bg-white/5 border-white/10' : 'bg-white border-[#ECECEC] shadow-sm']"
      >
        <div class="flex items-center gap-4 flex-1 min-w-0">
          <!-- Initiales -->
          <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-bordeaux-700 text-white text-xs font-bold shrink-0">
            {{ request.applicant?.first_name?.[0] ?? '' }}{{ request.applicant?.last_name?.[0] ?? '' }}
          </div>
          <div class="min-w-0">
            <p :class="['font-semibold truncate', theme.isDark ? 'text-bordeaux-200' : 'text-bordeaux-800']">
              {{ request.title }}
            </p>
            <p :class="['text-xs', theme.isDark ? 'text-bordeaux-400' : 'text-bordeaux-500']">
              {{ request.applicant?.first_name }} {{ request.applicant?.last_name }}
              · {{ formatDate(request.created_at) }}
            </p>
            <p v-if="request.assignedManager" :class="['text-xs mt-1', theme.isDark ? 'text-green-300' : 'text-green-700']">
              <i class="pi pi-check mr-1"></i> Affecté à : {{ request.assignedManager.first_name }} {{ request.assignedManager.last_name }}
            </p>
          </div>
        </div>

        <!-- Bouton affecter -->
        <button
          :class="['px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 shrink-0', request.assigned_manager_id ? 'bg-green-700 hover:bg-green-800 text-white' : 'bg-bordeaux-700 text-white hover:bg-bordeaux-800']"
          @click="openDialog(request)"
        >
          {{ request.assigned_manager_id ? 'Réaffecter' : 'Affecter' }}
        </button>
      </div>
    </div>

    <!-- Dialog affectation -->
    <Dialog
      v-model:visible="depositStore.showAssignDialog"
      header="Affecter à un responsable"
      :modal="true"
      :style="{ width: '420px' }"
      @hide="depositStore.showAssignDialog = false"
    >
      <div class="flex flex-col gap-4">
        <!-- Résumé demande -->
        <div v-if="depositStore.selectedRequest"
          :class="['p-3 rounded-xl', theme.isDark ? 'bg-white/5' : 'bg-bordeaux-50']">
          <p :class="['font-semibold text-sm', theme.isDark ? 'text-bordeaux-200' : 'text-bordeaux-800']">
            {{ depositStore.selectedRequest.title }}
          </p>
          <p :class="['text-xs mt-0.5', theme.isDark ? 'text-bordeaux-400' : 'text-bordeaux-500']">
            Soumis par {{ depositStore.selectedRequest.applicant?.first_name }}
            {{ depositStore.selectedRequest.applicant?.last_name }}
          </p>
        </div>

        <!-- Select responsable -->
        <div class="flex flex-col gap-1.5">
          <label class="text-sm font-semibold">Responsable</label>
          <Select
            v-model="selectedManagerId"
            :options="responsablesOptions"
            optionLabel="label"
            optionValue="value"
            placeholder="Choisir un responsable..."
            class="w-full"
          />
          <p v-if="responsablesOptions.length === 0" class="text-xs text-red-500 mt-1">
            Aucun responsable actif disponible.
          </p>
        </div>
      </div>

      <template #footer>
        <Button label="Annuler" text @click="depositStore.showAssignDialog = false" />
        <Button
          label="Confirmer l'affectation"
          :disabled="!selectedManagerId || depositStore.loading"
          :loading="depositStore.loading"
          @click="confirmerAffectation"
        />
      </template>
    </Dialog>
  </div>
</template>

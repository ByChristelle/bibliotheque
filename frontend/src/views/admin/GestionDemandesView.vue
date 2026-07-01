<script setup>
import { ref, computed, onMounted } from 'vue'
import { useThemeStore } from '@/stores/theme'
import Button from 'primevue/button'
import Tag from 'primevue/tag'
import Dialog from 'primevue/dialog'
import Textarea from 'primevue/textarea'
import Select from 'primevue/select'
import { useDepositRequestsStore } from '@/stores/depositRequests'
import { useUsersStore } from '@/stores/users' // On va créer ça si besoin, ou utiliser le store users
import { useToast } from 'primevue/usetoast'
import { Loader2 } from 'lucide-vue-next'

const theme = useThemeStore()
const depositStore = useDepositRequestsStore()
const usersStore = useUsersStore()
const toast = useToast()

const demandes = computed(() => depositStore.requests)
const responsables = computed(() => usersStore.users
  .filter(u => u.role === 'responsable_demande')
  .map(u => ({ label: `${u.first_name} ${u.last_name}`, value: u.id }))
)

const showJustifDialog = ref(false)
const showAffecterDialog = ref(false)
const selectedDemande = ref(null)
const justification = ref('')
const selectedResponsable = ref(null)
const actionType = ref('')

// Charger les données au montage
onMounted(async () => {
  await Promise.all([
    depositStore.fetchAll(),
    usersStore.fetchUsers(), // On suppose que le store users a une fonction fetchUsers
  ])
})

function ouvrirAction(d, action) {
  selectedDemande.value = d
  actionType.value = action
  justification.value = ''
  showJustifDialog.value = true
}

function ouvrirAffecter(d) {
  selectedDemande.value = d
  selectedResponsable.value = null
  showAffecterDialog.value = true
}

async function affecter() {
  if (!selectedResponsable.value || !selectedDemande.value) return
  const success = await depositStore.assign(selectedDemande.value.id, selectedResponsable.value)
  if (success) {
    toast.add({ severity: 'success', summary: 'Succès', detail: depositStore.message, life: 3000 })
    showAffecterDialog.value = false
  }
}

const statusLabel = {
  pending: 'en attente',
  approved_by_manager: 'validée par responsable',
  rejected_by_manager: 'refusée par responsable',
  published: 'publiée',
}
const statusSeverity = {
  pending: 'warn',
  approved_by_manager: 'success',
  rejected_by_manager: 'danger',
  published: 'success',
}
</script>

<template>
  <div>
    <div class="mb-6">
      <h1 :class="['text-2xl font-bold', theme.isDark ? 'text-bordeaux-100' : 'text-bordeaux-900']">
        Gestion des demandes
      </h1>
      <p :class="['text-sm', theme.isDark ? 'text-bordeaux-400' : 'text-bordeaux-600']">
        Examinez, publiez ou rejetez les demandes de dépôt
      </p>
    </div>

    <!-- Loader pendant le chargement -->
    <div class="flex items-center justify-center py-12" v-if="depositStore.loading">
      <Loader2 class="w-10 h-10 animate-spin text-bordeaux-600" />
      <span class="ml-3 text-sm">Chargement des demandes...</span>
    </div>

    <!-- Liste des demandes -->
    <div v-else class="flex flex-col gap-4">
      <div v-for="d in demandes" :key="d.id"
        :class="['rounded-2xl p-5 backdrop-blur-xl border',
          theme.isDark ? 'bg-white/5 border-white/10' : 'bg-white/30 border-white/50']">
        <div class="flex items-start justify-between gap-4">
          <div class="flex-1">
            <div class="flex items-center gap-2 mb-1 flex-wrap">
              <h3 :class="['font-semibold', theme.isDark ? 'text-bordeaux-200' : 'text-bordeaux-800']">{{ d.title }}</h3>
              <Tag :value="statusLabel[d.status] || d.status" :severity="statusSeverity[d.status]" />
            </div>
            <p :class="['text-xs mb-1', theme.isDark ? 'text-bordeaux-500' : 'text-bordeaux-400']">
              Déposé par : {{ d.applicant?.first_name }} {{ d.applicant?.last_name }} · {{ new Date(d.created_at).toLocaleDateString('fr-FR') }}
            </p>
            <p v-if="d.assigned_manager" :class="['text-xs', theme.isDark ? 'text-bordeaux-500' : 'text-bordeaux-400']">
              Responsable : {{ d.assigned_manager?.first_name }} {{ d.assigned_manager?.last_name }}
            </p>
            <div v-if="d.justification" :class="['mt-2 p-3 rounded-xl text-sm',
              theme.isDark ? 'bg-red-900/20 text-red-300 border border-red-800/30' : 'bg-red-50 text-red-700 border border-red-200']">
              <i class="pi pi-info-circle mr-1"></i> {{ d.justification }}
            </div>
          </div>

          <div class="flex flex-col gap-2 shrink-0">
            <Button v-if="d.status === 'approved_by_manager'" label="Publier" icon="pi pi-check" size="small" severity="success" @click="ouvrirAction(d, 'publier')" />
            <Button v-if="d.status === 'rejected_by_manager'" label="Invalider le refus" icon="pi pi-undo" size="small" @click="ouvrirAction(d, 'invalider')" />
            <Button v-if="d.status === 'rejected_by_manager'" label="Rejeter définitivement" icon="pi pi-times" size="small" severity="danger" outlined @click="ouvrirAction(d, 'rejeter')" />
            <Button label="Second avis" icon="pi pi-user-edit" size="small" outlined @click="ouvrirAffecter(d)" />
          </div>
        </div>
      </div>
    </div>

    <!-- Dialog action avec justification -->
    <Dialog v-model:visible="showJustifDialog" :header="actionType === 'publier' ? 'Confirmer la publication' : 'Justification'" :modal="true" :style="{ width: '480px' }">
      <p :class="['text-sm mb-3', theme.isDark ? 'text-white/70' : 'text-gray-600']">
        <span v-if="actionType === 'invalider'">Expliquez pourquoi vous passez outre le refus du responsable.</span>
        <span v-else-if="actionType === 'rejeter'">Confirmez le rejet définitif de cette demande.</span>
        <span v-else>Confirmez la publication de cette référence.</span>
      </p>
      <Textarea v-if="actionType !== 'publier'" v-model="justification" rows="4" class="w-full" placeholder="Justification..." />
      <template #footer>
        <Button label="Annuler" text @click="showJustifDialog = false" />
        <Button
          :label="actionType === 'publier' ? 'Publier' : actionType === 'rejeter' ? 'Rejeter' : 'Confirmer'"
          :severity="actionType === 'rejeter' ? 'danger' : 'success'"
          @click="showJustifDialog = false"
        />
      </template>
    </Dialog>

    <!-- Dialog affecter -->
    <Dialog v-model:visible="showAffecterDialog" header="Affecter à un responsable" :modal="true" :style="{ width: '420px' }">
      <p :class="['text-sm mb-3', theme.isDark ? 'text-white/70' : 'text-gray-600']">Choisissez un responsable pour obtenir un second avis.</p>
      <Select v-model="selectedResponsable" :options="responsables" optionLabel="label" optionValue="value" placeholder="Choisir un responsable" class="w-full" />
      <template #footer>
        <Button label="Annuler" text @click="showAffecterDialog = false" />
        <Button label="Affecter" :disabled="!selectedResponsable" @click="affecter" />
      </template>
    </Dialog>
  </div>
</template>

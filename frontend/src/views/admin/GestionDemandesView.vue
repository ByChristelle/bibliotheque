<script setup>
import { ref, computed, onMounted } from 'vue'
import { useThemeStore } from '@/stores/theme'
import Button from 'primevue/button'
import Tag from 'primevue/tag'
import Dialog from 'primevue/dialog'
import Textarea from 'primevue/textarea'
import Select from 'primevue/select'
import ConfirmDialog from 'primevue/confirmdialog'
import { useDepositRequestsStore } from '@/stores/depositRequests'
import { useUsersStore } from '@/stores/users'
import { useToast } from 'primevue/usetoast'
import { useConfirm } from 'primevue/useconfirm'
import { Loader2 } from 'lucide-vue-next'

const theme        = useThemeStore()
const depositStore = useDepositRequestsStore()
const usersStore   = useUsersStore()
const toast        = useToast()
const confirm      = useConfirm()

const searchQuery  = ref('')
const filterStatus = ref(null)
const currentPage  = ref(1)
const itemsPerPage = 8

const statusOptions = [
  { label: 'Tous les statuts',          value: null },
  { label: 'En attente',                value: 'pending' },
  { label: 'Validée par responsable',   value: 'approved_by_manager' },
  { label: 'Refusée par responsable',   value: 'rejected_by_manager' },
  { label: 'Publiée',                   value: 'published' },
  { label: 'Rejetée',                   value: 'rejected' },
]

const statusLabel = {
  pending:             'En attente',
  approved_by_manager: 'Validée · resp.',
  rejected_by_manager: 'Refusée · resp.',
  published:           'Publiée',
  rejected:            'Rejetée',
}
const statusSeverity = {
  pending:             'warn',
  approved_by_manager: 'success',
  rejected_by_manager: 'danger',
  published:           'success',
  rejected:            'danger',
}

const filtered = computed(() => {
  let list = depositStore.requests
  if (filterStatus.value) list = list.filter(d => d.status === filterStatus.value)
  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase()
    list = list.filter(d =>
      d.title?.toLowerCase().includes(q) ||
      `${d.applicant?.first_name} ${d.applicant?.last_name}`.toLowerCase().includes(q)
    )
  }
  return list
})

const totalPages = computed(() => Math.ceil(filtered.value.length / itemsPerPage) || 1)
const paginated  = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  return filtered.value.slice(start, start + itemsPerPage)
})

function resetPage() { currentPage.value = 1 }

const responsables = computed(() =>
  usersStore.users
    .filter(u => u.role === 'responsable_demande' && u.status === 'active')
    .map(u => ({ label: `${u.first_name} ${u.last_name}`, value: u.id }))
)

const showJustifDialog    = ref(false)
const showAffecterDialog  = ref(false)
const selectedDemande     = ref(null)
const justification       = ref('')
const selectedResponsable = ref(null)
const actionType          = ref('')

onMounted(async () => {
  await Promise.all([depositStore.fetchAll(), usersStore.fetchUsers()])
})

function ouvrirAction(d, action) {
  selectedDemande.value = d; actionType.value = action
  justification.value = ''; showJustifDialog.value = true
}

function ouvrirAffecter(d) {
  selectedDemande.value = d; selectedResponsable.value = null
  showAffecterDialog.value = true
}

async function affecter() {
  if (!selectedResponsable.value || !selectedDemande.value) return
  const ok = await depositStore.assign(selectedDemande.value.id, selectedResponsable.value)
  if (ok) { toast.add({ severity: 'success', summary: 'Affectée', detail: depositStore.message, life: 3000 }); showAffecterDialog.value = false }
}

function confirmerPublier(d) {
  confirm.require({
    message: `Publier "${d.title}" ? Elle sera visible par tous les utilisateurs.`,
    header: 'Confirmer la publication',
    acceptLabel: 'Publier',
    rejectLabel: 'Annuler',
    acceptClass: 'p-button-success',
    accept: async () => {
      const ok = await depositStore.publish(d.id)
      toast.add({ severity: ok ? 'success' : 'error', summary: ok ? 'Publiée !' : 'Erreur', detail: ok ? depositStore.message : 'La publication a échoué.', life: 3000 })
    }
  })
}

async function confirmerAction() {
  showJustifDialog.value = false
  toast.add({ severity: 'info', summary: 'Action effectuée', life: 2000 })
}
</script>

<template>
  <div>
    <ConfirmDialog />

    <div class="mb-6">
      <h1 :class="['text-2xl font-bold', theme.isDark ? 'text-bordeaux-100' : 'text-bordeaux-900']">Gestion des demandes</h1>
      <p :class="['text-sm', theme.isDark ? 'text-bordeaux-400' : 'text-bordeaux-600']">{{ filtered.length }} demande{{ filtered.length > 1 ? 's' : '' }}</p>
    </div>

    <!-- Filtres -->
    <div :class="['rounded-2xl p-4 mb-6 border flex flex-wrap gap-3', theme.isDark ? 'bg-white/5 border-white/10' : 'bg-white border-[#ECECEC] shadow-sm']">
      <input v-model="searchQuery" @input="resetPage" placeholder="Rechercher un titre, un auteur..."
        :class="['flex-1 min-w-48 px-4 py-2.5 rounded-xl text-sm outline-none transition-all',
          theme.isDark ? 'bg-white/10 border border-white/10 text-white placeholder-white/30 focus:border-white/30' : 'bg-[#F8F6F6] border border-[#ECECEC] text-[#2D2D2D] placeholder-[#aaa] focus:border-[#7A0026]']" />
      <Select v-model="filterStatus" @change="resetPage" :options="statusOptions" optionLabel="label" optionValue="value" placeholder="Tous les statuts" class="min-w-52" />
    </div>

    <!-- Loader -->
    <div v-if="depositStore.loading" class="flex items-center justify-center py-16">
      <Loader2 class="w-10 h-10 animate-spin text-bordeaux-600" />
      <span class="ml-3 text-sm text-[#94A3B8]">Chargement...</span>
    </div>

    <!-- Liste -->
    <div v-else-if="paginated.length" class="flex flex-col gap-3">
      <div v-for="d in paginated" :key="d.id"
        :class="['rounded-2xl p-5 border transition-all duration-200', theme.isDark ? 'bg-white/5 border-white/[0.07]' : 'bg-white border-[#ECECEC] shadow-sm hover:shadow-md']">
        <div class="flex items-start justify-between gap-4">
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-1.5 flex-wrap">
              <h3 class="font-semibold text-sm truncate" :class="theme.isDark ? 'text-white' : 'text-[#2D2D2D]'">{{ d.title }}</h3>
              <Tag :value="statusLabel[d.status] || d.status" :severity="statusSeverity[d.status]" />
            </div>
            <p class="text-xs text-[#94A3B8]">
              Déposé par <span class="font-medium" :class="theme.isDark ? 'text-white/70' : 'text-[#555]'">{{ d.applicant?.first_name }} {{ d.applicant?.last_name }}</span>
              · {{ new Date(d.created_at).toLocaleDateString('fr-FR', { day:'2-digit', month:'short', year:'numeric' }) }}
            </p>
            <p v-if="d.assigned_manager" class="text-xs text-[#94A3B8] mt-0.5">
              Responsable : <span class="font-medium" :class="theme.isDark ? 'text-white/60' : 'text-[#555]'">{{ d.assigned_manager?.first_name }} {{ d.assigned_manager?.last_name }}</span>
            </p>
            <div v-if="d.justification" class="mt-2 p-3 rounded-xl text-xs" :class="theme.isDark ? 'bg-red-900/20 text-red-300 border border-red-800/20' : 'bg-red-50 text-red-700 border border-red-200'">
              <i class="pi pi-info-circle mr-1"></i>{{ d.justification }}
            </div>
          </div>

          <div class="flex flex-col gap-1.5 shrink-0">
            <Button v-if="d.status === 'approved_by_manager'" label="Publier" icon="pi pi-check" size="small" severity="success" @click="confirmerPublier(d)" />
            <Button v-if="d.status === 'rejected_by_manager'" label="Invalider le refus" icon="pi pi-undo" size="small" @click="ouvrirAction(d, 'invalider')" />
            <Button v-if="d.status === 'rejected_by_manager'" label="Rejeter" icon="pi pi-times" size="small" severity="danger" outlined @click="ouvrirAction(d, 'rejeter')" />
            <Button v-if="!['published','rejected'].includes(d.status)" :label="d.assigned_manager_id ? 'Réaffecter' : 'Affecter'" icon="pi pi-user-edit" size="small" outlined @click="ouvrirAffecter(d)" />
          </div>
        </div>
      </div>
    </div>

    <!-- Vide -->
    <div v-else class="flex flex-col items-center justify-center py-16">
      <i class="pi pi-inbox text-5xl text-[#94A3B8] mb-3 block"></i>
      <p class="font-medium text-[#94A3B8]">Aucune demande trouvée</p>
    </div>

    <!-- Pagination -->
    <div v-if="totalPages > 1" class="flex items-center justify-between mt-6">
      <p class="text-xs text-[#94A3B8]">Page {{ currentPage }} / {{ totalPages }} · {{ filtered.length }} résultat{{ filtered.length > 1 ? 's' : '' }}</p>
      <div class="flex items-center gap-1">
        <button :disabled="currentPage === 1" @click="currentPage--"
          :class="['w-9 h-9 rounded-xl flex items-center justify-center text-sm transition-all disabled:opacity-30', theme.isDark ? 'text-white/70 hover:bg-white/10' : 'text-[#555] hover:bg-[#F8F6F6] border border-[#ECECEC]']">
          <i class="pi pi-chevron-left text-xs"></i>
        </button>
        <button v-for="p in totalPages" :key="p" @click="currentPage = p"
          :class="['w-9 h-9 rounded-xl flex items-center justify-center text-sm font-medium transition-all',
            p === currentPage ? 'bg-[#7A0026] text-white' : (theme.isDark ? 'text-white/60 hover:bg-white/10' : 'text-[#555] hover:bg-[#F8F6F6] border border-[#ECECEC]')]">
          {{ p }}
        </button>
        <button :disabled="currentPage === totalPages" @click="currentPage++"
          :class="['w-9 h-9 rounded-xl flex items-center justify-center text-sm transition-all disabled:opacity-30', theme.isDark ? 'text-white/70 hover:bg-white/10' : 'text-[#555] hover:bg-[#F8F6F6] border border-[#ECECEC]']">
          <i class="pi pi-chevron-right text-xs"></i>
        </button>
      </div>
    </div>

    <!-- Dialog justification -->
    <Dialog v-model:visible="showJustifDialog" :header="actionType === 'invalider' ? 'Invalider le refus' : 'Rejeter définitivement'" :modal="true" :style="{ width: '480px' }">
      <p class="text-sm mb-3" :class="theme.isDark ? 'text-white/60' : 'text-[#777]'">
        <span v-if="actionType === 'invalider'">Expliquez pourquoi vous passez outre le refus du responsable.</span>
        <span v-else>Confirmez le rejet définitif et fournissez une justification.</span>
      </p>
      <Textarea v-model="justification" rows="4" class="w-full" placeholder="Justification obligatoire..." />
      <template #footer>
        <Button label="Annuler" text @click="showJustifDialog = false" />
        <Button :label="actionType === 'rejeter' ? 'Rejeter définitivement' : 'Confirmer'" :severity="actionType === 'rejeter' ? 'danger' : 'success'" :disabled="!justification.trim()" @click="confirmerAction" />
      </template>
    </Dialog>

    <!-- Dialog affectation -->
    <Dialog v-model:visible="showAffecterDialog" header="Affecter à un responsable" :modal="true" :style="{ width: '420px' }">
      <p class="text-sm mb-3" :class="theme.isDark ? 'text-white/60' : 'text-[#777]'">Choisissez un responsable disponible.</p>
      <Select v-model="selectedResponsable" :options="responsables" optionLabel="label" optionValue="value" placeholder="Choisir un responsable" class="w-full" />
      <p v-if="!responsables.length" class="text-xs text-red-400 mt-2">Aucun responsable actif disponible.</p>
      <template #footer>
        <Button label="Annuler" text @click="showAffecterDialog = false" />
        <Button label="Affecter" :disabled="!selectedResponsable" @click="affecter" />
      </template>
    </Dialog>
  </div>
</template>

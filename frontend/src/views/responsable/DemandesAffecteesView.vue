<script setup>
import { computed, onMounted, ref } from 'vue'
import { useThemeStore } from '@/stores/theme'
import Button from 'primevue/button'
import Textarea from 'primevue/textarea'
import Dialog from 'primevue/dialog'
import { useDepositRequestsStore } from '@/stores/depositRequests'
import { Loader2 } from 'lucide-vue-next'

const depositStore = useDepositRequestsStore()
const theme = useThemeStore()

const demandes = computed(() => depositStore.assignedRequests)
onMounted(() => {
  depositStore.fetchMyAssigned()
})

// Pour le modal de détail
const showDetailDialog = ref(false)
const selectedDemande = ref(null)

// Pour le modal de refus
const showRefuserDialog = ref(false)
const justification = ref('')

function ouvrirDetail(d) {
  selectedDemande.value = d
  showDetailDialog.value = true
}

function ouvrirRefus(d) {
  selectedDemande.value = d
  justification.value = ''
  showRefuserDialog.value = true
}

function valider(d) {
  d.status = 'validée'
  // TODO: appel API
}

function confirmerRefus() {
  if (selectedDemande.value) {
    selectedDemande.value.status = 'refusée'
    // TODO: appel API avec justification.value
  }
  showRefuserDialog.value = false
}

// Fonction pour afficher le statut en français
function getStatusLabel(status) {
  const labels = {
    pending: 'En attente',
    assigned: 'Affectée',
    in_review: 'En revue',
    approved: 'Approuvée',
    rejected: 'Rejetée',
    validée: 'Validée',
    refusée: 'Refusée'
  }
  return labels[status] || status
}

// Fonction pour la couleur du badge statut
function getStatusClass(status) {
  switch (status) {
    case 'pending':
    case 'En attente':
      return 'bg-yellow-100 text-yellow-700'
    case 'assigned':
    case 'Affectée':
      return 'bg-blue-100 text-blue-700'
    case 'in_review':
    case 'En revue':
      return 'bg-purple-100 text-purple-700'
    case 'approved':
    case 'validée':
    case 'Approuvée':
    case 'Validée':
      return 'bg-green-100 text-green-700'
    case 'rejected':
    case 'refusée':
    case 'Rejetée':
    case 'Refusée':
      return 'bg-red-100 text-red-700'
    default:
      return 'bg-gray-100 text-gray-700'
  }
}

// Fonction pour afficher le type de document en français
function getDocumentTypeLabel(type) {
  const labels = {
    livre: 'Livre',
    memoire: 'Mémoire',
    these: 'Thèse',
    article: 'Article',
    revue: 'Revue',
    rapport: 'Rapport',
    guide: 'Guide',
    autre: 'Autre'
  }
  return labels[type] || type
}

// Fonction pour afficher la langue en français
function getLanguageLabel(lang) {
  const labels = {
    fr: 'Français',
    en: 'Anglais',
    autre: 'Autre'
  }
  return labels[lang] || lang
}
</script>

<template>
  <div>
    <div class="mb-6">
      <h1 :class="['text-2xl font-bold', theme.isDark ? 'text-bordeaux-100' : 'text-bordeaux-900']">
        Demandes affectées
      </h1>
      <p :class="['text-sm', theme.isDark ? 'text-bordeaux-400' : 'text-bordeaux-600']">
        Examinez et traitez les demandes qui vous sont attribuées
      </p>
    </div>

    <!-- Loader -->
    <div class="flex items-center justify-center z-50 py-12" v-if="depositStore.loading">
      <Loader2 class="w-10 h-10 text-primary-500 animate-spin" />
      <span class="ml-3 text-sm">Chargement des demandes...</span>
    </div>

    <!-- Liste des demandes -->
    <div v-else class="flex flex-col gap-4">
      <div v-for="d in demandes" :key="d.id"
        :class="['rounded-2xl p-5 backdrop-blur-xl border',
          theme.isDark ? 'bg-white/5 border-white/10' : 'bg-white/30 border-white/50']">
        <div class="flex items-start justify-between gap-4">
          <div class="flex-1">
            <div class="flex items-center gap-2 mb-1">
              <h3 :class="['font-semibold', theme.isDark ? 'text-bordeaux-200' : 'text-bordeaux-800']">{{ d.title }}</h3>
              <span :class="['text-xs px-2 py-0.5 rounded-full font-medium', getStatusClass(d.status)]">{{ getStatusLabel(d.status) }}</span>
            </div>
            <p :class="['text-xs', theme.isDark ? 'text-bordeaux-500' : 'text-bordeaux-400']">
              {{ d.applicant ? `${d.applicant.first_name} ${d.applicant.last_name}` : 'Auteur inconnu' }} · {{ new Date(d.created_at).toLocaleDateString('fr-FR') }}
            </p>
          </div>

          <div class="flex gap-2 shrink-0">
            <button title="Voir le détail" @click="ouvrirDetail(d)"
              class="w-10 h-10 rounded-lg flex items-center justify-center text-[#94A3B8] hover:text-[#60a5fa] hover:bg-[#60a5fa]/10 transition-all duration-200">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
              </svg>
            </button>

            <div v-if="d.status === 'pending' || d.status === 'En attente' || d.status === 'assigned' || d.status === 'Affectée'" class="flex gap-2">
              <Button label="Valider" icon="pi pi-check" size="small" severity="success" @click="valider(d)" />
              <Button label="Refuser" icon="pi pi-times" size="small" severity="danger" outlined @click="ouvrirRefus(d)" />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de détail COMPLET -->
    <Dialog v-model:visible="showDetailDialog" header="Détail de la demande" :modal="true" :style="{ width: '700px' }" :scrollable="true">
      <div v-if="selectedDemande" class="flex flex-col gap-5">
        <!-- En-tête -->
        <div class="flex items-center gap-3 border-b pb-4" :class="theme.isDark ? 'border-white/10' : 'border-gray-200'">
          <span :class="['font-semibold text-xl', theme.isDark ? 'text-bordeaux-200' : 'text-bordeaux-800']">{{ selectedDemande.title }}</span>
          <span :class="['text-xs px-2 py-0.5 rounded-full font-medium', getStatusClass(selectedDemande.status)]">
            {{ getStatusLabel(selectedDemande.status) }}
          </span>
        </div>

        <!-- Auteur de la demande -->
        <div class="flex flex-col gap-1">
          <span class="text-xs font-semibold uppercase tracking-widest text-[#94A3B8]">Auteur de la demande</span>
          <span class="text-sm">
            {{ selectedDemande.applicant 
              ? `${selectedDemande.applicant.first_name} ${selectedDemande.applicant.last_name} (${selectedDemande.applicant.email})` 
              : 'Inconnu' }}
          </span>
        </div>

        <!-- Si on a un brouillon de référence -->
        <div v-if="selectedDemande.reference_brouillon" class="space-y-4 border-t pt-4" :class="theme.isDark ? 'border-white/10' : 'border-gray-200'">
          <h3 :class="['text-sm font-semibold', theme.isDark ? 'text-bordeaux-300' : 'text-bordeaux-700']">Informations de la référence</h3>

          <div class="grid grid-cols-2 gap-4">
            <div class="flex flex-col gap-1">
              <span class="text-xs font-semibold uppercase tracking-widest text-[#94A3B8]">Sous-titre</span>
              <span class="text-sm">{{ selectedDemande.reference_brouillon.subtitle || '—' }}</span>
            </div>
            <div class="flex flex-col gap-1">
              <span class="text-xs font-semibold uppercase tracking-widest text-[#94A3B8]">Auteur(s)</span>
              <span class="text-sm">{{ selectedDemande.reference_brouillon.authors }}</span>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div class="flex flex-col gap-1">
              <span class="text-xs font-semibold uppercase tracking-widest text-[#94A3B8]">Catégorie</span>
              <span class="text-sm">{{ selectedDemande.reference_brouillon.category?.name || '—' }}</span>
            </div>
            <div class="flex flex-col gap-1">
              <span class="text-xs font-semibold uppercase tracking-widest text-[#94A3B8]">Type de document</span>
              <span class="text-sm">{{ getDocumentTypeLabel(selectedDemande.reference_brouillon.document_type) }}</span>
            </div>
          </div>

          <div class="grid grid-cols-3 gap-4">
            <div class="flex flex-col gap-1">
              <span class="text-xs font-semibold uppercase tracking-widest text-[#94A3B8]">Langue</span>
              <span class="text-sm">{{ getLanguageLabel(selectedDemande.reference_brouillon.language) }}</span>
            </div>
            <div class="flex flex-col gap-1">
              <span class="text-xs font-semibold uppercase tracking-widest text-[#94A3B8]">Année de publication</span>
              <span class="text-sm">{{ selectedDemande.reference_brouillon.publication_year || '—' }}</span>
            </div>
            <div class="flex flex-col gap-1">
              <span class="text-xs font-semibold uppercase tracking-widest text-[#94A3B8]">Pages</span>
              <span class="text-sm">{{ selectedDemande.reference_brouillon.pages || '—' }}</span>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div class="flex flex-col gap-1">
              <span class="text-xs font-semibold uppercase tracking-widest text-[#94A3B8]">Éditeur</span>
              <span class="text-sm">{{ selectedDemande.reference_brouillon.publisher || '—' }}</span>
            </div>
            <div class="flex flex-col gap-1">
              <span class="text-xs font-semibold uppercase tracking-widest text-[#94A3B8]">ISBN</span>
              <span class="text-sm">{{ selectedDemande.reference_brouillon.isbn || '—' }}</span>
            </div>
          </div>

          <div class="flex flex-col gap-1">
            <span class="text-xs font-semibold uppercase tracking-widest text-[#94A3B8]">Mots-clés</span>
            <span class="text-sm">{{ selectedDemande.reference_brouillon.keywords || '—' }}</span>
          </div>

          <div class="flex flex-col gap-1">
            <span class="text-xs font-semibold uppercase tracking-widest text-[#94A3B8]">Résumé</span>
            <span class="text-sm">{{ selectedDemande.reference_brouillon.abstract || '—' }}</span>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div v-if="selectedDemande.reference_brouillon.cover_image" class="flex flex-col gap-1">
              <span class="text-xs font-semibold uppercase tracking-widest text-[#94A3B8]">Couverture</span>
              <img :src="`http://localhost:8000/storage/${selectedDemande.reference_brouillon.cover_image}`" alt="Couverture" class="w-32 h-44 object-cover rounded-lg" />
            </div>
            <div v-if="selectedDemande.reference_brouillon.file_path" class="flex flex-col gap-1">
              <span class="text-xs font-semibold uppercase tracking-widest text-[#94A3B8]">Fichier PDF</span>
              <a :href="`http://localhost:8000/storage/${selectedDemande.reference_brouillon.file_path}`" target="_blank" class="text-sm text-blue-600 hover:underline">
                Voir / Télécharger le fichier
              </a>
            </div>
          </div>
        </div>

        <!-- Si pas de brouillon -->
        <div v-else class="text-center py-4 text-gray-500">
          Aucune information de référence disponible
        </div>

        <!-- Date de création -->
        <div class="border-t pt-4" :class="theme.isDark ? 'border-white/10' : 'border-gray-200'">
          <div class="flex flex-col gap-1">
            <span class="text-xs font-semibold uppercase tracking-widest text-[#94A3B8]">Date de création</span>
            <span class="text-sm">{{ new Date(selectedDemande.created_at).toLocaleDateString('fr-FR', { 
              weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' 
            }) }}</span>
          </div>
        </div>
      </div>
      <template #footer>
        <Button label="Fermer" text @click="showDetailDialog = false" />
      </template>
    </Dialog>

    <!-- Modal refus -->
    <Dialog
      v-model:visible="showRefuserDialog"
      header="Motif du refus"
      :modal="true"
      :style="{ width: '480px' }"
    >
      <p class="text-sm text-gray-600 mb-3">
        Veuillez renseigner obligatoirement une justification détaillée.
      </p>
      <Textarea v-model="justification" rows="4" class="w-full" placeholder="Expliquez les raisons du refus..." />
      <template #footer>
        <Button label="Annuler" text @click="showRefuserDialog = false" />
        <Button label="Confirmer le refus" severity="danger" :disabled="!justification.trim()" @click="confirmerRefus" />
      </template>
    </Dialog>
  </div>
</template>
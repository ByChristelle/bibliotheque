<script setup>
import { computed, onMounted, ref } from 'vue'
import { useThemeStore } from '@/stores/theme'
import Button from 'primevue/button'
import Textarea from 'primevue/textarea'
import Dialog from 'primevue/dialog'
import { useDepositRequestsStore } from '@/stores/depositRequests'
import { Loader2 } from 'lucide-vue-next'
import { useConfirm } from 'primevue/useconfirm'
// import ConfirmDialog from 'primevue/confirmdialog'

const confirm = useConfirm()




const depositStore = useDepositRequestsStore()
const theme = useThemeStore()

const demandes = computed(() => depositStore.assignedRequests)
onMounted(() => { depositStore.fetchMyAssigned() })

const showDetailDialog  = ref(false)
const showRefuserDialog = ref(false)
const selectedDemande   = ref(null)
const justification     = ref('')

function ouvrirDetail(d) { selectedDemande.value = d; showDetailDialog.value = true }
function ouvrirRefus(d)  { selectedDemande.value = d; justification.value = ''; showRefuserDialog.value = true }

async function valider(d) {
  confirm.require({
    message: `Êtes-vous sûr de vouloir valider "${d.title}" ?`,
    header: 'Confirmation',
    icon: 'pi pi-check-circle',
    acceptLabel: 'Oui, valider',
    rejectLabel: 'Annuler',
    acceptClass: 'p-button-success',
    accept: async () => {
      const success = await depositStore.approve(d.id)
      if (success) toast.add({ severity: 'success', summary: 'Validée', detail: depositStore.message, life: 3000 })
    }
  })
}



async function confirmerRefus() {
  if (!selectedDemande.value) return
  const success = await depositStore.reject(selectedDemande.value.id, justification.value)
  if (success) {
    toast.add({ severity: 'success', summary: 'Refusée', detail: depositStore.message, life: 3000 })
    showRefuserDialog.value = false
  }
}


const statusLabel = { pending:'En attente', assigned:'Affectée', in_review:'En revue', approved:'Approuvée', rejected:'Rejetée', validée:'Validée', refusée:'Refusée' }
const statusClass = { pending:'bg-yellow-100 text-yellow-700', assigned:'bg-blue-100 text-blue-700', in_review:'bg-purple-100 text-purple-700', approved:'bg-green-100 text-green-700', validée:'bg-green-100 text-green-700', rejected:'bg-red-100 text-red-700', refusée:'bg-red-100 text-red-700' }
const docTypeLabel = { livre:'Livre', memoire:'Mémoire', these:'Thèse', article:'Article', revue:'Revue', rapport:'Rapport', guide:'Guide', autre:'Autre' }
const langLabel = { fr:'Français', en:'Anglais', autre:'Autre' }

function sl(s)  { return statusLabel[s]  || s }
function sc(s)  { return statusClass[s]  || 'bg-gray-100 text-gray-700' }
function dtl(t) { return docTypeLabel[t] || t }
function ll(l)  { return langLabel[l]    || l }
</script>

<template>
  <div>
    <div class="mb-6">
      <h1 :class="['text-2xl font-bold', theme.isDark ? 'text-bordeaux-100' : 'text-bordeaux-900']">Demandes affectées</h1>
      <p :class="['text-sm', theme.isDark ? 'text-bordeaux-400' : 'text-bordeaux-600']">Examinez et traitez les demandes qui vous sont attribuées</p>
    </div>

    <div class="flex items-center justify-center py-12" v-if="depositStore.loading">
      <Loader2 class="w-10 h-10 animate-spin text-bordeaux-600" />
      <span class="ml-3 text-sm">Chargement...</span>
    </div>

    <div v-else class="flex flex-col gap-4">
      <div v-for="d in demandes" :key="d.id"
        :class="['rounded-2xl p-5 border', theme.isDark ? 'bg-white/5 border-white/10' : 'bg-white border-[#ECECEC] shadow-sm']">
        <div class="flex items-start justify-between gap-4">
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-1 flex-wrap">
              <h3 :class="['font-semibold truncate', theme.isDark ? 'text-bordeaux-200' : 'text-bordeaux-800']">{{ d.title }}</h3>
              <span :class="['text-xs px-2 py-0.5 rounded-full font-medium shrink-0', sc(d.status)]">{{ sl(d.status) }}</span>
            </div>
            <p :class="['text-xs', theme.isDark ? 'text-bordeaux-500' : 'text-bordeaux-400']">
              {{ d.applicant ? `${d.applicant.first_name} ${d.applicant.last_name}` : 'Auteur inconnu' }}
              · {{ new Date(d.created_at).toLocaleDateString('fr-FR') }}
            </p>
          </div>
          <div class="flex items-center gap-2 shrink-0 flex-wrap justify-end">
            <button @click="ouvrirDetail(d)"
              class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 text-[#60a5fa] bg-[#60a5fa]/10 hover:bg-[#60a5fa]/20">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607ZM10.5 7.5v6m3-3h-6" />
              </svg>
              Analyser
            </button>
            <div v-if="['pending','assigned'].includes(d.status)" class="flex gap-2">
              <Button label="Valider" icon="pi pi-check" size="small" severity="success" @click="valider(d)" />
              <Button label="Refuser" icon="pi pi-times" size="small" severity="danger" outlined @click="ouvrirRefus(d)" />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ── Dialog Analyser ── -->
    <Dialog v-model:visible="showDetailDialog" header="Analyse de la demande" :modal="true" :style="{ width: '680px' }" :scrollable="true">
      <div v-if="selectedDemande" class="flex flex-col gap-6 py-2">

        <!-- Bandeau titre -->
        <div class="flex items-start justify-between gap-3 p-4 rounded-xl" :class="theme.isDark ? 'bg-white/5' : 'bg-slate-50'">
          <div>
            <p class="text-xs font-semibold uppercase tracking-widest text-[#94A3B8] mb-1">Titre de la demande</p>
            <h3 class="text-base font-bold" :class="theme.isDark ? 'text-white' : 'text-[#2D2D2D]'">{{ selectedDemande.title }}</h3>
            <p class="text-xs mt-1" :class="theme.isDark ? 'text-white/40' : 'text-[#94A3B8]'">
              Soumis par
              <span class="font-medium" :class="theme.isDark ? 'text-white/70' : 'text-[#2D2D2D]'">
                {{ selectedDemande.applicant ? `${selectedDemande.applicant.first_name} ${selectedDemande.applicant.last_name}` : 'Inconnu' }}
              </span>
              · {{ new Date(selectedDemande.created_at).toLocaleDateString('fr-FR', { day:'2-digit', month:'long', year:'numeric' }) }}
            </p>
          </div>
          <span :class="['text-xs px-3 py-1.5 rounded-full font-semibold shrink-0', sc(selectedDemande.status)]">{{ sl(selectedDemande.status) }}</span>
        </div>

        <div v-if="selectedDemande.reference_brouillon" class="flex flex-col gap-5">

          <!-- Identification -->
          <div>
            <div class="flex items-center gap-2 mb-3">
              <span class="text-xs font-bold uppercase tracking-widest text-[#94A3B8]">Identification</span>
              <span class="flex-1 h-px" :class="theme.isDark ? 'bg-white/10' : 'bg-[#ECECEC]'"></span>
            </div>
            <div class="grid grid-cols-2 gap-3">
              <div v-for="item in [
                { label: 'Sous-titre', val: selectedDemande.reference_brouillon.subtitle },
                { label: 'Auteur(s)', val: selectedDemande.reference_brouillon.authors },
                { label: 'Catégorie', val: selectedDemande.reference_brouillon.category?.name },
                { label: 'Type de document', val: dtl(selectedDemande.reference_brouillon.document_type) },
              ]" :key="item.label"
                class="p-3 rounded-xl" :class="theme.isDark ? 'bg-white/5' : 'bg-white border border-[#ECECEC]'">
                <p class="text-xs text-[#94A3B8] mb-0.5">{{ item.label }}</p>
                <p class="text-sm font-medium" :class="theme.isDark ? 'text-white' : 'text-[#2D2D2D]'">{{ item.val || '—' }}</p>
              </div>
            </div>
          </div>

          <!-- Détails bibliographiques -->
          <div>
            <div class="flex items-center gap-2 mb-3">
              <span class="text-xs font-bold uppercase tracking-widest text-[#94A3B8]">Détails bibliographiques</span>
              <span class="flex-1 h-px" :class="theme.isDark ? 'bg-white/10' : 'bg-[#ECECEC]'"></span>
            </div>
            <div class="grid grid-cols-3 gap-3">
              <div v-for="item in [
                { label: 'Langue', val: ll(selectedDemande.reference_brouillon.language) },
                { label: 'Année', val: selectedDemande.reference_brouillon.publication_year },
                { label: 'Pages', val: selectedDemande.reference_brouillon.pages },
                { label: 'Éditeur', val: selectedDemande.reference_brouillon.publisher },
                { label: 'ISBN', val: selectedDemande.reference_brouillon.isbn, span: 2 },
              ]" :key="item.label"
                class="p-3 rounded-xl" :class="[theme.isDark ? 'bg-white/5' : 'bg-white border border-[#ECECEC]', item.span ? `col-span-${item.span}` : '']">
                <p class="text-xs text-[#94A3B8] mb-0.5">{{ item.label }}</p>
                <p class="text-sm font-medium" :class="theme.isDark ? 'text-white' : 'text-[#2D2D2D]'">{{ item.val || '—' }}</p>
              </div>
            </div>
          </div>

          <!-- Contenu -->
          <div>
            <div class="flex items-center gap-2 mb-3">
              <span class="text-xs font-bold uppercase tracking-widest text-[#94A3B8]">Contenu</span>
              <span class="flex-1 h-px" :class="theme.isDark ? 'bg-white/10' : 'bg-[#ECECEC]'"></span>
            </div>
            <div class="flex flex-col gap-3">
              <div class="p-3 rounded-xl" :class="theme.isDark ? 'bg-white/5' : 'bg-white border border-[#ECECEC]'">
                <p class="text-xs text-[#94A3B8] mb-1">Mots-clés</p>
                <div v-if="selectedDemande.reference_brouillon.keywords" class="flex flex-wrap gap-1.5">
                  <span v-for="kw in selectedDemande.reference_brouillon.keywords.split(',')" :key="kw"
                    class="text-xs px-2.5 py-1 rounded-full"
                    :class="theme.isDark ? 'bg-white/10 text-white/70' : 'bg-[#F8F6F6] text-[#555] border border-[#ECECEC]'">
                    {{ kw.trim() }}
                  </span>
                </div>
                <p v-else class="text-sm" :class="theme.isDark ? 'text-white/40' : 'text-[#94A3B8]'">—</p>
              </div>
              <div class="p-3 rounded-xl" :class="theme.isDark ? 'bg-white/5' : 'bg-white border border-[#ECECEC]'">
                <p class="text-xs text-[#94A3B8] mb-1">Résumé</p>
                <p class="text-sm leading-relaxed" :class="theme.isDark ? 'text-white/80' : 'text-[#2D2D2D]'">
                  {{ selectedDemande.reference_brouillon.abstract || '—' }}
                </p>
              </div>
            </div>
          </div>

          <!-- Fichiers -->
          <div v-if="selectedDemande.reference_brouillon.cover_image || selectedDemande.reference_brouillon.file_path">
            <div class="flex items-center gap-2 mb-3">
              <span class="text-xs font-bold uppercase tracking-widest text-[#94A3B8]">Fichiers joints</span>
              <span class="flex-1 h-px" :class="theme.isDark ? 'bg-white/10' : 'bg-[#ECECEC]'"></span>
            </div>
            <div class="flex gap-4 flex-wrap items-start">
              <div v-if="selectedDemande.reference_brouillon.cover_image" class="flex flex-col items-center gap-2">
                <img :src="`http://localhost:8000/storage/${selectedDemande.reference_brouillon.cover_image}`"
                  alt="Couverture" class="w-24 h-36 object-cover rounded-xl shadow" />
                <p class="text-xs text-[#94A3B8]">Couverture</p>
              </div>
              <div v-if="selectedDemande.reference_brouillon.file_path">
                <a :href="`http://localhost:8000/storage/${selectedDemande.reference_brouillon.file_path}`"
                  target="_blank"
                  class="flex items-center gap-2 px-4 py-3 rounded-xl text-sm font-medium no-underline transition-all duration-200"
                  :class="theme.isDark ? 'bg-white/10 text-white hover:bg-white/15' : 'bg-[#F8F6F6] text-[#2D2D2D] border border-[#ECECEC] hover:bg-[#ECECEC]'">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-4 h-4 text-red-500">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                  </svg>
                  Voir le document PDF
                </a>
              </div>
            </div>
          </div>

        </div>
        <div v-else class="text-center py-8 text-sm text-[#94A3B8]">Aucune information de référence disponible.</div>

      </div>
      <template #footer>
        <Button label="Fermer" text @click="showDetailDialog = false" />
      </template>
    </Dialog>

    <!-- ── Dialog Refus ── -->
    <Dialog v-model:visible="showRefuserDialog" header="Motif du refus" :modal="true" :style="{ width: '480px' }">
      <p class="text-sm mb-3" :class="theme.isDark ? 'text-white/60' : 'text-[#777]'">
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

<script setup>
import { ref } from 'vue'
import { useThemeStore } from '@/stores/theme'
import Button from 'primevue/button'
import Textarea from 'primevue/textarea'
import Dialog from 'primevue/dialog'

const theme = useThemeStore()

const demandes = ref([
  { id: 1, title: 'Introduction aux algorithmes', auteur: 'Jean Kouassi', category: 'Sciences', date: '17/06/2026', status: 'en attente' },
  { id: 2, title: 'Droit des contrats', auteur: 'Marie Tall', category: 'Droit', date: '16/06/2026', status: 'en attente' },
  { id: 3, title: 'Histoire contemporaine', auteur: 'Paul Diallo', category: 'Histoire', date: '15/06/2026', status: 'en attente' },
])

const showRefuserDialog = ref(false)
const selectedDemande = ref(null)
const justification = ref('')

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

const statusClass = {
  'en attente': 'bg-yellow-100 text-yellow-700',
  'validée': 'bg-green-100 text-green-700',
  'refusée': 'bg-red-100 text-red-700',
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

    <div class="flex flex-col gap-4">
      <div v-for="d in demandes" :key="d.id"
        :class="['rounded-2xl p-5 backdrop-blur-xl border',
          theme.isDark ? 'bg-white/5 border-white/10' : 'bg-white/30 border-white/50']">
        <div class="flex items-start justify-between gap-4">
          <div class="flex-1">
            <div class="flex items-center gap-2 mb-1">
              <h3 :class="['font-semibold', theme.isDark ? 'text-bordeaux-200' : 'text-bordeaux-800']">{{ d.title }}</h3>
              <span :class="['text-xs px-2 py-0.5 rounded-full font-medium', statusClass[d.status]]">{{ d.status }}</span>
            </div>
            <p :class="['text-xs', theme.isDark ? 'text-bordeaux-500' : 'text-bordeaux-400']">
              {{ d.auteur }} · {{ d.category }} · {{ d.date }}
            </p>
          </div>

          <div v-if="d.status === 'en attente'" class="flex gap-2 shrink-0">
            <Button label="Valider" icon="pi pi-check" size="small" severity="success" @click="valider(d)" />
            <Button label="Refuser" icon="pi pi-times" size="small" severity="danger" outlined @click="ouvrirRefus(d)" />
          </div>
        </div>
      </div>
    </div>

    <!-- Dialog refus -->
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

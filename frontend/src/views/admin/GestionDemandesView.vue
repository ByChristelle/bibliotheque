<script setup>
import { ref } from 'vue'
import { useThemeStore } from '@/stores/theme'
import Button from 'primevue/button'
import Tag from 'primevue/tag'
import Dialog from 'primevue/dialog'
import Textarea from 'primevue/textarea'
import Select from 'primevue/select'

const theme = useThemeStore()

const demandes = ref([
  { id: 1, title: 'Introduction aux algorithmes', auteur: 'Jean Kouassi', responsable: 'Marie Tall', status: 'validée par responsable', date: '17/06/2026', justification: null },
  { id: 2, title: 'Droit des contrats', auteur: 'Paul Diallo', responsable: 'Marie Tall', status: 'refusée par responsable', date: '16/06/2026', justification: 'Format non conforme.' },
  { id: 3, title: 'Histoire contemporaine', auteur: 'Sophie Kone', responsable: null, status: 'en attente', date: '15/06/2026', justification: null },
])

const responsables = [
  { label: 'Marie Tall', value: 1 },
  { label: 'Ahmed Bah', value: 2 },
]

const showJustifDialog = ref(false)
const showAffecterDialog = ref(false)
const selectedDemande = ref(null)
const justification = ref('')
const selectedResponsable = ref(null)
const actionType = ref('')

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

const statusSeverity = {
  'en attente': 'warn',
  'validée par responsable': 'success',
  'refusée par responsable': 'danger',
  'publiée': 'success',
  'rejetée': 'danger',
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

    <div class="flex flex-col gap-4">
      <div v-for="d in demandes" :key="d.id"
        :class="['rounded-2xl p-5 backdrop-blur-xl border',
          theme.isDark ? 'bg-white/5 border-white/10' : 'bg-white/30 border-white/50']">
        <div class="flex items-start justify-between gap-4">
          <div class="flex-1">
            <div class="flex items-center gap-2 mb-1 flex-wrap">
              <h3 :class="['font-semibold', theme.isDark ? 'text-bordeaux-200' : 'text-bordeaux-800']">{{ d.title }}</h3>
              <Tag :value="d.status" :severity="statusSeverity[d.status]" />
            </div>
            <p :class="['text-xs mb-1', theme.isDark ? 'text-bordeaux-500' : 'text-bordeaux-400']">
              Déposé par : {{ d.auteur }} · {{ d.date }}
            </p>
            <p v-if="d.responsable" :class="['text-xs', theme.isDark ? 'text-bordeaux-500' : 'text-bordeaux-400']">
              Responsable : {{ d.responsable }}
            </p>
            <div v-if="d.justification" :class="['mt-2 p-3 rounded-xl text-sm',
              theme.isDark ? 'bg-red-900/20 text-red-300 border border-red-800/30' : 'bg-red-50 text-red-700 border border-red-200']">
              <i class="pi pi-info-circle mr-1"></i> {{ d.justification }}
            </div>
          </div>

          <div class="flex flex-col gap-2 shrink-0">
            <Button v-if="d.status === 'validée par responsable'" label="Publier" icon="pi pi-check" size="small" severity="success" @click="ouvrirAction(d, 'publier')" />
            <Button v-if="d.status === 'refusée par responsable'" label="Invalider le refus" icon="pi pi-undo" size="small" @click="ouvrirAction(d, 'invalider')" />
            <Button v-if="d.status === 'refusée par responsable'" label="Rejeter définitivement" icon="pi pi-times" size="small" severity="danger" outlined @click="ouvrirAction(d, 'rejeter')" />
            <Button label="Second avis" icon="pi pi-user-edit" size="small" outlined @click="ouvrirAffecter(d)" />
          </div>
        </div>
      </div>
    </div>

    <!-- Dialog action avec justification -->
    <Dialog v-model:visible="showJustifDialog" :header="actionType === 'publier' ? 'Confirmer la publication' : 'Justification'" :modal="true" :style="{ width: '480px' }">
      <p class="text-sm text-gray-600 mb-3">
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
      <p class="text-sm text-gray-600 mb-3">Choisissez un responsable pour obtenir un second avis.</p>
      <Select v-model="selectedResponsable" :options="responsables" optionLabel="label" optionValue="value" placeholder="Choisir un responsable" class="w-full" />
      <template #footer>
        <Button label="Annuler" text @click="showAffecterDialog = false" />
        <Button label="Affecter" :disabled="!selectedResponsable" @click="showAffecterDialog = false" />
      </template>
    </Dialog>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useThemeStore } from '@/stores/theme'
import { useRouter } from 'vue-router'
import Button from 'primevue/button'
import Select from 'primevue/select'

const theme = useThemeStore()
const router = useRouter()

const filterStatus = ref(null)
const statusOptions = [
  { label: 'Tous les statuts', value: null },
  { label: 'En attente', value: 'en attente' },
  { label: 'Validée', value: 'validée' },
  { label: 'Refusée', value: 'refusée' },
  { label: 'Publiée', value: 'publiée' },
]

const demandes = ref([
  { id: 1, title: 'Introduction aux algorithmes', date: '12/06/2026', status: 'validée', justification: null },
  { id: 2, title: 'Droit constitutionnel', date: '17/06/2026', status: 'en attente', justification: null },
  { id: 3, title: 'Économie politique africaine', date: '05/05/2026', status: 'refusée', justification: "Le document ne respecte pas le format attendu." },
  { id: 4, title: 'Physique quantique appliquée', date: '20/04/2026', status: 'publiée', justification: null },
])

const statusClass = {
  'validée': 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
  'en attente': 'bg-yellow-100 text-yellow-700',
  'refusée': 'bg-red-100 text-red-700',
  'publiée': 'bg-bordeaux-100 text-bordeaux-700',
}
</script>

<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 :class="['text-2xl font-bold', theme.isDark ? 'text-bordeaux-100' : 'text-bordeaux-900']">
          Mes demandes
        </h1>
        <p :class="['text-sm', theme.isDark ? 'text-bordeaux-400' : 'text-bordeaux-600']">
          Suivez l'état de vos demandes de dépôt
        </p>
      </div>
      <Button label="Nouvelle demande" icon="pi pi-plus" @click="router.push('/espace/nouvelle-demande')" />
    </div>

    <!-- Filtre -->
    <div class="mb-5">
      <Select
        v-model="filterStatus"
        :options="statusOptions"
        optionLabel="label"
        optionValue="value"
        placeholder="Filtrer par statut"
        class="w-56"
      />
    </div>

    <!-- Liste -->
    <div class="flex flex-col gap-4">
      <div
        v-for="d in demandes" :key="d.id"
        :class="['rounded-2xl p-5 backdrop-blur-xl border',
          theme.isDark ? 'bg-white/5 border-white/10' : 'bg-white/30 border-white/50']">
        <div class="flex items-start justify-between gap-4">
          <div class="flex-1">
            <h3 :class="['font-semibold mb-1', theme.isDark ? 'text-bordeaux-200' : 'text-bordeaux-800']">
              {{ d.title }}
            </h3>
            <p :class="['text-xs', theme.isDark ? 'text-bordeaux-500' : 'text-bordeaux-400']">
              Soumise le {{ d.date }}
            </p>
            <div v-if="d.justification" :class="['mt-3 p-3 rounded-xl text-sm',
              theme.isDark ? 'bg-red-900/20 text-red-300 border border-red-800/30' : 'bg-red-50 text-red-700 border border-red-200']">
              <i class="pi pi-info-circle mr-1"></i>
              {{ d.justification }}
            </div>
          </div>
          <span :class="['text-xs px-2.5 py-1 rounded-full font-medium shrink-0', statusClass[d.status]]">
            {{ d.status }}
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

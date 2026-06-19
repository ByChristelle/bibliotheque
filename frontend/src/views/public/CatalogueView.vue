<script setup>
import { ref } from 'vue'
import { useThemeStore } from '@/stores/theme'
import { useRouter } from 'vue-router'
import InputText from 'primevue/inputtext'
import Button from 'primevue/button'
import Select from 'primevue/select'

const theme = useThemeStore()
const router = useRouter()

const search = ref('')
const selectedCategory = ref(null)

const categories = [
  { label: 'Toutes les catégories', value: null },
  { label: 'Sciences', value: 'sciences' },
  { label: 'Littérature', value: 'litterature' },
  { label: 'Droit', value: 'droit' },
  { label: 'Histoire', value: 'histoire' },
]

// Données de démo
const references = [
  { id: 1, title: 'Introduction aux algorithmes', author: 'Thomas H. Cormen', category: 'Sciences', year: 2009, downloads: 342 },
  { id: 2, title: 'Le droit constitutionnel moderne', author: 'Pierre Avril', category: 'Droit', year: 2018, downloads: 128 },
  { id: 3, title: "Histoire de l'Afrique", author: 'Joseph Ki-Zerbo', category: 'Histoire', year: 1972, downloads: 512 },
  { id: 4, title: 'Les misérables', author: 'Victor Hugo', category: 'Littérature', year: 1862, downloads: 891 },
  { id: 5, title: 'Physique quantique', author: 'Richard Feynman', category: 'Sciences', year: 1985, downloads: 267 },
  { id: 6, title: 'Économie politique', author: 'Alain Barrère', category: 'Sciences', year: 2001, downloads: 183 },
]
</script>

<template>
  <div class="max-w-7xl mx-auto px-6 py-10">
    <!-- En-tête -->
    <div class="mb-8">
      <h1 :class="['text-3xl font-bold mb-2', theme.isDark ? 'text-bordeaux-100' : 'text-bordeaux-900']">
        Catalogue
      </h1>
      <p :class="['text-sm', theme.isDark ? 'text-bordeaux-400' : 'text-bordeaux-600']">
        {{ references.length }} références disponibles
      </p>
    </div>

    <!-- Filtres -->
    <div :class="['rounded-2xl p-5 mb-8 backdrop-blur-xl border flex flex-wrap gap-4',
      theme.isDark ? 'bg-white/5 border-white/10' : 'bg-white/30 border-white/50']">
      <InputText
        v-model="search"
        placeholder="Rechercher un titre, auteur..."
        class="flex-1 min-w-48"
      />
      <Select
        v-model="selectedCategory"
        :options="categories"
        optionLabel="label"
        optionValue="value"
        placeholder="Catégorie"
        class="min-w-48"
      />
      <Button label="Rechercher" icon="pi pi-search" />
    </div>

    <!-- Grille -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
      <div
        v-for="ref in references"
        :key="ref.id"
        :class="['rounded-2xl p-5 backdrop-blur-xl border cursor-pointer transition-all hover:-translate-y-1 hover:shadow-xl',
          theme.isDark ? 'bg-white/5 border-white/10 hover:bg-white/10' : 'bg-white/30 border-white/50 hover:bg-white/50']"
        @click="router.push(`/references/${ref.id}`)"
      >
        <div class="flex items-start justify-between mb-3">
          <span :class="['text-xs font-medium px-2.5 py-1 rounded-full',
            theme.isDark ? 'bg-bordeaux-800/60 text-bordeaux-300' : 'bg-bordeaux-100 text-bordeaux-700']">
            {{ ref.category }}
          </span>
          <span :class="['text-xs', theme.isDark ? 'text-bordeaux-500' : 'text-bordeaux-400']">
            {{ ref.year }}
          </span>
        </div>

        <h3 :class="['font-semibold mb-1 line-clamp-2', theme.isDark ? 'text-bordeaux-200' : 'text-bordeaux-800']">
          {{ ref.title }}
        </h3>
        <p :class="['text-sm mb-4', theme.isDark ? 'text-bordeaux-400' : 'text-bordeaux-600']">
          {{ ref.author }}
        </p>

        <div class="flex items-center justify-between">
          <span :class="['text-xs flex items-center gap-1', theme.isDark ? 'text-bordeaux-500' : 'text-bordeaux-400']">
            <i class="pi pi-download"></i> {{ ref.downloads }}
          </span>
          <Button label="Voir" icon="pi pi-eye" text size="small" />
        </div>
      </div>
    </div>
  </div>
</template>

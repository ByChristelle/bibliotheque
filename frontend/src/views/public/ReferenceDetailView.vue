<script setup>
import { useThemeStore } from '@/stores/theme'
import { useRouter } from 'vue-router'
import Button from 'primevue/button'

const theme = useThemeStore()
const router = useRouter()

// Données de démo — TODO: charger depuis l'API via useRoute().params.id
const reference = {
  title: 'Introduction aux algorithmes',
  author: 'Thomas H. Cormen',
  category: 'Sciences',
  year: 2009,
  publisher: 'MIT Press',
  keywords: ['algorithmes', 'informatique', 'structures de données', 'complexité'],
  description: "Cet ouvrage de référence présente de façon exhaustive les algorithmes fondamentaux de l'informatique. Il couvre les structures de données, le tri, la recherche, la théorie des graphes et bien plus encore.",
  downloads: 342,
  views: 1240,
}
</script>

<template>
  <div class="max-w-4xl mx-auto px-6 py-10">
    <!-- Retour -->
    <Button label="Retour au catalogue" icon="pi pi-arrow-left" text size="small" class="mb-6" @click="router.back()" />

    <div :class="['rounded-2xl p-8 backdrop-blur-xl border',
      theme.isDark ? 'bg-white/5 border-white/10' : 'bg-white/30 border-white/50']">

      <!-- Header -->
      <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
        <div>
          <span :class="['text-xs font-medium px-2.5 py-1 rounded-full',
            theme.isDark ? 'bg-bordeaux-800/60 text-bordeaux-300' : 'bg-bordeaux-100 text-bordeaux-700']">
            {{ reference.category }}
          </span>
          <h1 :class="['text-2xl font-bold mt-3 mb-1', theme.isDark ? 'text-bordeaux-100' : 'text-bordeaux-900']">
            {{ reference.title }}
          </h1>
          <p :class="['text-sm', theme.isDark ? 'text-bordeaux-400' : 'text-bordeaux-600']">
            {{ reference.author }} · {{ reference.year }}
          </p>
        </div>

        <div class="flex gap-2">
          <Button label="Lire en ligne" icon="pi pi-eye" />
          <Button label="Télécharger" icon="pi pi-download" outlined />
        </div>
      </div>

      <!-- Infos -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div v-for="info in [
          { label: 'Éditeur', value: reference.publisher },
          { label: 'Année', value: reference.year },
          { label: 'Téléchargements', value: reference.downloads },
          { label: 'Consultations', value: reference.views },
        ]" :key="info.label"
          :class="['rounded-xl p-3 text-center',
            theme.isDark ? 'bg-white/5' : 'bg-bordeaux-50/60']">
          <div :class="['text-lg font-bold', theme.isDark ? 'text-bordeaux-200' : 'text-bordeaux-800']">
            {{ info.value }}
          </div>
          <div :class="['text-xs', theme.isDark ? 'text-bordeaux-500' : 'text-bordeaux-500']">
            {{ info.label }}
          </div>
        </div>
      </div>

      <!-- Description -->
      <div class="mb-6">
        <h2 :class="['text-base font-semibold mb-2', theme.isDark ? 'text-bordeaux-300' : 'text-bordeaux-700']">
          Description
        </h2>
        <p :class="['text-sm leading-relaxed', theme.isDark ? 'text-bordeaux-400' : 'text-bordeaux-600']">
          {{ reference.description }}
        </p>
      </div>

      <!-- Mots-clés -->
      <div>
        <h2 :class="['text-base font-semibold mb-2', theme.isDark ? 'text-bordeaux-300' : 'text-bordeaux-700']">
          Mots-clés
        </h2>
        <div class="flex flex-wrap gap-2">
          <span
            v-for="kw in reference.keywords" :key="kw"
            :class="['text-xs px-3 py-1 rounded-full',
              theme.isDark ? 'bg-bordeaux-800/50 text-bordeaux-300' : 'bg-bordeaux-100 text-bordeaux-700']">
            {{ kw }}
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

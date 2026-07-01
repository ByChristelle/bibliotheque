<script setup>
import { useThemeStore } from '@/stores/theme'
import { computed } from 'vue'
import { useRouter , useRoute } from 'vue-router'
import Button from 'primevue/button'
import { useReferenceStore } from '@/stores/reference'
import { onMounted } from 'vue'
import { Loader2 } from 'lucide-vue-next'

const theme = useThemeStore()
const router = useRouter()
const route = useRoute()
const referenceStore = useReferenceStore()

onMounted(async () => {
  await referenceStore.fetchSingleReference(route.params.id)
})

function formatAuthors(authors) {
  if (!authors?.length) return '—'
  return authors.map(a => `${a.first_name} ${a.last_name}`).join(', ')
}

const referenceInfos = computed(() => {
  if (!referenceStore.currentReference) return []
  return [
    { 
      label: 'Langue', 
      value: referenceStore.currentReference.language === 'fr' ? 'Français' : referenceStore.currentReference.language === 'en' ? 'Anglais' : 'Autre' 
    },
    { label: 'Pages', value: referenceStore.currentReference.pages || '—' },
    { label: 'Téléchargements', value: referenceStore.currentReference.download_count || 0 },
    { label: 'Vues', value: referenceStore.currentReference.view_count || 0 }
  ]
})
</script>

<template>
  <div class="max-w-4xl mx-auto px-6 py-10">
    <!-- Retour -->
    <Button label="Retour au catalogue" icon="pi pi-arrow-left" text size="small" class="mb-6" @click="router.back()" />

    <!-- Loader -->
    <div class="flex items-center justify-center py-12" v-if="referenceStore.loading">
      <Loader2 class="w-10 h-10 text-primary-500 animate-spin" />
      <span class="ml-3 text-sm">Chargement de la référence...</span>
    </div>

    <!-- Contenu -->
    <div v-else-if="referenceStore.currentReference" :class="['rounded-2xl p-8 backdrop-blur-xl border',
      theme.isDark ? 'bg-white/5 border-white/10' : 'bg-white/30 border-white/50']">

      <!-- Header -->
      <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
        <div class="flex items-start gap-6 flex-1">
          <div v-if="referenceStore.currentReference.cover_image" class="w-32 h-44 rounded-lg bg-cover bg-center shrink-0"
            :style="{ backgroundImage: `url(${referenceStore.currentReference.cover_image})` }">
          </div>
          <div class="flex-1">
            <span :class="['text-xs font-medium px-2.5 py-1 rounded-full mb-3 inline-block',
              theme.isDark ? 'bg-bordeaux-800/60 text-bordeaux-300' : 'bg-bordeaux-100 text-bordeaux-700']">
              {{ referenceStore.currentReference.category?.name || '—' }}
            </span>
            <h1 :class="['text-2xl font-bold mb-1', theme.isDark ? 'text-bordeaux-100' : 'text-bordeaux-900']">
              {{ referenceStore.currentReference.title }}
            </h1>
            <p :class="['text-sm mb-2', theme.isDark ? 'text-bordeaux-400' : 'text-bordeaux-600']">
              {{ formatAuthors(referenceStore.currentReference.authors) }}
            </p>
            <p :class="['text-sm', theme.isDark ? 'text-bordeaux-500' : 'text-bordeaux-500']">
              {{ referenceStore.currentReference.publisher?.name || '—' }}
              {{ referenceStore.currentReference.publication_year ? ` · ${referenceStore.currentReference.publication_year}` : '' }}
            </p>
          </div>
        </div>

        <div class="flex gap-2">
          <Button v-if="referenceStore.currentReference.file_path" label="Télécharger" icon="pi pi-download" />
          <Button label="Voir en ligne" icon="pi pi-eye" outlined @click.stop= "router.push('/inscription')" />
        </div>
      </div>

      <!-- Infos -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div v-for="info in referenceInfos" :key="info.label"
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
      <div v-if="referenceStore.currentReference.abstract" class="mb-6">
        <h2 :class="['text-base font-semibold mb-2', theme.isDark ? 'text-bordeaux-300' : 'text-bordeaux-700']">
          Résumé
        </h2>
        <p :class="['text-sm leading-relaxed', theme.isDark ? 'text-bordeaux-400' : 'text-bordeaux-600']">
          {{ referenceStore.currentReference.abstract }}
        </p>
      </div>
    </div>
  </div>
</template>

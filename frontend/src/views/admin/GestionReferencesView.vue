<script setup>
import { computed, onMounted, ref } from 'vue'
import { useThemeStore } from '@/stores/theme'
import { useAuthStore } from '@/stores/auth'
import { useReferenceStore } from '@/stores/reference'
import { useRouter } from 'vue-router'
import { Loader2 } from 'lucide-vue-next'
import InputText from 'primevue/inputtext'

const theme          = useThemeStore()
const auth           = useAuthStore()
const referenceStore = useReferenceStore()
const router         = useRouter()

const search = ref('')

onMounted(async () => {
  await referenceStore.fetchReferences()
})

const references = computed(() => {
  const q = search.value.toLowerCase().trim()
  if (!q) return referenceStore.references
  return referenceStore.references.filter(r =>
    r.title?.toLowerCase().includes(q) ||
    r.authors?.some(a => `${a.first_name} ${a.last_name}`.toLowerCase().includes(q)) ||
    r.category?.name?.toLowerCase().includes(q)
  )
})

const isAdmin = computed(() => auth.user?.role === 'admin')

function coverUrl(path) {
  if (!path) return null
  // Si l'URL est déjà complète, on la retourne telle quelle
  if (path.startsWith('http')) return path
  return `http://localhost:8000/storage/${path}`
}

function authorsLabel(authors) {
  if (!authors?.length) return 'Auteur inconnu'
  return authors.map(a => `${a.first_name} ${a.last_name}`).join(', ')
}

const statusColor = {
  published: 'bg-green-100 text-green-700',
  draft:     'bg-yellow-100 text-yellow-700',
  archived:  'bg-red-100 text-red-700',
}
const statusLabel = {
  published: 'Publié',
  draft:     'Brouillon',
  archived:  'Archivé',
}

function lire(ref) {
  if (ref.file_path) window.open(`http://localhost:8000/api/references/${ref.id}/lire`, '_blank')
}

function telecharger(ref) {
  window.open(`http://localhost:8000/api/references/${ref.id}/download`, '_blank')
}

</script>

<template>
  <div class="pb-20">

    <!-- En-tête -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 :class="['text-2xl font-bold', theme.isDark ? 'text-white' : 'text-[#2D2D2D]']">Références</h1>
        <p class="text-sm text-[#94A3B8] mt-0.5">{{ references.length }} référence{{ references.length > 1 ? 's' : '' }}</p>
      </div>
    </div>

    <!-- Recherche -->
    <div class="mb-6">
      <InputText v-model="search" placeholder="Rechercher un titre, auteur, catégorie..." class="w-full max-w-sm" />
    </div>

    <!-- Loader -->
    <div v-if="referenceStore.loading" class="flex items-center justify-center py-20">
      <Loader2 class="w-10 h-10 animate-spin text-bordeaux-600" />
      <span class="ml-3 text-sm text-[#94A3B8]">Chargement des références...</span>
    </div>

    <!-- Grille de cartes -->
  <div v-else-if="references.length" class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-10 max-w-6xl mx-auto px-6">
  <div
    v-for="ref in references" :key="ref.id"
    class="rounded-2xl overflow-hidden flex flex-col transition-all duration-300 hover:-translate-y-1 hover:shadow-lg cursor-pointer"
    :class="theme.isDark ? 'bg-[#0F172A] border border-white/[0.07]' : 'bg-white border border-[#ECECEC] shadow-sm'"
    @click="router.push(`/references/${ref.id}`)"
  >
    <!-- Couverture (Aspect horizontal comme la maquette) -->
    <div class="relative w-full aspect-[16/10] flex items-center justify-center overflow-hidden"
      :class="theme.isDark ? 'bg-[#1E293B]' : 'bg-[#F8F6F6]'">
      <img
        v-if="coverUrl(ref.cover_image)"
        :src="coverUrl(ref.cover_image)"
        :alt="ref.title"
        class="w-full h-full object-cover transition-transform duration-300 hover:scale-105"
      />
      <!-- Placeholder si pas d'image -->
      <div v-else class="flex flex-col items-center justify-center w-full h-full opacity-40">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
          class="w-12 h-12" :class="theme.isDark ? 'text-white/30' : 'text-[#B78493]'">
          <path d="M11.25 4.533A9.707 9.707 0 0 0 6 3a9.735 9.735 0 0 0-3.25.555.75.75 0 0 0-.5.707v14.25a.75.75 0 0 0 1 .707A8.237 8.237 0 0 1 6 18.75c1.995 0 3.823.707 5.25 1.886V4.533ZM12.75 20.636A8.214 8.214 0 0 1 18 18.75c.966 0 1.89.166 2.75.47a.75.75 0 0 0 1-.708V4.262a.75.75 0 0 0-.5-.707A9.735 9.735 0 0 0 18 3a9.707 9.707 0 0 0-5.25 1.533v16.103Z" />
        </svg>
      </div>
    </div>

    <!-- Infos + Actions -->
    <div class="flex flex-col gap-3 p-4 flex-1">
      <!-- Titre & Auteur -->
      <div class="flex-1">
        <h3 class="text-sm font-bold leading-snug line-clamp-2 min-h-[40px]"
          :class="theme.isDark ? 'text-white' : 'text-[#2D2D2D]'">
          {{ ref.title }}
        </h3>
        <p class="text-xs mt-1 line-clamp-2"
          :class="theme.isDark ? 'text-white/50' : 'text-[#777]'">
          {{ authorsLabel(ref.authors) }}
        </p>
      </div>

      <!-- Pied de carte : Statut + Année -->
      <div class="flex items-center justify-between pt-1">
        <span class="text-[11px] px-2 py-0.5 rounded-full font-medium"
          :class="statusColor[ref.status] || 'bg-gray-100 text-gray-600'">
          {{ statusLabel[ref.status] || ref.status }}
          <span v-if="ref.publication_year"> · {{ ref.publication_year }}</span>
        </span>
      </div>

      <!-- Boutons Actions côte à côte (Bordeaux & Gris clair) -->
      <div class="flex items-center gap-2 mt-1">
        <!-- Télécharger (Bouton Principal) -->
        <button
          @click.stop="telecharger(ref)"
          class="flex-1 py-2 rounded-xl text-xs font-semibold flex items-center justify-center gap-1.5 transition-all duration-200 shadow-sm"
          :class="theme.isDark ? 'bg-bordeaux-700/90 text-white hover:bg-bordeaux-600' : 'bg-[#7A0026] text-white hover:bg-[#5e001c]'"
        >
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-3.5 h-3.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
          </svg>
          Télécharger
        </button>

        <!-- Lire (Bouton Secondaire) -->
        <button
          v-if="ref.file_path"
          @click.stop="lire(ref)"
          class="px-3 py-2 rounded-xl text-xs font-semibold flex items-center justify-center gap-1.5 transition-all duration-200 border"
          :class="theme.isDark ? 'bg-white/5 border-white/10 text-white hover:bg-white/10' : 'bg-[#F4F4F4] border-[#E5E5E5] text-[#2D2D2D] hover:bg-[#EAEAEA]'"
        >
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-3.5 h-3.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
          </svg>
          Lire
        </button>
      </div>
    </div>
  </div>
</div>

    <!-- État vide -->
    <div v-else class="flex flex-col items-center justify-center py-20 text-center">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
        class="w-16 h-16 mb-4 text-[#94A3B8]">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
      </svg>
      <p class="text-[#94A3B8] font-medium">Aucune référence trouvée</p>
      <p class="text-sm text-[#94A3B8]/60 mt-1">Essayez un autre terme de recherche</p>
    </div>

    <!-- FAB admin -->
    <div class="fixed bottom-8 right-8 z-50 group" v-if="isAdmin">
      <button
        class="w-14 h-14 rounded-full text-white shadow-xl hover:shadow-2xl hover:scale-110 transition-all duration-300 flex items-center justify-center"
        :class="theme.isDark ? 'bg-bordeaux-700' : 'bg-[#7A0026]'"
      >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
      </button>
      <div class="absolute bottom-full right-0 mb-2 px-3 py-1 rounded-lg shadow text-xs font-medium whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity"
        :class="theme.isDark ? 'bg-[#1E293B] text-white' : 'bg-white text-[#2D2D2D] border border-[#ECECEC]'">
        Ajouter une référence
      </div>
    </div>

  </div>
</template>

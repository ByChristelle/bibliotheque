<script setup>
import { ref, computed, onMounted } from 'vue'
import { useThemeStore } from '@/stores/theme'
import { useRouter } from 'vue-router'
import InputText from 'primevue/inputtext'
import Button from 'primevue/button'
import Select from 'primevue/select'
import { useReferenceStore } from '@/stores/reference'
import { useCategoriesStore } from '@/stores/categories'
import { Loader2 } from 'lucide-vue-next'

const theme = useThemeStore()
const router = useRouter()
const referenceStore = useReferenceStore()
const categoriesStore = useCategoriesStore()

const search = ref('')
const selectedCategory = ref(null)

onMounted(async () => {
  await Promise.all([
    referenceStore.fetchReferences(),
    categoriesStore.fetchCategories()
  ])
  console.log('References chargées:', referenceStore.references)
  console.log('Categories chargées:', categoriesStore.categories)
})

const categoriesOptions = computed(() => [
  { label: 'Toutes les catégories', value: null },
  ...categoriesStore.categories.map(c => ({ label: c.name, value: c.id }))
])

const filteredReferences = computed(() => {
  let refs = referenceStore.references

  if (selectedCategory.value) {
    refs = refs.filter(r => r.category_id === selectedCategory.value)
  }

  if (search.value) {
    const searchLower = search.value.toLowerCase()
    refs = refs.filter(r => 
      r.title.toLowerCase().includes(searchLower) ||
      r.authors?.some(a => `${a.first_name} ${a.last_name}`.toLowerCase().includes(searchLower))
    )
  }

  return refs
})

function formatAuthors(authors) {
  if (!authors?.length) return '—'
  return authors.map(a => `${a.first_name} ${a.last_name}`).join(', ')
}
</script>

<template>
  <!-- Fond global teinté doux et chaud comme sur l'image -->
  <div :class="['min-h-screen max-w-7xl mx-auto px-6 py-10 transition-colors duration-200', 
    theme.isDark ? 'bg-stone-900 text-stone-100' : 'bg-[#f4efe6] text-stone-900']">
    
    <!-- En-tête -->
    <div class="mb-8">
      <h1 :class="['text-4xl font-bold mb-2 tracking-tight', theme.isDark ? 'text-stone-100' : 'text-[#3d0c1b]']">
        Catalogue
      </h1>
      <p :class="['text-sm font-medium', theme.isDark ? 'text-stone-400' : 'text-[#5c4d4d]']">
        {{ filteredReferences.length }} références disponibles
      </p>
    </div>

    <!-- Filtres avec bordure bordeaux fine et coins adoucis -->
    <div :class="['rounded-xl p-4 mb-8 border flex flex-wrap gap-4 items-center',
      theme.isDark ? 'bg-stone-800/80 border-[#5c1c2e]' : 'bg-[#faf8f5] border-[#5a1827] shadow-sm']">
      <InputText
        v-model="search"
        placeholder="Rechercher un titre, auteur..."
        class="flex-1 min-w-48 !bg-transparent !border-none focus:!ring-0"
      />
      <Select
        v-model="selectedCategory"
        :options="categoriesOptions"
        optionLabel="label"
        optionValue="value"
        placeholder="Catégorie"
        class="min-w-48 !bg-transparent !border-none"
      />
    </div>

    <!-- Loader -->
    <div class="flex items-center justify-center py-12" v-if="referenceStore.loading">
      <Loader2 class="w-10 h-10 text-[#8a2b43] animate-spin" />
      <span class="ml-3 text-sm">Chargement du catalogue...</span>
    </div>

    <!-- Grille des Catalogues -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="ref in filteredReferences"
        :key="ref.id"
        :class="['relative rounded-2xl p-5 border-2 transition-all duration-200 hover:shadow-md flex flex-col justify-between',
          theme.isDark ? 'bg-stone-800 border-[#5c1c2e] hover:border-[#8a2b43]' : 'bg-[#faf8f5] border-[#5a1827] hover:bg-[#fcfbfa]']"
      >
        <!-- Section Supérieure : Image + Textes -->
        <div class="flex items-start gap-4 mb-5">
          <!-- Image de couverture style "Livre" ou Miniature -->
          <div v-if="ref.cover_image" class="w-24 h-28 rounded-xl bg-cover bg-center shrink-0 border border-stone-200 shadow-sm"
            :style="{ backgroundImage: `url(${ref.cover_image})` }">
          </div>
          <div v-else class="w-24 h-28 rounded-xl bg-stone-200 shrink-0 flex items-center justify-center border border-stone-300">
            <i class="pi pi-book text-stone-400 text-2xl"></i>
          </div>

          <div class="flex-1 min-w-0">
            <!-- Badge Catégorie Soft -->
            <span :class="['text-xs font-semibold px-2.5 py-0.5 rounded-md mb-1.5 inline-block',
              theme.isDark ? 'bg-stone-700 text-stone-300' : 'bg-[#e5ded4] text-[#5c4d4d]']">
              {{ ref.category?.name || '—' }}
            </span>
            <!-- Titre Principal -->
            <h3 :class="['font-bold text-lg mb-0.5 truncate', theme.isDark ? 'text-stone-100' : 'text-[#2b0813]']">
              {{ ref.title }}
            </h3>
           <!-- Auteur -->
<p :class="['text-sm font-medium mb-3 truncate font-sans', theme.isDark ? 'text-stone-400' : 'text-[#706060]']">
  {{ formatAuthors(ref.authors) }}
</p>
          </div>
        </div>

        <!-- Section Inférieure : Boutons d'actions empilés & Bouton J'aime -->
        <div class="mt-auto pt-2 flex flex-col gap-2">
          <!-- Bouton Lire (Si disponible/Logique) -->
         
          <button class="w-full py-2 px-4 rounded-xl text-sm font-medium flex items-center justify-center gap-2 transition-colors bg-[#b09e99]/30 text-[#5c4d4d] hover:bg-[#b09e99]/40"  >
               <RouterLink to="/inscription" >
          <i class="pi pi-file text-xs"></i>  Lire
          </RouterLink>
          </button>

          <!-- Bouton Télécharger -->
          <button class="w-full py-2 px-4 rounded-xl text-sm font-medium flex items-center justify-center gap-2 transition-colors bg-[#b09e99]/30 text-[#5c4d4d] hover:bg-[#b09e99]/40">
           <RouterLink to="/inscription" >
          <i class="pi pi-file text-xs"></i>  Telecharger
          </RouterLink>
          </button>

          <!-- Bouton Détails / Voir Plus -->
          <button @click.stop="router.push(`/references/${ref.id}`)" class="w-full py-2 px-4 rounded-xl text-sm font-medium flex items-center justify-center gap-2 transition-colors bg-[#b09e99]/30 text-[#5c4d4d] hover:bg-[#b09e99]/40">
            <i class="pi pi-eye text-xs"></i> Détails
          </button>

          <!-- Ligne de pied : Année & Bouton J'aime Absolu/Aligné -->
          <div class="flex items-center justify-between mt-2 pt-1">
            <span :class="['text-xs font-semibold px-2 py-0.5 rounded bg-stone-200/60 dark:bg-stone-700 text-stone-600 dark:text-stone-400']">
              Publié - {{ ref.publication_year || '2025' }}
            </span>
            
            <!-- Bouton J'aime style Icône avec Compteur (0) -->
            <button class="flex flex-col items-center justify-center text-[#2b0813] hover:text-[#5a1827] transition-colors" @click.stop="">
              <i class="pi pi-heart text-lg"></i>
              <span class="text-[10px] font-bold">J'aime</span>
            </button>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

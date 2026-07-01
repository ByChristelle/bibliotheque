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
  <div class="max-w-7xl mx-auto px-6 py-10">
    <!-- En-tête -->
    <div class="mb-8">
      <h1 :class="['text-3xl font-bold mb-2', theme.isDark ? 'text-bordeaux-100' : 'text-bordeaux-900']">
        Catalogue
      </h1>
      <p :class="['text-sm', theme.isDark ? 'text-bordeaux-400' : 'text-bordeaux-600']">
        {{ filteredReferences.length }} références disponibles
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
        :options="categoriesOptions"
        optionLabel="label"
        optionValue="value"
        placeholder="Catégorie"
        class="min-w-48"
      />
    </div>

    <!-- Loader -->
    <div class="flex items-center justify-center py-12" v-if="referenceStore.loading">
      <Loader2 class="w-10 h-10 text-primary-500 animate-spin" />
      <span class="ml-3 text-sm">Chargement du catalogue...</span>
    </div>

    <!-- Grille -->
    <div v-else class=" grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
      <div
        v-for="ref in filteredReferences"
        :key="ref.id"
        :class="['rounded-2xl p-5 backdrop-blur-xl border cursor-pointer transition-all hover:-translate-y-1 hover:shadow-xl',
          theme.isDark ? 'bg-white/5 border-white/10 hover:bg-white/10' : 'bg-white/30 border-white/50 hover:bg-white/50']"
        @click="router.push(`/references/${ref.id}`)"
      >
        <div class=" flex items-start gap-3 mb-3">
          <div v-if="ref.cover_image" class="w-20 h-25 rounded-lg bg-cover bg-center shrink-0"
            :style="{ backgroundImage: `url(${ref.cover_image})` }">
          </div>
          <div class="flex-1">
            <span :class="['text-xs font-medium px-2.5 py-1 rounded-full mb-2 inline-block',
              theme.isDark ? 'bg-bordeaux-800/60 text-bordeaux-300' : 'bg-bordeaux-100 text-bordeaux-700']">
              {{ ref.category?.name || '—' }}
            </span>
            <h3 :class="['font-semibold mb-1 line-clamp-2', theme.isDark ? 'text-bordeaux-200' : 'text-bordeaux-800']">
              {{ ref.title }}
            </h3>
            <p :class="['text-sm mb-2', theme.isDark ? 'text-bordeaux-400' : 'text-bordeaux-600']">
              {{ formatAuthors(ref.authors) }}
            </p>
            <div class="flex items-center gap-4 text-xs" :class="theme.isDark ? 'text-bordeaux-500' : 'text-bordeaux-500'">
              <span v-if="ref.publication_year">{{ ref.publication_year }}</span>
              <span v-if="ref.download_count">
                <i class="pi pi-download mr-1"></i> {{ ref.download_count }}
              </span>
            </div>
          </div>
        </div>

        <div class="flex items-center justify-between">
          <Button label="Voir plus" icon="pi pi-eye" text size="small" @click.stop="router.push(`/references/${ref.id}`)" />
        </div>
      </div>
    </div>
  </div>
</template>

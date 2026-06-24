<script setup>
import { computed, onMounted, ref } from 'vue'
import { useThemeStore } from '@/stores/theme'
import InputText from 'primevue/inputtext'
import Button from 'primevue/button'
import Dialog from 'primevue/dialog'
import {useCategoriesStore} from '@/stores/categories'
import {Loader2} from 'lucide-vue-next'

const theme = useThemeStore()
const categoriesStore = useCategoriesStore()
const categories = computed(() => categoriesStore.categories)

onMounted(async()=>{
  await categoriesStore.fetchCategories()
})


const showDialog = ref(false)
const newCategory = ref({
  name: '',
  slug: '',
  description: ''
})

async function ajouter() {
  if (newCategory.value.name.trim()) {
    const success = await categoriesStore.createCategorie({
      name: newCategory.value.name.trim(),
      slug: newCategory.value.slug.trim(),
      description: newCategory.value.description.trim()
    })
    if (success) {
      newCategory.value = { name: '', slug: '', description: '' }
      showDialog.value = false
    }
  }
}


</script>

<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 :class="['text-2xl font-bold', theme.isDark ? 'text-bordeaux-100' : 'text-bordeaux-900']">
          Catégories, auteurs & mots-clés
        </h1>
        <p :class="['text-sm', theme.isDark ? 'text-bordeaux-400' : 'text-bordeaux-600']">
          Gérez les données de référence de la plateforme
        </p>
      </div>
    </div>

     <!-- Loader pendant le chargement -->
    <div class="flex items-center justify-center py-12" v-if="categoriesStore.loading">
      <Loader2 class="w-10 h-10 text-primary-500 animate-spin" />
  <span class="ml-3 text-sm">Chargement des Catégories...</span>

    </div>

    <!-- Grille des catégories, affichée seulement si pas en chargement -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="cat in categories" :key="cat.id"
        :class="['rounded-2xl p-5 backdrop-blur-xl border flex items-center justify-between',
          theme.isDark ? 'bg-white/5 border-white/10' : 'bg-white/30 border-white/50']">
        <div>
          <p :class="['font-semibold', theme.isDark ? 'text-bordeaux-200' : 'text-bordeaux-800']">{{ cat.name }}</p>
          <p :class="['text-xs', theme.isDark ? 'text-bordeaux-500' : 'text-bordeaux-400']">{{ cat.count }} références</p>
        </div>
        <div class="flex gap-1">
          <Button icon="pi pi-pencil" text rounded size="small" />
          <Button icon="pi pi-trash" text rounded size="small" severity="danger" />
        </div>
      </div>
    </div>

   

    <!-- Bouton flottant (FAB) en bas à droite -->
<div class="fixed bottom-8 right-8 z-50 group"
>
 <button
  @click="showDialog = true"
 class="  bg-gradient-to-br from-bordeaux-600 w-16 h-16 rounded-full bg-gradient-to-brfrom-bordeaux-600 to-bordeaux-800 text-white shadow-xl hover:shadow-2xl hover:scale-110 transition-all duration-300 flex items-center justify-center"
  >
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-8 h-8">

      <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
    </svg>
  </button>
  <!-- Tooltip "Ajouter" -->
  <div class="absolute bottom-full right-0 mb-2 px-3 py-1 rounded-lg shadow-lg text-sm font-medium whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"
       :class="theme.isDark ? 'bg-bordeaux-800 text-white' : 'bg-white text-gray-800'">
    Ajouter
  </div>
</div>

    

   <Dialog v-model:visible="showDialog" header="Nouvelle catégorie" :modal="true" :style="{ width: '380px' }">
      <div class="flex flex-col gap-3">
        <label class="text-sm font-semibold">Nom de la catégorie</label>
        <InputText v-model="newCategory.name" placeholder="ex: Philosophie" class="w-full" />
      </div>
      <div class="flex flex-col gap-3">
        <label class="text-sm font-semibold">slug de la catégorie</label>
        <InputText v-model="newCategory.slug" placeholder="ex: Philo" class="w-full" />
      </div>
      <div class="flex flex-col gap-3">
        <label class="text-sm font-semibold">description</label>
        <InputText v-model="newCategory.description"  class="w-full" />
      </div>
      <template #footer>
        <Button label="Annuler" text @click="showDialog = false" />
        <Button label="Ajouter"  @click="ajouter" />
      </template>
    </Dialog>
  </div>


    
  
</template>

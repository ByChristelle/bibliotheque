<script setup>
import { computed, onMounted, ref } from 'vue'
import { useThemeStore } from '@/stores/theme'
import InputText from 'primevue/inputtext'
import Button from 'primevue/button'
import Dialog from 'primevue/dialog'
import {useCategoriesStore} from '@/stores/categories'
import {Loader2} from 'lucide-vue-next'
import { useToast } from 'primevue'

const toast=useToast()
const theme = useThemeStore()
const categoriesStore = useCategoriesStore()


//Pour l'affichage
const categories = computed(() => categoriesStore.categories)
onMounted(async()=>{
  await categoriesStore.fetchCategories()
})

//Pour la création 
const showDialog = ref(false)
const newCategory = ref({
  name: '',
  slug: '',
  description: ''
})

async function ajouter() {
  if (newCategory.value.name.trim()) {
    try {
       const success = await categoriesStore.createCategorie({
      name: newCategory.value.name.trim(),
      slug: newCategory.value.slug.trim(),
      description: newCategory.value.description.trim() 
    })
         if (success) {
      newCategory.value = { name: '', slug: '', description: '' }
      showDialog.value = false
       toast.add({
        severity: 'success',
        summary: 'Categorie Crée',
        detail: categoriesStore.message, //  le message récupéré depuis la réponse
        // icon: 'pi pi-check',
        life: 4000
      })
    }
    } catch (error) {
       toast.add({
      severity: 'error',
      summary: 'Erreur',
      detail: "Une erreur est survenue lors de la création de la catégorie.",
      life: 4000
    })
    console.error(`erreur de modification` , error)
      
    }
  }
}

//Pour la modification
const showEditDialog = ref(false)
const editCategory = ref({ id: null, name: '', slug: '', description: '' })

function ouvrirModification(cat) {
  editCategory.value = { id: cat.id, name: cat.name, slug: cat.slug, description: cat.description ?? '' }
  showEditDialog.value = true
}

async function modifier() {
    const success = await categoriesStore.updateCategorie(editCategory.value.id, {
      name: editCategory.value.name,
      slug: editCategory.value.slug,
      description: editCategory.value.description
    })
    if (success) {
      showEditDialog.value = false
      toast.add({ severity: 'success', summary: 'Modifié', detail: categoriesStore.message, life: 3000 })
    }else{
      toast.add({ severity: 'error', summary: 'Erreur', detail: 'Modification échouée', life: 3000 })
    }
  }

//pour les voir detail
const showDetailDialog = ref(false)
const selectedCategory = ref(null)

function voirDetail(cat) {
  selectedCategory.value = cat
  showDetailDialog.value = true
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
    <div class="flex items-center justify-center z-50 py-12" v-if="categoriesStore.loading">
      <Loader2 class="w-10 h-10 text-primary-500  animate-spin " />
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
          <!-- Voir détail -->
          <button title="Voir le détail"
            class="w-8 h-8 rounded-lg flex items-center justify-center text-[#94A3B8] hover:text-[#60a5fa] hover:bg-[#60a5fa]/10 transition-all duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-4 h-4">
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
            </svg>
          </button>
          <!-- Modifier -->
          <button title="Modifier" @click="ouvrirModification(cat)"
            class="w-8 h-8 rounded-lg flex items-center justify-center text-[#94A3B8] hover:text-[#fbbf24] hover:bg-[#fbbf24]/10 transition-all duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-4 h-4">
              <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" />
            </svg>
          </button>
          <!-- Archiver -->
          <button title="Archiver" @click="voirDetail(cat)"
            class="w-8 h-8 rounded-lg flex items-center justify-center text-[#94A3B8] hover:text-[#f87171] hover:bg-[#f87171]/10 transition-all duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-4 h-4">
              <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
            </svg>
          </button>
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

    
<!-- Modal de creation -->
   <Dialog v-model:visible="showDialog" header="Nouvelle catégorie" :modal="true" :style="{ width: '380px' } " class="z-10">
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


    <!-- Modal de modification -->

    <Dialog v-model:visible="showEditDialog" header="Modifier la catégorie" :modal="true" :style="{ width: '380px' }">
  <div class="flex flex-col gap-3">
    <label class="text-sm font-semibold">Nom</label>
    <InputText v-model="editCategory.name" class="w-full" />
  </div>
  <div class="flex flex-col gap-3 mt-3">
    <label class="text-sm font-semibold">Slug</label>
    <InputText v-model="editCategory.slug" class="w-full" />
  </div>
  <div class="flex flex-col gap-3 mt-3">
    <label class="text-sm font-semibold">Description</label>
    <InputText v-model="editCategory.description" class="w-full" />
  </div>
  <template #footer>
    <Button label="Annuler" text @click="showEditDialog = false" />
    <Button label="Modifier" @click="modifier" />
  </template>
</Dialog>


<!-- Pour le voir détail -->
 <Dialog v-model:visible="showDetailDialog" header="Détail de la catégorie" :modal="true" :style="{ width: '420px' }">
  <div v-if="selectedCategory" class="flex flex-col gap-4">
    
    <div class="flex flex-col gap-1">
      <span class="text-xs font-semibold uppercase tracking-widest text-[#94A3B8]">Nom</span>
      <span class="text-sm font-medium">{{ selectedCategory.name }}</span>
    </div>

    <div class="flex flex-col gap-1">
      <span class="text-xs font-semibold uppercase tracking-widest text-[#94A3B8]">Slug</span>
      <span class="text-sm font-medium">{{ selectedCategory.slug }}</span>
    </div>

    <div class="flex flex-col gap-1">
      <span class="text-xs font-semibold uppercase tracking-widest text-[#94A3B8]">Description</span>
      <span class="text-sm">{{ selectedCategory.description || '—' }}</span>
    </div>

    <div class="flex flex-col gap-1">
      <span class="text-xs font-semibold uppercase tracking-widest text-[#94A3B8]">Références</span>
      <span class="text-sm font-medium">{{ selectedCategory.count }}</span>
    </div>

    <div class="flex flex-col gap-1">
      <span class="text-xs font-semibold uppercase tracking-widest text-[#94A3B8]">Créée le</span>
      <span class="text-sm">{{ new Date(selectedCategory.created_at).toLocaleDateString('fr-FR') }}</span>
    </div>

  </div>
  <template #footer>
    <Button label="Fermer" text @click="showDetailDialog = false" />
  </template>
</Dialog>


  </div>


    
  
</template>

<style>
.ttt{
  z-index: 50;
}
</style>

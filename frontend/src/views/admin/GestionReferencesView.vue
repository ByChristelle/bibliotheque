<script setup>
import { ref } from 'vue'
import { useThemeStore } from '@/stores/theme'
import InputText from 'primevue/inputtext'
import Button from 'primevue/button'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Tag from 'primevue/tag'

const theme = useThemeStore()
const search = ref('')

const references = ref([
  { id: 1, title: 'Introduction aux algorithmes', author: 'Thomas Cormen', category: 'Sciences', year: 2009, status: 'publiée' },
  { id: 2, title: 'Les misérables', author: 'Victor Hugo', category: 'Littérature', year: 1862, status: 'publiée' },
  { id: 3, title: "Histoire de l'Afrique", author: 'Joseph Ki-Zerbo', category: 'Histoire', year: 1972, status: 'publiée' },
])

const newReference= ref({
  title: '',
  author: '',
  category: '',
  year: 0,
  status: 'publiée'
})

const showDialog =ref(false)
const addReference = async ()=> {
  showDialog.value = true
}
</script>

<template>
  <div class="relative min-h-screen">
    <div>
      <div class="flex items-center justify-between mb-6">
        <div>
          <h1 :class="['text-2xl font-bold', theme.isDark ? 'text-bordeaux-100' : 'text-bordeaux-900']">
            Références
          </h1>
          <p :class="['text-sm', theme.isDark ? 'text-bordeaux-400' : 'text-bordeaux-600']">
            {{ references.length }} références publiées
          </p>
        </div>
      </div>

      <div class="mb-5">
        <InputText v-model="search" placeholder="Rechercher..." class="w-72" />
      </div>

      <div :class="['rounded-2xl overflow-hidden backdrop-blur-xl border',
        theme.isDark ? 'bg-white/5 border-white/10' : 'bg-white/30 border-white/50']">
        <DataTable :value="references" stripedRows>
          <Column field="title" header="Titre" />
          <Column field="author" header="Auteur" />
          <Column field="category" header="Catégorie" />
          <Column field="year" header="Année" />
          <Column field="status" header="Statut">
            <template #body="{ data }">
              <Tag :value="data.status" severity="success" />
            </template>
          </Column>
          <Column header="Actions">
            <template #body>
              <div class="flex gap-2">
                <Button icon="pi pi-pencil" text rounded size="small" />
                <Button icon="pi pi-trash" text rounded size="small" severity="danger" />
              </div>
            </template>
          </Column>
        </DataTable>
      </div>
    </div>

  <!-- Bouton flottant (FAB) en bas à droite -->
<div class="fixed bottom-8 right-8 z-50 group">
 <button
  @click="showDialog = true"
  class="w-16 h-16 rounded-full bg-gradient-to-br from-bordeaux-600 to-bordeaux-800 text-white shadow-xl hover:shadow-2xl hover:scale-110 transition-all duration-300 flex items-center justify-center"
  style="background: linear-gradient(to bottom right, #9f1239, #7f1d1d);"
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

    

    <!-- Modal (pour plus tard) -->
    <Dialog v-model:visible="showDialog" modal header="Ajouter une référence" :style="{ width: '50rem' }">
      <p>Contenu du modal à venir...</p>
    </Dialog>
  </div>
</template>

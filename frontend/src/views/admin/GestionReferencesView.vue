<script setup>
import { computed, onMounted, ref } from 'vue'
import { useThemeStore } from '@/stores/theme'
import { useAuthStore } from '@/stores/auth'
import InputText from 'primevue/inputtext'
import Button from 'primevue/button'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Tag from 'primevue/tag'
import Dialog from 'primevue/dialog'
import {Loader2} from 'lucide-vue-next'
import { useReferenceStore } from '@/stores/reference'

const theme = useThemeStore()
const auth = useAuthStore()
const search = ref('')
const referenceStore = useReferenceStore()
const isAdmin = computed(() => auth.user?.role === 'admin')

onMounted(async()=>{
  await referenceStore.fetchReferences()
  // await loadSelectOptions()

})
const references = computed(()=>referenceStore.references)

// État du formulaire
const newReference = ref({
  title: '',
  description: '',
  category_id: null,
  publisher_id: null,
  publication_year: new Date().getFullYear(),
  isbn: '',
  status: 'draft',
  cover_image: null
})


// État pour les listes déroulantes
const categories = ref([])
const publishers = ref([])
const authors = ref([])
const selectedAuthors = ref([])



// Gérer l'upload de l'image
const onImageUpload = (event) => {
  const file = event.files[0]
  if (file) {
    newReference.value.cover_image = file
  }
}

// Soumettre le formulaire
const submitReference = async () => {
  const formData = new FormData()
  formData.append('title', newReference.value.title)
  formData.append('description', newReference.value.description)
  formData.append('category_id', newReference.value.category_id)
  formData.append('publisher_id', newReference.value.publisher_id)
  formData.append('publication_year', newReference.value.publication_year)
  formData.append('isbn', newReference.value.isbn)
  formData.append('status', newReference.value.status)
  if (newReference.value.cover_image) {
    formData.append('cover_image', newReference.value.cover_image)
  }
  selectedAuthors.value.forEach(authorId => {
    formData.append('authors[]', authorId)
  })
   // Tu devras créer cette fonction dans le store
  const success = await referenceStore.createReference(formData)
  if (success) {
    showDialog.value = false
    resetForm()
  }
}

// Réinitialiser le formulaire
const resetForm = () => {
  newReference.value = {
    title: '',
    description: '',
    category_id: null,
    publisher_id: null,
    publication_year: new Date().getFullYear(),
    isbn: '',
    status: 'draft',
    cover_image: null
  }
  selectedAuthors.value = []
}


const showDialog = ref(false)

const getStatusSeverity = (status) => {
  const severities = {
    'draft': 'secondary',
    'published': 'success',
    'archived': 'danger'
  }
  return severities[status] || 'secondary'
}

const getStatusLabel = (status) => {
  const labels = {
    'draft': 'Brouillon',
    'published': 'Publié',
    'archived': 'Archivé'
  }
  return labels[status] || status
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

      <!-- Loader pendant le chargement -->
      <div class="flex items-center justify-center py-12" v-if="referenceStore.loading">
        <Loader2 class="w-10 h-10 text-primary-500 animate-spin" />
        <span class="ml-3 text-sm">Chargement des références...</span>
      </div>

      <!-- Tableau des références -->
      <div v-else :class="['rounded-2xl overflow-hidden backdrop-blur-xl border',
        theme.isDark ? 'bg-white/5 border-white/10' : 'bg-white/30 border-white/50']">
        <DataTable :value="references" stripedRows>
          <Column field="title" header="Titre" />
          <Column header="Auteurs">
            <template #body="{ data }">
              {{ data.authors?.map(a => `${a.first_name} ${a.last_name}`).join(', ') || '-' }}
            </template>
          </Column>
          <Column header="Catégorie">
            <template #body="{ data }">
              {{ data.category?.name || '-' }}
            </template>
          </Column>
          <Column field="publication_year" header="Année" />
          <Column header="Statut">
            <template #body="{ data }">
              <Tag :value="getStatusLabel(data.status)" :severity="getStatusSeverity(data.status)" />
            </template>
          </Column>
          <Column header="Actions" v-if="isAdmin">
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
<div class="fixed bottom-8 right-8 z-50 group" v-if="isAdmin">
 <button
  @click="showDialog = true"
  class="w-16 h-16 rounded-full bg-gradient-to-br from-bordeaux-600 to-bordeaux-800 text-white shadow-xl hover:shadow-2xl hover:scale-110 transition-all duration-300 flex items-center justify-center"
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
  <Dialog v-model:visible="showDialog" modal header="Ajouter une référence" :style="{ width: '50rem' }" @hide="resetForm">
  <div class="flex flex-col gap-4 p-2">
    <!-- Titre -->
    <div>
      <label class="block text-sm font-medium mb-1">Titre *</label>
      <InputText v-model="newReference.title" placeholder="Titre de la référence" class="w-full" />
    </div>

    <!-- Description -->
    <div>
      <label class="block text-sm font-medium mb-1">Description</label>
      <Textarea v-model="newReference.description" placeholder="Description de la référence" rows="3" class="w-full" />
    </div>

    <!-- Deux colonnes -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <!-- Catégorie -->
      <div>
        <label class="block text-sm font-medium mb-1">Catégorie *</label>
        <Dropdown v-model="newReference.category_id" :options="categories" option-label="name" option-value="id" placeholder="Sélectionner une catégorie" class="w-full" />
      </div>

      <!-- Éditeur -->
      <div>
        <label class="block text-sm font-medium mb-1">Éditeur</label>
        <Dropdown v-model="newReference.publisher_id" :options="publishers" option-label="name" option-value="id" placeholder="Sélectionner un éditeur" class="w-full" />
      </div>

      <!-- Année de publication -->
      <div>
        <label class="block text-sm font-medium mb-1">Année de publication</label>
        <InputNumber v-model="newReference.publication_year" :min="1900" :max="2030" class="w-full" />
      </div>

      <!-- ISBN -->
      <div>
        <label class="block text-sm font-medium mb-1">ISBN</label>
        <InputText v-model="newReference.isbn" placeholder="Numéro ISBN" class="w-full" />
      </div>

      <!-- Statut -->
      <div>
        <label class="block text-sm font-medium mb-1">Statut</label>
        <Dropdown v-model="newReference.status" :options="[{ label: 'Brouillon', value: 'draft' }, { label: 'Publié', value: 'published' }]" class="w-full" />
      </div>
    </div>

    <!-- Auteurs -->
    <div>
      <label class="block text-sm font-medium mb-1">Auteurs</label>
      <MultiSelect v-model="selectedAuthors" :options="authors" option-label="name" option-value="id" placeholder="Sélectionner des auteurs" display="chip" class="w-full" />
    </div>

    <!-- Upload de l'image de couverture -->
    <div>
      <label class="block text-sm font-medium mb-1">Image de couverture</label>
      <FileUpload
        mode="basic"
        :auto="true"
        @select="onImageUpload"
        accept="image/*"
        :max-file-size="2000000"
        choose-label="Choisir une image"
        class="w-full"
      />
    </div>

    <!-- Boutons de soumission -->
    <div class="flex justify-end gap-2 pt-4 border-t">
      <Button label="Annuler" severity="secondary" text @click="showDialog = false" />
      <Button label="Ajouter" @click="submitReference" :loading="referenceStore.loading" />
    </div>
  </div>
</Dialog>
  </div>
</template>

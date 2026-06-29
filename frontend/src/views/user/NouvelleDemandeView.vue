<script setup>
import { ref, computed, onMounted } from 'vue'
import { useThemeStore } from '@/stores/theme'
import { useRouter } from 'vue-router'
import InputText from 'primevue/inputtext'
import Textarea from 'primevue/textarea'
import Select from 'primevue/select'
import Button from 'primevue/button'
import { useCategoriesStore } from '@/stores/categories'
import { useToast } from 'primevue/usetoast'
import { useDepositRequestsStore } from '@/stores/depositRequests'


const theme = useThemeStore()
const router = useRouter()
const toast  = useToast()
const categoriesStore = useCategoriesStore()
const depositStore = useDepositRequestsStore()

onMounted(async () => {
  await categoriesStore.fetchCategories()
})

const categoriesOptions = computed(() =>
  categoriesStore.categories.map(c => ({ label: c.name, value: c.id }))
)

const languageOptions = [
  { label: 'Français', value: 'fr' },
  { label: 'Anglais',  value: 'en' },
  { label: 'Autre',    value: 'autre' },
]

const documentTypeOptions = [
  { label: 'Livre',    value: 'livre' },
  { label: 'Mémoire',  value: 'memoire' },
  { label: 'Thèse',    value: 'these' },
  { label: 'Article',  value: 'article' },
  { label: 'Revue',    value: 'revue' },
  { label: 'Rapport',  value: 'rapport' },
  { label: 'Guide',    value: 'guide' },
  { label: 'Autre',    value: 'autre' },
]

const form = ref({
  title:            '',
  subtitle:         '',
  authors:          '',
  category_id:      null,
  publisher:        '',
  publication_year: '',
  pages:            '',
  isbn:             '',
  language:         'fr',
  document_type:    null,
  keywords:         '',
  abstract:         '',
  cover_image:      null,
  file:             null,
})

const loading = ref(false)

function onCoverChange(e) {
  form.value.cover_image = e.target.files[0] ?? null
}

function onFileChange(e) {
  form.value.file = e.target.files[0] ?? null
}

async function submit() {
  const formData = new FormData()
  formData.append('title', form.value.title)
  if (form.value.description) formData.append('description', form.value.abstract)
  if (form.value.file) formData.append('proposed_file', form.value.file)

  loading.value = true
  const success = await depositStore.submit(formData)
  loading.value = false

  if (success) {
    toast.add({ severity: 'success', summary: 'Demande soumise', detail: depositStore.message, life: 4000 })
    router.push('/espace/mes-demandes')
  } else {
    toast.add({ severity: 'error', summary: 'Erreur', detail: 'Une erreur est survenue.', life: 4000 })
  }
}

</script>

<template>
  <div class="max-w-2xl">
    <div class="mb-6">
      <h1 :class="['text-2xl font-bold', theme.isDark ? 'text-bordeaux-100' : 'text-bordeaux-900']">
        Nouvelle demande de dépôt
      </h1>
      <p :class="['text-sm', theme.isDark ? 'text-bordeaux-400' : 'text-bordeaux-600']">
        Remplissez les informations de la référence à soumettre
      </p>
    </div>

    <div :class="['rounded-2xl p-6 backdrop-blur-xl border',
      theme.isDark ? 'bg-white/5 border-white/10' : 'bg-white/30 border-white/50']">
      <form @submit.prevent="submit" class="flex flex-col gap-5">

        <!-- Titre -->
        <div class="flex flex-col gap-1.5">
          <label :class="['text-sm font-semibold', theme.isDark ? 'text-bordeaux-300' : 'text-bordeaux-700']">
            Titre *
          </label>
          <InputText v-model="form.title" placeholder="Titre de la référence" class="w-full" required />
        </div>

        <!-- Sous-titre -->
        <div class="flex flex-col gap-1.5">
          <label :class="['text-sm font-semibold', theme.isDark ? 'text-bordeaux-300' : 'text-bordeaux-700']">
            Sous-titre
          </label>
          <InputText v-model="form.subtitle" placeholder="Sous-titre (optionnel)" class="w-full" />
        </div>

        <!-- Auteurs -->
        <div class="flex flex-col gap-1.5">
          <label :class="['text-sm font-semibold', theme.isDark ? 'text-bordeaux-300' : 'text-bordeaux-700']">
            Auteur(s) *
          </label>
          <InputText v-model="form.authors" placeholder="Nom Prénom, Nom Prénom..." class="w-full" required />
        </div>

        <!-- Année / Pages / ISBN -->
        <div class="grid grid-cols-3 gap-4">
          <div class="flex flex-col gap-1.5">
            <label :class="['text-sm font-semibold', theme.isDark ? 'text-bordeaux-300' : 'text-bordeaux-700']">
              Année
            </label>
            <InputText v-model="form.publication_year" placeholder="ex: 2024" class="w-full" />
          </div>
          <div class="flex flex-col gap-1.5">
            <label :class="['text-sm font-semibold', theme.isDark ? 'text-bordeaux-300' : 'text-bordeaux-700']">
              Pages
            </label>
            <InputText v-model="form.pages" placeholder="ex: 320" class="w-full" />
          </div>
          <div class="flex flex-col gap-1.5">
            <label :class="['text-sm font-semibold', theme.isDark ? 'text-bordeaux-300' : 'text-bordeaux-700']">
              ISBN
            </label>
            <InputText v-model="form.isbn" placeholder="978-..." class="w-full" />
          </div>
        </div>

        <!-- Type de document / Langue -->
        <div class="grid grid-cols-2 gap-4">
          <div class="flex flex-col gap-1.5">
            <label :class="['text-sm font-semibold', theme.isDark ? 'text-bordeaux-300' : 'text-bordeaux-700']">
              Type de document *
            </label>
            <Select v-model="form.document_type" :options="documentTypeOptions" optionLabel="label" optionValue="value" placeholder="Choisir..." class="w-full" />
          </div>
          <div class="flex flex-col gap-1.5">
            <label :class="['text-sm font-semibold', theme.isDark ? 'text-bordeaux-300' : 'text-bordeaux-700']">
              Langue
            </label>
            <Select v-model="form.language" :options="languageOptions" optionLabel="label" optionValue="value" class="w-full" />
          </div>
        </div>

        <!-- Catégorie / Éditeur -->
        <div class="grid grid-cols-2 gap-4">
          <div class="flex flex-col gap-1.5">
            <label :class="['text-sm font-semibold', theme.isDark ? 'text-bordeaux-300' : 'text-bordeaux-700']">
              Catégorie *
            </label>
            <Select v-model="form.category_id" :options="categoriesOptions" optionLabel="label" optionValue="value" placeholder="Choisir..." class="w-full" />
          </div>
          <div class="flex flex-col gap-1.5">
            <label :class="['text-sm font-semibold', theme.isDark ? 'text-bordeaux-300' : 'text-bordeaux-700']">
              Éditeur
            </label>
            <InputText v-model="form.publisher" placeholder="Nom de l'éditeur" class="w-full" />
          </div>
        </div>

        <!-- Mots-clés -->
        <div class="flex flex-col gap-1.5">
          <label :class="['text-sm font-semibold', theme.isDark ? 'text-bordeaux-300' : 'text-bordeaux-700']">
            Mots-clés
          </label>
          <InputText v-model="form.keywords" placeholder="Séparés par des virgules" class="w-full" />
        </div>

        <!-- Résumé -->
        <div class="flex flex-col gap-1.5">
          <label :class="['text-sm font-semibold', theme.isDark ? 'text-bordeaux-300' : 'text-bordeaux-700']">
            Résumé
          </label>
          <Textarea v-model="form.abstract" placeholder="Résumé ou description de la référence..." rows="4" class="w-full" />
        </div>

        <!-- Image de couverture -->
        <div class="flex flex-col gap-1.5">
          <label :class="['text-sm font-semibold', theme.isDark ? 'text-bordeaux-300' : 'text-bordeaux-700']">
            Image de couverture
          </label>
          <input type="file" accept="image/*" @change="onCoverChange"
            class="text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-bordeaux-50 file:text-bordeaux-700 hover:file:bg-bordeaux-100 cursor-pointer" />
        </div>

        <!-- Fichier PDF -->
        <div class="flex flex-col gap-1.5">
          <label :class="['text-sm font-semibold', theme.isDark ? 'text-bordeaux-300' : 'text-bordeaux-700']">
            Fichier (PDF)
          </label>
          <input type="file" accept=".pdf" @change="onFileChange"
            class="text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-bordeaux-50 file:text-bordeaux-700 hover:file:bg-bordeaux-100 cursor-pointer" />
        </div>

        <!-- Actions -->
        <div class="flex gap-3 pt-2">
          <Button type="submit" label="Soumettre la demande" icon="pi pi-send" :loading="loading" />
          <Button label="Annuler" text @click="router.push('/espace/mes-demandes')" />
        </div>

      </form>
    </div>
  </div>
</template>

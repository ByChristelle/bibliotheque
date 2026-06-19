<script setup>
import { ref } from 'vue'
import { useThemeStore } from '@/stores/theme'
import { useRouter } from 'vue-router'
import InputText from 'primevue/inputtext'
import Textarea from 'primevue/textarea'
import Select from 'primevue/select'
import Button from 'primevue/button'
import FileUpload from 'primevue/fileupload'

const theme = useThemeStore()
const router = useRouter()

const form = ref({
  title: '',
  authors: '',
  category: null,
  publisher: '',
  year: '',
  keywords: '',
  description: '',
  file: null,
})

const loading = ref(false)

const categories = [
  { label: 'Sciences', value: 'sciences' },
  { label: 'Littérature', value: 'litterature' },
  { label: 'Droit', value: 'droit' },
  { label: 'Histoire', value: 'histoire' },
]

function submit() {
  // TODO: appel API
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

        <div class="flex flex-col gap-1.5">
          <label :class="['text-sm font-semibold', theme.isDark ? 'text-bordeaux-300' : 'text-bordeaux-700']">
            Titre *
          </label>
          <InputText v-model="form.title" placeholder="Titre de la référence" class="w-full" required />
        </div>

        <div class="flex flex-col gap-1.5">
          <label :class="['text-sm font-semibold', theme.isDark ? 'text-bordeaux-300' : 'text-bordeaux-700']">
            Auteur(s) *
          </label>
          <InputText v-model="form.authors" placeholder="Nom Prénom, Nom Prénom..." class="w-full" required />
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div class="flex flex-col gap-1.5">
            <label :class="['text-sm font-semibold', theme.isDark ? 'text-bordeaux-300' : 'text-bordeaux-700']">
              Catégorie *
            </label>
            <Select v-model="form.category" :options="categories" optionLabel="label" optionValue="value" placeholder="Choisir..." class="w-full" />
          </div>

          <div class="flex flex-col gap-1.5">
            <label :class="['text-sm font-semibold', theme.isDark ? 'text-bordeaux-300' : 'text-bordeaux-700']">
              Année de publication
            </label>
            <InputText v-model="form.year" placeholder="ex: 2024" class="w-full" />
          </div>
        </div>

        <div class="flex flex-col gap-1.5">
          <label :class="['text-sm font-semibold', theme.isDark ? 'text-bordeaux-300' : 'text-bordeaux-700']">
            Éditeur
          </label>
          <InputText v-model="form.publisher" placeholder="Nom de l'éditeur" class="w-full" />
        </div>

        <div class="flex flex-col gap-1.5">
          <label :class="['text-sm font-semibold', theme.isDark ? 'text-bordeaux-300' : 'text-bordeaux-700']">
            Mots-clés
          </label>
          <InputText v-model="form.keywords" placeholder="Séparés par des virgules" class="w-full" />
        </div>

        <div class="flex flex-col gap-1.5">
          <label :class="['text-sm font-semibold', theme.isDark ? 'text-bordeaux-300' : 'text-bordeaux-700']">
            Description
          </label>
          <Textarea v-model="form.description" placeholder="Résumé ou description de la référence..." rows="4" class="w-full" />
        </div>

        <div class="flex flex-col gap-1.5">
          <label :class="['text-sm font-semibold', theme.isDark ? 'text-bordeaux-300' : 'text-bordeaux-700']">
            Fichier (PDF)
          </label>
          <FileUpload mode="basic" accept=".pdf" :maxFileSize="10000000" chooseLabel="Choisir un fichier" class="w-full" />
        </div>

        <div class="flex gap-3 pt-2">
          <Button type="submit" label="Soumettre la demande" icon="pi pi-send" :loading="loading" />
          <Button label="Annuler" text @click="router.push('/espace/mes-demandes')" />
        </div>
      </form>
    </div>
  </div>
</template>

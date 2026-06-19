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
</script>

<template>
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
</template>

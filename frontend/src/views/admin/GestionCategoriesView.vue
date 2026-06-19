<script setup>
import { ref } from 'vue'
import { useThemeStore } from '@/stores/theme'
import InputText from 'primevue/inputtext'
import Button from 'primevue/button'
import Dialog from 'primevue/dialog'

const theme = useThemeStore()

const categories = ref([
  { id: 1, name: 'Sciences', count: 450 },
  { id: 2, name: 'Littérature', count: 312 },
  { id: 3, name: 'Droit', count: 280 },
  { id: 4, name: 'Histoire', count: 198 },
])

const showDialog = ref(false)
const newCategory = ref('')

function ajouter() {
  if (newCategory.value.trim()) {
    categories.value.push({ id: Date.now(), name: newCategory.value.trim(), count: 0 })
    newCategory.value = ''
    showDialog.value = false
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
      <Button label="Ajouter une catégorie" icon="pi pi-plus" @click="showDialog = true" />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
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

    <Dialog v-model:visible="showDialog" header="Nouvelle catégorie" :modal="true" :style="{ width: '380px' }">
      <div class="flex flex-col gap-3">
        <label class="text-sm font-semibold">Nom de la catégorie</label>
        <InputText v-model="newCategory" placeholder="ex: Philosophie" class="w-full" />
      </div>
      <template #footer>
        <Button label="Annuler" text @click="showDialog = false" />
        <Button label="Ajouter" :disabled="!newCategory.trim()" @click="ajouter" />
      </template>
    </Dialog>
  </div>
</template>

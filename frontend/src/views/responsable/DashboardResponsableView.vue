<script setup>
import { useThemeStore } from '@/stores/theme'
import { useRouter } from 'vue-router'
import Button from 'primevue/button'

const theme = useThemeStore()
const router = useRouter()

const stats = [
  { label: 'Demandes affectées', value: 8, icon: 'pi pi-inbox', color: 'text-bordeaux-600' },
  { label: 'En attente', value: 5, icon: 'pi pi-clock', color: 'text-yellow-500' },
  { label: 'Validées', value: 2, icon: 'pi pi-check-circle', color: 'text-green-500' },
  { label: 'Refusées', value: 1, icon: 'pi pi-times-circle', color: 'text-red-500' },
]

const demandes = [
  { id: 1, title: 'Introduction aux algorithmes', auteur: 'Jean Kouassi', date: '17/06/2026' },
  { id: 2, title: 'Droit des contrats', auteur: 'Marie Tall', date: '16/06/2026' },
  { id: 3, title: 'Histoire contemporaine', auteur: 'Paul Diallo', date: '15/06/2026' },
]
</script>

<template>
  <div>
    <div class="mb-6">
      <h1 :class="['text-2xl font-bold', theme.isDark ? 'text-bordeaux-100' : 'text-bordeaux-900']">
        Tableau de bord
      </h1>
      <p :class="['text-sm', theme.isDark ? 'text-bordeaux-400' : 'text-bordeaux-600']">
        Demandes en attente de votre examen
      </p>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
      <div v-for="stat in stats" :key="stat.label"
        :class="['rounded-2xl p-5 backdrop-blur-xl border',
          theme.isDark ? 'bg-white/5 border-white/10' : 'bg-white/30 border-white/50']">
        <i :class="[stat.icon, stat.color, 'text-2xl mb-2 block']"></i>
        <div :class="['text-2xl font-bold', theme.isDark ? 'text-bordeaux-200' : 'text-bordeaux-800']">{{ stat.value }}</div>
        <div :class="['text-xs', theme.isDark ? 'text-bordeaux-500' : 'text-bordeaux-500']">{{ stat.label }}</div>
      </div>
    </div>

    <div :class="['rounded-2xl p-6 backdrop-blur-xl border',
      theme.isDark ? 'bg-white/5 border-white/10' : 'bg-white/30 border-white/50']">
      <div class="flex items-center justify-between mb-4">
        <h2 :class="['font-semibold', theme.isDark ? 'text-bordeaux-200' : 'text-bordeaux-800']">
          Demandes récentes
        </h2>
        <Button label="Voir tout" text size="small" @click="router.push('/responsable/demandes')" />
      </div>
      <div class="flex flex-col gap-3">
        <div v-for="d in demandes" :key="d.id"
          :class="['flex items-center justify-between p-3 rounded-xl',
            theme.isDark ? 'bg-white/5' : 'bg-bordeaux-50/50']">
          <div>
            <p :class="['text-sm font-medium', theme.isDark ? 'text-bordeaux-200' : 'text-bordeaux-800']">{{ d.title }}</p>
            <p :class="['text-xs', theme.isDark ? 'text-bordeaux-500' : 'text-bordeaux-400']">{{ d.auteur }} · {{ d.date }}</p>
          </div>
          <Button label="Examiner" size="small" @click="router.push('/responsable/demandes')" />
        </div>
      </div>
    </div>
  </div>
</template>

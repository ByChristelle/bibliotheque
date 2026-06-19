<script setup>
import { useThemeStore } from '@/stores/theme'
import { useRouter } from 'vue-router'
import Button from 'primevue/button'

const theme = useThemeStore()
const router = useRouter()

const stats = [
  { label: 'Utilisateurs inscrits', value: 248, icon: 'pi pi-users', color: 'text-bordeaux-600' },
  { label: 'Comptes actifs', value: 231, icon: 'pi pi-check-circle', color: 'text-green-500' },
  { label: 'Comptes désactivés', value: 17, icon: 'pi pi-ban', color: 'text-red-500' },
  { label: 'Responsables', value: 5, icon: 'pi pi-shield', color: 'text-yellow-500' },
]
</script>

<template>
  <div>
    <div class="mb-6">
      <h1 :class="['text-2xl font-bold', theme.isDark ? 'text-bordeaux-100' : 'text-bordeaux-900']">
        Tableau de bord RH
      </h1>
      <p :class="['text-sm', theme.isDark ? 'text-bordeaux-400' : 'text-bordeaux-600']">
        Gestion des comptes utilisateurs
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

    <div class="flex gap-3">
      <Button label="Gérer les utilisateurs" icon="pi pi-users" @click="router.push('/rh/utilisateurs')" />
    </div>
  </div>
</template>

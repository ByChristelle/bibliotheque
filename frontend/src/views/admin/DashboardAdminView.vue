<script setup>
import { useThemeStore } from '@/stores/theme'
import { useRouter } from 'vue-router'
import Button from 'primevue/button'

const theme = useThemeStore()
const router = useRouter()

const stats = [
  { label: 'Références publiées', value: 2400, icon: 'pi pi-book', color: 'text-bordeaux-600' },
  { label: 'Demandes en cours', value: 14, icon: 'pi pi-inbox', color: 'text-yellow-500' },
  { label: 'Utilisateurs', value: 248, icon: 'pi pi-users', color: 'text-blue-500' },
  { label: 'Téléchargements', value: '18k', icon: 'pi pi-download', color: 'text-green-500' },
]

const shortcuts = [
  { label: 'Gérer les demandes', icon: 'pi pi-inbox', to: '/admin/demandes' },
  { label: 'Gérer les références', icon: 'pi pi-book', to: '/admin/references' },
  { label: 'Gérer les utilisateurs', icon: 'pi pi-users', to: '/admin/utilisateurs' },
  { label: 'Voir les logs', icon: 'pi pi-history', to: '/admin/logs' },
]
</script>

<template>
  <div>
    <div class="mb-6">
      <h1 :class="['text-2xl font-bold', theme.isDark ? 'text-bordeaux-100' : 'text-bordeaux-900']">
        Tableau de bord
      </h1>
      <p :class="['text-sm', theme.isDark ? 'text-bordeaux-400' : 'text-bordeaux-600']">
        Vue d'ensemble de la plateforme
      </p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
      <div v-for="stat in stats" :key="stat.label"
        :class="['rounded-2xl p-5 backdrop-blur-xl border',
          theme.isDark ? 'bg-white/5 border-white/10' : 'bg-white/30 border-white/50']">
        <i :class="[stat.icon, stat.color, 'text-2xl mb-2 block']"></i>
        <div :class="['text-2xl font-bold', theme.isDark ? 'text-bordeaux-200' : 'text-bordeaux-800']">{{ stat.value }}</div>
        <div :class="['text-xs', theme.isDark ? 'text-bordeaux-500' : 'text-bordeaux-500']">{{ stat.label }}</div>
      </div>
    </div>

    <!-- Raccourcis -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <div
        v-for="s in shortcuts" :key="s.to"
        :class="['rounded-2xl p-5 backdrop-blur-xl border cursor-pointer transition-all hover:-translate-y-1',
          theme.isDark ? 'bg-white/5 border-white/10 hover:bg-white/10' : 'bg-white/30 border-white/50 hover:bg-white/50']"
        @click="router.push(s.to)">
        <i :class="[s.icon, 'text-xl mb-2 block', theme.isDark ? 'text-bordeaux-400' : 'text-bordeaux-600']"></i>
        <span :class="['text-sm font-medium', theme.isDark ? 'text-bordeaux-300' : 'text-bordeaux-700']">
          {{ s.label }}
        </span>
      </div>
    </div>
  </div>
</template>

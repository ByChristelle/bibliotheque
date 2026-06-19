<script setup>
import { useThemeStore } from '@/stores/theme'
import { useRouter } from 'vue-router'
import Button from 'primevue/button'

const theme = useThemeStore()
const router = useRouter()

const stats = [
  { label: 'Demandes soumises', value: 4, icon: 'pi pi-send', color: 'text-bordeaux-600' },
  { label: 'En attente', value: 1, icon: 'pi pi-clock', color: 'text-yellow-500' },
  { label: 'Validées', value: 2, icon: 'pi pi-check-circle', color: 'text-green-500' },
  { label: 'Refusées', value: 1, icon: 'pi pi-times-circle', color: 'text-red-500' },
]

const recentDemandes = [
  { id: 1, title: 'Introduction aux algorithmes', status: 'validée', date: '12/06/2026' },
  { id: 2, title: 'Droit constitutionnel', status: 'en attente', date: '17/06/2026' },
]

const statusClass = {
  'validée': 'bg-green-100 text-green-700',
  'en attente': 'bg-yellow-100 text-yellow-700',
  'refusée': 'bg-red-100 text-red-700',
  'publiée': 'bg-bordeaux-100 text-bordeaux-700',
}
</script>

<template>
  <div>
    <div class="mb-6">
      <h1 :class="['text-2xl font-bold', theme.isDark ? 'text-bordeaux-100' : 'text-bordeaux-900']">
        Bonjour 👋
      </h1>
      <p :class="['text-sm', theme.isDark ? 'text-bordeaux-400' : 'text-bordeaux-600']">
        Voici un aperçu de votre activité
      </p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
      <div
        v-for="stat in stats" :key="stat.label"
        :class="['rounded-2xl p-5 backdrop-blur-xl border',
          theme.isDark ? 'bg-white/5 border-white/10' : 'bg-white/30 border-white/50']">
        <i :class="[stat.icon, stat.color, 'text-2xl mb-2 block']"></i>
        <div :class="['text-2xl font-bold', theme.isDark ? 'text-bordeaux-200' : 'text-bordeaux-800']">
          {{ stat.value }}
        </div>
        <div :class="['text-xs', theme.isDark ? 'text-bordeaux-500' : 'text-bordeaux-500']">
          {{ stat.label }}
        </div>
      </div>
    </div>

    <!-- Demandes récentes -->
    <div :class="['rounded-2xl p-6 backdrop-blur-xl border',
      theme.isDark ? 'bg-white/5 border-white/10' : 'bg-white/30 border-white/50']">
      <div class="flex items-center justify-between mb-4">
        <h2 :class="['font-semibold', theme.isDark ? 'text-bordeaux-200' : 'text-bordeaux-800']">
          Demandes récentes
        </h2>
        <Button label="Voir tout" text size="small" @click="router.push('/espace/mes-demandes')" />
      </div>

      <div class="flex flex-col gap-3">
        <div
          v-for="d in recentDemandes" :key="d.id"
          :class="['flex items-center justify-between p-3 rounded-xl',
            theme.isDark ? 'bg-white/5' : 'bg-bordeaux-50/50']">
          <div>
            <p :class="['text-sm font-medium', theme.isDark ? 'text-bordeaux-200' : 'text-bordeaux-800']">
              {{ d.title }}
            </p>
            <p :class="['text-xs', theme.isDark ? 'text-bordeaux-500' : 'text-bordeaux-400']">
              {{ d.date }}
            </p>
          </div>
          <span :class="['text-xs px-2.5 py-1 rounded-full font-medium', statusClass[d.status]]">
            {{ d.status }}
          </span>
        </div>
      </div>
    </div>

    <div class="mt-5">
      <Button label="Soumettre une nouvelle référence" icon="pi pi-plus" @click="router.push('/espace/nouvelle-demande')" />
    </div>
  </div>
</template>

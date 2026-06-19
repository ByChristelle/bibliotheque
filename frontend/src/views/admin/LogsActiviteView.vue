<script setup>
import { ref } from 'vue'
import { useThemeStore } from '@/stores/theme'

const theme = useThemeStore()

const logs = ref([
  { id: 1, user: 'Admin', action: 'Publication référence', detail: 'Introduction aux algorithmes', date: '19/06/2026 14:32', type: 'success' },
  { id: 2, user: 'Marie Tall', action: 'Refus demande', detail: 'Droit des contrats — format non conforme', date: '19/06/2026 11:15', type: 'danger' },
  { id: 3, user: 'Jean Kouassi', action: 'Soumission demande', detail: 'Histoire contemporaine', date: '18/06/2026 09:47', type: 'info' },
  { id: 4, user: 'Admin', action: 'Création utilisateur', detail: 'sophie@email.com', date: '17/06/2026 16:20', type: 'info' },
  { id: 5, user: 'Paul Diallo', action: 'Téléchargement', detail: 'Les misérables', date: '17/06/2026 10:05', type: 'info' },
])

const typeIcon = {
  success: 'pi pi-check-circle text-green-500',
  danger: 'pi pi-times-circle text-red-500',
  info: 'pi pi-info-circle text-blue-500',
}
</script>

<template>
  <div>
    <div class="mb-6">
      <h1 :class="['text-2xl font-bold', theme.isDark ? 'text-bordeaux-100' : 'text-bordeaux-900']">
        Journaux d'activité
      </h1>
      <p :class="['text-sm', theme.isDark ? 'text-bordeaux-400' : 'text-bordeaux-600']">
        Historique des actions effectuées sur la plateforme
      </p>
    </div>

    <div :class="['rounded-2xl backdrop-blur-xl border overflow-hidden',
      theme.isDark ? 'bg-white/5 border-white/10' : 'bg-white/30 border-white/50']">
      <div
        v-for="(log, index) in logs" :key="log.id"
        :class="['flex items-start gap-4 p-4 transition-colors',
          index !== logs.length - 1 && (theme.isDark ? 'border-b border-white/5' : 'border-b border-bordeaux-100/50'),
          theme.isDark ? 'hover:bg-white/5' : 'hover:bg-bordeaux-50/30']">
        <i :class="[typeIcon[log.type], 'text-xl mt-0.5 shrink-0']"></i>
        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-2 flex-wrap">
            <span :class="['text-sm font-semibold', theme.isDark ? 'text-bordeaux-200' : 'text-bordeaux-800']">
              {{ log.user }}
            </span>
            <span :class="['text-sm', theme.isDark ? 'text-bordeaux-400' : 'text-bordeaux-600']">
              — {{ log.action }}
            </span>
          </div>
          <p :class="['text-xs truncate', theme.isDark ? 'text-bordeaux-500' : 'text-bordeaux-400']">
            {{ log.detail }}
          </p>
        </div>
        <span :class="['text-xs shrink-0', theme.isDark ? 'text-bordeaux-500' : 'text-bordeaux-400']">
          {{ log.date }}
        </span>
      </div>
    </div>
  </div>
</template>

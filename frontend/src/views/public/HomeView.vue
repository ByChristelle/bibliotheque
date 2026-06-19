<script setup>
import { useThemeStore } from '@/stores/theme'
import { useRouter } from 'vue-router'
import Button from 'primevue/button'

const theme = useThemeStore()
const router = useRouter()

const stats = [
  { label: 'Références', value: '2 400+', icon: 'pi pi-book' },
  { label: 'Utilisateurs', value: '1 200+', icon: 'pi pi-users' },
  { label: 'Téléchargements', value: '18 000+', icon: 'pi pi-download' },
  { label: 'Catégories', value: '32', icon: 'pi pi-tags' },
]
</script>

<template>
  <div>
    <!-- Hero -->
    <section class="max-w-7xl mx-auto px-6 py-20 flex flex-col items-center text-center gap-6">
      <div class="w-16 h-16 rounded-2xl flex items-center justify-center bg-gradient-to-br from-bordeaux-700 to-bordeaux-900 shadow-xl">
        <i class="pi pi-book text-white text-3xl"></i>
      </div>

      <h1 :class="['text-4xl md:text-5xl font-extrabold leading-tight max-w-2xl',
        theme.isDark ? 'text-bordeaux-100' : 'text-bordeaux-900']">
        La bibliothèque numérique à portée de main
      </h1>

      <p :class="['text-lg max-w-xl', theme.isDark ? 'text-bordeaux-400' : 'text-bordeaux-600']">
        Consultez, lisez et téléchargez des milliers de références documentaires depuis n'importe quel appareil.
      </p>

      <div class="flex flex-wrap gap-3 justify-center">
        <Button label="Explorer le catalogue" icon="pi pi-search" size="large" @click="router.push('/catalogue')" />
        <Button label="Créer un compte" icon="pi pi-user-plus" outlined size="large" @click="router.push('/inscription')" />
      </div>
    </section>

    <!-- Stats -->
    <section class="max-w-7xl mx-auto px-6 pb-16">
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div
          v-for="stat in stats"
          :key="stat.label"
          :class="['rounded-2xl p-5 text-center backdrop-blur-xl border',
            theme.isDark ? 'bg-white/5 border-white/10' : 'bg-white/30 border-white/50']">
          <i :class="[stat.icon, 'text-2xl mb-2 block', theme.isDark ? 'text-bordeaux-400' : 'text-bordeaux-600']"></i>
          <div :class="['text-2xl font-bold', theme.isDark ? 'text-bordeaux-200' : 'text-bordeaux-800']">
            {{ stat.value }}
          </div>
          <div :class="['text-sm', theme.isDark ? 'text-bordeaux-400' : 'text-bordeaux-600']">
            {{ stat.label }}
          </div>
        </div>
      </div>
    </section>

    <!-- CTA -->
    <section class="max-w-7xl mx-auto px-6 pb-20">
      <div :class="['rounded-2xl p-10 text-center backdrop-blur-xl border',
        theme.isDark ? 'bg-bordeaux-900/30 border-bordeaux-700/30' : 'bg-bordeaux-700/10 border-bordeaux-300/50']">
        <h2 :class="['text-2xl font-bold mb-3', theme.isDark ? 'text-bordeaux-200' : 'text-bordeaux-800']">
          Vous avez une référence à partager ?
        </h2>
        <p :class="['mb-5 text-sm', theme.isDark ? 'text-bordeaux-400' : 'text-bordeaux-600']">
          Soumettez une demande de dépôt et contribuez à enrichir la collection.
        </p>
        <Button label="Soumettre une référence" icon="pi pi-plus" @click="router.push('/inscription')" />
      </div>
    </section>
  </div>
</template>

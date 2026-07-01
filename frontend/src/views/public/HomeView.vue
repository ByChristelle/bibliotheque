<script setup>
import { useThemeStore } from '@/stores/theme'
import { useRouter } from 'vue-router'
import { ref, onMounted } from 'vue'
import Button from 'primevue/button'
import api from '@/services/axios'

const theme = useThemeStore()
const router = useRouter()

const stats = ref([
  { label: 'Références', value: '0', icon: 'pi pi-book' },
  { label: 'Utilisateurs', value: '0', icon: 'pi pi-users' },
  { label: 'Téléchargements', value: '0', icon: 'pi pi-download' },
  { label: 'Catégories', value: '0', icon: 'pi pi-tags' },
])

const loading = ref(true)

onMounted(async () => {
  try {
    const response = await api.get('/stats')
    // Mettre à jour les stats !
    stats.value[0].value = response.data.stats.references.toLocaleString('fr-FR')
stats.value[1].value = response.data.stats.users.toLocaleString('fr-FR')
stats.value[2].value = response.data.stats.downloads.toLocaleString('fr-FR')
stats.value[3].value = response.data.stats.categories.toLocaleString('fr-FR')
  } catch (error) {
    console.error('Erreur lors du chargement des stats', error)
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <!-- Fond image bibliothèque sur toute la page -->
  <div class="relative min-h-screen">

    <!-- Image de fond fixe -->
    <div
      class="fixed inset-0 z-0 bg-cover bg-center bg-no-repeat"
      style="background-image: url('https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=1920&q=80');"
    ></div>

    <!-- Overlay sombre pour lisibilité -->
    <div
      class="fixed inset-0 z-0"
      :style="theme.isDark
        ? 'background: rgba(10, 0, 5, 0.25)'
        : 'background: rgba(30, 5, 10, 0.40)'"
    ></div>

    <!-- Contenu par-dessus -->
    <div class="relative z-10">

      <!-- Hero -->
      <section class="max-w-7xl mx-auto px-6 py-24 flex flex-col items-center text-center gap-6">
        <!-- <div class="w-16 h-16 rounded-2xl flex items-center justify-center shadow-xl"
          style="background: linear-gradient(135deg, #7A0026, #3d0011)">
          <i class="pi pi-book text-white text-3xl"></i>
        </div> -->

        <div class="w-20 h-20 rounded-xl flex items-center justify-center shrink-0" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.25)">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-10 h-10 text-white">
              <path d="M11.25 4.533A9.707 9.707 0 0 0 6 3a9.735 9.735 0 0 0-3.25.555.75.75 0 0 0-.5.707v14.25a.75.75 0 0 0 1 .707A8.237 8.237 0 0 1 6 18.75c1.995 0 3.823.707 5.25 1.886V4.533ZM12.75 20.636A8.214 8.214 0 0 1 18 18.75c.966 0 1.89.166 2.75.47a.75.75 0 0 0 1-.708V4.262a.75.75 0 0 0-.5-.707A9.735 9.735 0 0 0 18 3a9.707 9.707 0 0 0-5.25 1.533v16.103Z" />
            </svg>
          </div>

        <h1 class="text-4xl md:text-5xl font-extrabold leading-tight max-w-2xl text-white drop-shadow-lg">
          La bibliothèque numérique à portée de main
        </h1>

        <p class="text-lg max-w-xl text-white/80">
          Consultez, lisez et téléchargez des milliers de références documentaires depuis n'importe quel appareil.
        </p>

        <div class="flex flex-wrap gap-3 justify-center">
          <Button label="Explorer le catalogue" icon="pi pi-search" size="large" @click="router.push('/catalogue')" />
          <Button label="Créer un compte" icon="pi pi-user-plus" outlined size="large" @click="router.push('/inscription')"
            style="border-color: rgba(255,255,255,0.5); color: white;" />
        </div>
      </section>

      <!-- Stats -->
      <section class="max-w-7xl mx-auto px-6 pb-16">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <div
            v-for="stat in stats"
            :key="stat.label"
            class="rounded-2xl p-5 text-center border border-white/20"
            style="background: rgba(255,255,255,0.08); backdrop-filter: blur(16px);">
            <i :class="[stat.icon, 'text-2xl mb-2 block text-white/80']"></i>
            <div class="text-2xl font-bold text-white">{{ stat.value }}</div>
            <div class="text-sm text-white/70">{{ stat.label }}</div>
          </div>
        </div>
      </section>

      <!-- CTA -->
      <section class="max-w-7xl mx-auto px-6 pb-24">
        <div class="rounded-2xl p-10 text-center border border-white/20"
          style="background: rgba(122, 0, 38, 0.35); backdrop-filter: blur(20px);">
          <h2 class="text-2xl font-bold mb-3 text-white">
            Vous avez une référence à partager ?
          </h2>
          <p class="mb-5 text-sm text-white/75">
            Soumettez une demande de dépôt et contribuez à enrichir la collection.
          </p>
          <Button label="Soumettre une référence" icon="pi pi-plus" @click="router.push('/inscription')" />
        </div>
      </section>

    </div>
  </div>
</template>

<script setup>
import { useThemeStore } from '@/stores/theme'
import { useRouter } from 'vue-router'
import Button from 'primevue/button'

const theme = useThemeStore()
const router = useRouter()
</script>

<template>
  <div :class="['min-h-screen relative transition-colors duration-300',
    theme.isDark
      ? 'bg-gradient-to-br from-bordeaux-950 via-bordeaux-900 to-bordeaux-950'
      : 'bg-white']">

    <!-- Orbs décoratifs — light: bordeaux discret, dark: bordeaux intense -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
      <div class="absolute -top-40 -right-24 w-[500px] h-[500px] rounded-full"
           :class="theme.isDark ? 'opacity-30 bg-bordeaux-700' : 'opacity-10 bg-bordeaux-700'"
           style="filter: blur(100px)"></div>
      <div class="absolute -bottom-20 -left-20 w-80 h-80 rounded-full"
           :class="theme.isDark ? 'opacity-20 bg-bordeaux-900' : 'opacity-8 bg-bordeaux-600'"
           style="filter: blur(80px)"></div>
    </div>

    <!-- Navbar -->
    <nav :class="['sticky top-0 z-50 border-b backdrop-blur-xl',
      theme.isDark
        ? 'bg-black/20 border-white/10'
        : 'bg-white/90 border-gray-100']">
      <div class="max-w-7xl mx-auto px-6 py-3 flex items-center gap-6">
        <!-- Logo -->
        <div class="flex items-center gap-2 cursor-pointer select-none" @click="router.push('/')">
          <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-gradient-to-br from-bordeaux-700 to-bordeaux-900">
            <i class="pi pi-book text-white text-sm"></i>
          </div>
          <span :class="['font-bold text-lg', theme.isDark ? 'text-bordeaux-200' : 'text-bordeaux-800']">
            BiblioConnect
          </span>
        </div>

        <!-- Liens -->
        <div class="flex gap-6 flex-1">
          <RouterLink
            v-for="link in [{ label: 'Accueil', to: '/' }, { label: 'Catalogue', to: '/catalogue' }]"
            :key="link.to"
            :to="link.to"
            :class="['text-sm font-medium pb-0.5 border-b-2 transition-colors no-underline',
              theme.isDark
                ? 'text-bordeaux-300 border-transparent hover:text-bordeaux-200 hover:border-bordeaux-400'
                : 'text-gray-600 border-transparent hover:text-bordeaux-700 hover:border-bordeaux-600']"
            activeClass="!border-bordeaux-600 !text-bordeaux-700"
          >
            {{ link.label }}
          </RouterLink>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-2">
          <Button
            :icon="theme.isDark ? 'pi pi-sun' : 'pi pi-moon'"
            rounded text size="small"
            @click="theme.toggle()"
          />
          <Button label="Connexion" outlined size="small" @click="router.push('/connexion')" />
          <Button label="Inscription" size="small" @click="router.push('/inscription')" />
        </div>
      </div>
    </nav>

    <!-- Contenu -->
    <main class="relative z-10">
      <RouterView />
    </main>
  </div>
</template>

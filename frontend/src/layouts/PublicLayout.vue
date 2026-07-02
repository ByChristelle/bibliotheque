<script setup>
import { useThemeStore } from '@/stores/theme'
import { useRouter } from 'vue-router'

const theme = useThemeStore()
const router = useRouter()

const links = [
  { label: 'Accueil', to: '/' },
  { label: 'Catalogue', to: '/catalogue' },
]
</script>

<template>
  <div :class="['min-h-screen transition-colors duration-300', theme.isDark ? 'bg-bordeaux-950 text-white' : 'bg-[#F8F6F6] text-[#2D2D2D]']">

    <!-- Navbar transparente -->
    <nav class="sticky top-0 z-50 border-b border-white/10 backdrop-blur-md" style="background: rgba(0,0,0,0.25)">
      <div class="max-w-7xl mx-auto px-6 h-16 flex items-center gap-8">

        <!-- Logo -->
        <div class="flex items-center gap-2.5 cursor-pointer select-none shrink-0" @click="router.push('/')">
          <!-- Icône livre SVG -->
          <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.25)">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-white">
              <path d="M11.25 4.533A9.707 9.707 0 0 0 6 3a9.735 9.735 0 0 0-3.25.555.75.75 0 0 0-.5.707v14.25a.75.75 0 0 0 1 .707A8.237 8.237 0 0 1 6 18.75c1.995 0 3.823.707 5.25 1.886V4.533ZM12.75 20.636A8.214 8.214 0 0 1 18 18.75c.966 0 1.89.166 2.75.47a.75.75 0 0 0 1-.708V4.262a.75.75 0 0 0-.5-.707A9.735 9.735 0 0 0 18 3a9.707 9.707 0 0 0-5.25 1.533v16.103Z" />
            </svg>
          </div>
          <span class="font-bold text-base text-white">BiblioConnecthghg</span>
        </div>

        <!-- Liens -->
        <div class="flex gap-1 flex-1">
          <RouterLink
            v-for="link in links" :key="link.to" :to="link.to"
            class="px-3 py-1.5 rounded-lg text-sm font-medium no-underline transition-all duration-200 text-white/80 hover:text-white hover:bg-white/10"
            activeClass="!text-white !bg-white/15"
          >
            {{ link.label }}
          </RouterLink>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-2">
          <button
            class="w-8 h-8 rounded-lg flex items-center justify-center transition-all duration-200 text-white/70 hover:bg-white/10 hover:text-white"
            @click="theme.toggle()"
          >
            <i :class="theme.isDark ? 'pi pi-sun text-sm' : 'pi pi-moon text-sm'"></i>
          </button>
          <button
            class="px-4 py-1.5 rounded-xl text-sm font-medium border border-white/30 text-white hover:bg-white/10 transition-all duration-200"
            @click="router.push('/connexion')"
          >
            Connexion
          </button>
          <button
            class="px-4 py-1.5 rounded-xl text-sm font-medium text-white transition-all duration-200 hover:opacity-90"
            style="background: rgba(122,0,38,0.85)"
            @click="router.push('/inscription')"
          >
            Inscription
          </button>
        </div>
      </div>
    </nav>

    <main>
      <RouterView />
    </main>
  </div>
</template>

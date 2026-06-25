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

    <!-- Navbar -->
    <nav :class="['sticky top-0 z-50 border-b transition-all duration-300',
      theme.isDark ? 'bg-bordeaux-950/90 border-white/10 backdrop-blur-xl' : 'bg-white border-[#ECECEC] shadow-sm']">
      <div class="max-w-7xl mx-auto px-6 h-16 flex items-center gap-8">

        <!-- Logo -->
        <div class="flex items-center gap-2.5 cursor-pointer select-none shrink-0" @click="router.push('/')">
          <div class="w-8 h-8 rounded-xl flex items-center justify-center bg-[#7A0026]">
            <i class="pi pi-book text-white text-sm"></i>
          </div>
          <span :class="['font-bold text-base', theme.isDark ? 'text-white' : 'text-[#7A0026]']">
            BiblioConnect
          </span>
        </div>

        <!-- Liens -->
        <div class="flex gap-1 flex-1">
          <RouterLink
            v-for="link in links" :key="link.to" :to="link.to"
            :class="['px-3 py-1.5 rounded-lg text-sm font-medium no-underline transition-all duration-200',
              theme.isDark ? 'text-white/70 hover:text-white hover:bg-white/10' : 'text-[#777] hover:text-[#7A0026] hover:bg-[#E8D6DB]']"
            activeClass="!text-[#7A0026] !bg-[#E8D6DB]"
          >
            {{ link.label }}
          </RouterLink>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-2">
          <button
            :class="['w-8 h-8 rounded-lg flex items-center justify-center transition-all duration-200',
              theme.isDark ? 'text-white/60 hover:bg-white/10 hover:text-white' : 'text-[#777] hover:bg-[#ECECEC] hover:text-[#2D2D2D]']"
            @click="theme.toggle()"
          >
            <i :class="theme.isDark ? 'pi pi-sun text-sm' : 'pi pi-moon text-sm'"></i>
          </button>
          <button
            :class="['px-4 py-1.5 rounded-xl text-sm font-medium border transition-all duration-200',
              theme.isDark ? 'border-white/20 text-white hover:bg-white/10' : 'border-[#7A0026] text-[#7A0026] hover:bg-[#E8D6DB]']"
            @click="router.push('/connexion')"
          >
            Connexion
          </button>
          <button
            class="px-4 py-1.5 rounded-xl text-sm font-medium bg-[#7A0026] text-white hover:bg-[#5e001c] transition-all duration-200 shadow-sm hover:shadow-md"
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

<script setup>
import { ref, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useThemeStore } from '@/stores/theme'
import Button from 'primevue/button'
import { useAuthStore } from '@/stores/auth'

const theme = useThemeStore()
const router = useRouter()
const route = useRoute()
const auth =useAuthStore()

const sidebarOpen = ref(true)

const menus = {
  user: [
    { label: 'Tableau de bord', icon: 'pi pi-home', to: '/espace/dashboard' },
    { label: 'Mes demandes', icon: 'pi pi-list', to: '/espace/mes-demandes' },
    { label: 'Nouvelle demande', icon: 'pi pi-plus-circle', to: '/espace/nouvelle-demande' },
  ],
  responsable: [
    { label: 'Tableau de bord', icon: 'pi pi-home', to: '/responsable/dashboard' },
    { label: 'Demandes affectées', icon: 'pi pi-inbox', to: '/responsable/demandes' },
  ],
  rh: [
    { label: 'Tableau de bord', icon: 'pi pi-home', to: '/rh/dashboard' },
    { label: 'Utilisateurs', icon: 'pi pi-users', to: '/rh/utilisateurs' },
  ],
  admin: [
    { label: 'Tableau de bord', icon: 'pi pi-home', to: '/admin/dashboard' },
    { label: 'Demandes', icon: 'pi pi-inbox', to: '/admin/demandes' },
    { label: 'Références', icon: 'pi pi-book', to: '/admin/references' },
    { label: 'Utilisateurs', icon: 'pi pi-users', to: '/admin/utilisateurs' },
    { label: 'Catégories', icon: 'pi pi-tags', to: '/admin/categories' },
    { label: "Journaux d'activité", icon: 'pi pi-history', to: '/admin/logs' },
  ],
}

// TODO: remplacer par le store auth
const role = ref('admin')
const navItems = computed(() => menus[role.value] ?? menus.user)
</script>

<template>
  <div :class="['flex min-h-screen relative transition-colors duration-300',
    theme.isDark
      ? 'bg-gradient-to-br from-bordeaux-950 via-bordeaux-900 to-bordeaux-950'
      : 'bg-gray-50']">

    <!-- Orbs -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
      <div class="absolute -top-32 -right-32 w-[450px] h-[450px] rounded-full"
           :class="theme.isDark ? 'opacity-20 bg-bordeaux-700' : 'opacity-5 bg-bordeaux-700'"
           style="filter: blur(100px)"></div>
    </div>

    <!-- Sidebar -->
    <aside :class="['fixed left-0 top-0 bottom-0 z-50 flex flex-col border-r transition-all duration-300',
      sidebarOpen ? 'w-60' : 'w-16',
      theme.isDark ? 'bg-bordeaux-950/95 border-white/10' : 'bg-white border-gray-200']">

      <!-- Header -->
      <div :class="['flex items-center p-3 border-b',
        theme.isDark ? 'border-white/10' : 'border-gray-100']">
        <div v-if="sidebarOpen" class="flex items-center gap-2 flex-1 overflow-hidden">
          <div class="w-7 h-7 rounded-lg flex items-center justify-center bg-gradient-to-br from-bordeaux-700 to-bordeaux-900 shrink-0">
            <i class="pi pi-book text-white text-xs"></i>
          </div>
          <span :class="['font-bold text-sm truncate', theme.isDark ? 'text-bordeaux-200' : 'text-bordeaux-800']">
            BiblioConnect
          </span>
        </div>
        <Button
          :icon="sidebarOpen ? 'pi pi-chevron-left' : 'pi pi-chevron-right'"
          rounded text size="small"
          @click="sidebarOpen = !sidebarOpen"
        />
      </div>

      <!-- Navigation -->
      <nav class="flex-1 p-2 flex flex-col gap-1 overflow-y-auto">
        <RouterLink
          v-for="item in navItems"
          :key="item.to"
          :to="item.to"
          :title="!sidebarOpen ? item.label : ''"
          :class="['flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all no-underline',
            theme.isDark
              ? 'text-bordeaux-300 hover:bg-white/10 hover:text-bordeaux-100'
              : 'text-gray-600 hover:bg-bordeaux-50 hover:text-bordeaux-700',
            !sidebarOpen && 'justify-center']"
          activeClass="!bg-bordeaux-50 !text-bordeaux-700 font-semibold"
        >
          <i :class="[item.icon, 'shrink-0']"></i>
          <span v-if="sidebarOpen" class="truncate">{{ item.label }}</span>
        </RouterLink>
      </nav>

      <!-- Footer -->
      <div :class="['p-2 border-t flex items-center gap-2',
        theme.isDark ? 'border-white/10' : 'border-gray-100']">
        <Button
          :icon="theme.isDark ? 'pi pi-sun' : 'pi pi-moon'"
          rounded text size="small"
          @click="theme.toggle()"
        />
        <Button
          v-if="sidebarOpen"
          label="Déconnexion"
          icon="pi pi-sign-out"
          text severity="danger" size="small"
          @click="auth.logout()"
        />
        <!-- <Button
          v-else
          icon="pi pi-sign-out"
          rounded text severity="danger" size="small"
          @click="router.push('/connexion')"
        /> -->
      </div>
    </aside>

    <!-- Contenu -->
    <div :class="['flex-1 flex flex-col relative z-10 transition-all duration-300',
      sidebarOpen ? 'ml-60' : 'ml-16']">

      <!-- Topbar -->
      <header :class="['sticky top-0 z-40 flex items-center justify-between px-6 py-3 border-b',
        theme.isDark ? 'bg-bordeaux-950/80 backdrop-blur-xl border-white/10' : 'bg-white border-gray-200']">
        <h2 :class="['text-base font-semibold', theme.isDark ? 'text-bordeaux-200' : 'text-gray-800']">
          {{ route.meta.title ?? 'Tableau de bord' }}
        </h2>
        <div class="flex items-center gap-2">
          <div :class="['w-8 h-8 rounded-full flex items-center justify-center',
            theme.isDark ? 'bg-bordeaux-800/60 text-bordeaux-300' : 'bg-bordeaux-100 text-bordeaux-700']">
            <i class="pi pi-user text-sm"></i>
          </div>
          <span :class="['text-sm font-medium', theme.isDark ? 'text-bordeaux-300' : 'text-gray-700']">
            Utilisateur
          </span>
        </div>
      </header>

      <main class="flex-1 p-6">
        <RouterView />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useThemeStore } from '@/stores/theme'
import Button from 'primevue/button'
import { useAuthStore } from '@/stores/auth'

const theme = useThemeStore()
const router = useRouter()
const route = useRoute()
const auth = useAuthStore()

const sidebarOpen = ref(true)

// Liste des menus avec les configurations SVG (les chemins path sont affichés directement dans le template)
const menus = {
  user: [
    { label: 'Tableau de bord', icon: 'home', to: '/espace/dashboard' },
    { label: 'Mes demandes', icon: 'list', to: '/espace/mes-demandes' },
    { label: 'Nouvelle demande', icon: 'plus-circle', to: '/espace/nouvelle-demande' },
  ],
  responsable: [
    { label: 'Tableau de bord', icon: 'home', to: '/responsable/dashboard' },
    { label: 'Demandes affectées', icon: 'inbox', to: '/responsable/demandes' },
  ],
  rh: [
    { label: 'Tableau de bord', icon: 'home', to: '/rh/dashboard' },
    { label: 'Utilisateurs', icon: 'users', to: '/rh/utilisateurs' },
  ],
  admin: [
    { label: 'Tableau de bord', icon: 'home', to: '/admin/dashboard' },
    { label: 'Demandes', icon: 'inbox', to: '/admin/demandes' },
    { label: 'Références', icon: 'book', to: '/admin/references' },
    { label: 'Utilisateurs', icon: 'users', to: '/admin/utilisateurs' },
    { label: 'Catégories', icon: 'tags', to: '/admin/categories' },
    { label: "Journaux d'activité", icon: 'history', to: '/admin/logs' },
  ],
}

// Utilisation dynamique du rôle présent dans le store d'authentification (avec repli sur 'user')
const currentRole = computed(() => auth.user?.role || 'admin')
const navItems = computed(() => menus[currentRole.value] ?? menus.user)
</script>

<template>
  <div :class="['flex min-h-screen relative transition-colors duration-300',
    theme.isDark
      ? 'bg-gradient-to-br from-bordeaux-950 via-bordeaux-900 to-bordeaux-950 text-white'
      : 'bg-gray-50 text-gray-900']">

    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
      <div class="absolute -top-32 -right-32 w-[450px] h-[450px] rounded-full"
           :class="theme.isDark ? 'opacity-20 bg-bordeaux-700' : 'opacity-5 bg-bordeaux-700'"
           style="filter: blur(100px)"></div>
    </div>

    <aside :class="['fixed left-0 top-0 bottom-0 z-50 flex flex-col border-r transition-all duration-300',
      sidebarOpen ? 'w-60' : 'w-16',
      theme.isDark ? 'bg-bordeaux-950/95 border-white/10' : 'bg-white border-gray-200']">

      <div :class="['flex items-center p-3 h-[57px] border-b',
        theme.isDark ? 'border-white/10' : 'border-gray-100']">
        <div v-if="sidebarOpen" class="flex items-center gap-2 flex-1 overflow-hidden">
          <div class="w-8 h-8 rounded-xl flex items-center justify-center bg-gradient-to-br from-bordeaux-600 to-bordeaux-800 shrink-0 shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-white">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9s2.015-9 4.5-9m0 0a9.015 9.015 0 0 1 8.716 2.253M12 3a9.015 9.015 0 0 0-8.716 2.253" />
            </svg>
          </div>
          <span :class="['font-bold text-sm truncate tracking-wide', theme.isDark ? 'text-bordeaux-100' : 'text-bordeaux-950']">
            BiblioConnect
          </span>
        </div>
        <Button
          :icon="sidebarOpen ? 'pi pi-chevron-left' : 'pi pi-chevron-right'"
          rounded text size="small"
          :class="theme.isDark ? 'text-bordeaux-400 hover:text-bordeaux-100' : 'text-gray-500'"
          @click="sidebarOpen = !sidebarOpen"
        />
      </div>

      <nav class="flex-1 p-2 flex flex-col gap-1 overflow-y-auto custom-scrollbar">
        <RouterLink
          v-for="item in navItems"
          :key="item.to"
          :to="item.to"
          custom
          v-slot="{ href, navigate, isActive }"
        >
          <a
            :href="href"
            @click="navigate"
            :title="!sidebarOpen ? item.label : ''"
            :class="[
              'group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 no-underline relative',
              // Styles si la rubrique est ACTIVE
              isActive 
                ? (theme.isDark ? 'bg-bordeaux-600/30 text-bordeaux-100 font-semibold shadow-sm' : 'bg-bordeaux-50 text-bordeaux-800 font-semibold')
                : (theme.isDark ? 'text-bordeaux-300 hover:bg-white/5 hover:text-bordeaux-100' : 'text-gray-600 hover:bg-gray-100/80 hover:text-bordeaux-900'),
              !sidebarOpen && 'justify-center'
            ]"
          >
            <div v-if="isActive" class="absolute left-0 top-3 bottom-3 w-1 rounded-r-md bg-bordeaux-600"></div>

            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" 
              :class="['w-5 h-5 shrink-0 transition-colors duration-200', isActive ? 'text-bordeaux-600 dark:text-bordeaux-400' : 'text-current opacity-70 group-hover:opacity-100']">
              
              <path v-if="item.icon === 'home'" stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
              
              <path v-else-if="item.icon === 'list'" stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.007 5.25H3.75v.008h.008V12Zm0 5.25H3.75v.008h.008v-.008Z" />
              
              <path v-else-if="item.icon === 'plus-circle'" stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
              
              <path v-else-if="item.icon === 'inbox'" stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 0 1 2.008 1.24l.885 1.77a2.25 2.25 0 0 0 2.007 1.24h1.98a2.25 2.25 0 0 0 2.007-1.24l.885-1.77a2.25 2.25 0 0 1 2.007-1.24h3.86m-18 0h18m-18 0v-2.25A2.25 2.25 0 0 1 4.5 9h15A2.25 2.25 0 0 1 21.75 11.25v2.25m-18 0v6a2.25 2.25 0 0 0 2.25 2.25h15a2.25 2.25 0 0 0 2.25-2.25v-6m-18 0A2.25 2.25 0 0 1 4.5 9H8.12a2.25 2.25 0 0 1 1.877-.994h4.006a2.25 2.25 0 0 1 1.877.994h3.62A2.25 2.25 0 0 1 21.75 11.25v2.25" />
              
              <path v-else-if="item.icon === 'users'" stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
              
              <path v-else-if="item.icon === 'book'" stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
              
              <path v-else-if="item.icon === 'tags'" stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581a1.947 1.947 0 0 0 2.754 0l4.318-4.318a1.947 1.947 0 0 0 0-2.754L11.16 3.659A2.25 2.25 0 0 0 9.568 3Z M6 6h.008v.008H6V6Z" />
              
              <path v-else-if="item.icon === 'history'" stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>

            <span v-if="sidebarOpen" class="truncate tracking-wide">{{ item.label }}</span>
          </a>
        </RouterLink>
      </nav>

      <div :class="['p-2 border-t flex flex-col gap-1',
        theme.isDark ? 'border-white/10' : 'border-gray-100']">
        
        <div class="flex items-center justify-between w-full gap-2">
          <Button
            :icon="theme.isDark ? 'pi pi-sun' : 'pi pi-moon'"
            rounded text size="small"
            :class="theme.isDark ? 'text-bordeaux-400 hover:text-bordeaux-100' : 'text-gray-500'"
            @click="theme.toggle()"
          />
          <Button
            v-if="sidebarOpen"
            label="Déconnexion"
            icon="pi pi-sign-out"
            text severity="danger" size="small"
            class="flex-1 justify-start rounded-xl"
            @click="auth.logout()"
          />
          <Button
            v-else
            icon="pi pi-sign-out"
            rounded text severity="danger" size="small"
            @click="auth.logout()"
          />
        </div>
      </div>
    </aside>

    <div :class="['flex-1 flex flex-col relative z-10 transition-all duration-300',
      sidebarOpen ? 'ml-60' : 'ml-16']">

      <header :class="['sticky top-0 z-40 flex items-center justify-between px-6 py-3 border-b h-[57px]',
        theme.isDark ? 'bg-bordeaux-950/80 backdrop-blur-xl border-white/10' : 'bg-white border-gray-200 shadow-sm']">
        <h2 :class="['text-sm font-semibold tracking-wide', theme.isDark ? 'text-bordeaux-200' : 'text-gray-800']">
          {{ route.meta.title ?? 'Tableau de bord' }}
        </h2>
        <div class="flex items-center gap-2">
          <div :class="['w-7 h-7 rounded-full flex items-center justify-center shadow-inner',
            theme.isDark ? 'bg-bordeaux-800/60 text-bordeaux-200' : 'bg-bordeaux-100 text-bordeaux-800']">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
            </svg>
          </div>
          <span :class="['text-xs font-semibold tracking-wide', theme.isDark ? 'text-bordeaux-300' : 'text-gray-700']">
            {{ auth.user?.first_name || 'Utilisateur' }}
          </span>
        </div>
      </header>

      <main class="flex-1 p-6">
        <RouterView />
      </main>
    </div>
  </div>
</template>

<style scoped>
/* Petit nettoyage visuel de la scrollbar pour les longs menus */
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 10px;
}
</style>
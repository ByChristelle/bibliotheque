<script setup>
import { ref, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useThemeStore } from '@/stores/theme'
import { useAuthStore } from '@/stores/auth'

const theme = useThemeStore()
const route  = useRoute()
const auth   = useAuthStore()
const router = useRouter()

const sidebarOpen = ref(true)

const menus = {
  user: [
    { label: 'Tableau de bord',  icon: 'pi pi-home',        to: '/espace/dashboard' },
    { label: 'Mes demandes',     icon: 'pi pi-list',         to: '/espace/mes-demandes' },
    { label: 'Nouvelle demande', icon: 'pi pi-plus-circle',  to: '/espace/nouvelle-demande' },
    { label: 'Références',         icon: 'pi pi-book',    to: '/admin/references' },
    
  ],
  responsable_demande: [
    { label: 'Tableau de bord',    icon: 'pi pi-home',   to: '/responsable/dashboard' },
    { label: 'Demandes affectées', icon: 'pi pi-inbox',  to: '/responsable/demandes' },
    { label: 'Références',         icon: 'pi pi-book',    to: '/admin/references' },
  ],
  responsable_rh: [
    { label: 'Tableau de bord', icon: 'pi pi-home',  to: '/rh/dashboard' },
    { label: 'Utilisateurs',    icon: 'pi pi-users', to: '/rh/utilisateurs' },
    { label: 'Références',         icon: 'pi pi-book',    to: '/admin/references' },

  ],
  admin: [
    { label: 'Tableau de bord',    icon: 'pi pi-home',    to: '/admin/dashboard' },
    { label: 'Demandes',           icon: 'pi pi-inbox',   to: '/admin/demandes' },
    { label: 'Références',         icon: 'pi pi-book',    to: '/admin/references' },
    { label: 'Utilisateurs',       icon: 'pi pi-users',   to: '/admin/utilisateurs' },
    { label: 'Catégories',         icon: 'pi pi-tags',    to: '/admin/categories' },
    { label: "Journaux d'activité",icon: 'pi pi-history', to: '/admin/logs' },
  ],
}

const currentRole = computed(() => auth.user?.role || 'user')
const navItems    = computed(() => menus[currentRole.value] ?? menus.user)
const userName    = computed(() => auth.user ? `${auth.user.first_name} ${auth.user.last_name}` : 'Utilisateur')
const userInitials= computed(() => {
  if (!auth.user) return 'U'
  return `${auth.user.first_name?.[0] ?? ''}${auth.user.last_name?.[0] ?? ''}`.toUpperCase()
})
</script>

<template>
  <div :class="['flex min-h-screen transition-colors duration-300',
    theme.isDark ? 'bg-bordeaux-950' : 'bg-[#F8F6F6]']">

    <!-- ── Sidebar ── -->
    <aside :class="['fixed left-0 top-0 bottom-0 z-50 flex flex-col transition-all duration-300 border-r',
      sidebarOpen ? 'w-64' : 'w-[68px]',
      theme.isDark ? 'bg-bordeaux-950 border-white/10' : 'bg-[#F3EFEF] border-[#E0D8D8]']">

      <!-- Header sidebar -->
      <div class="flex items-center h-16 px-4 gap-3 shrink-0">
        <div class="w-8 h-8 rounded-xl flex items-center justify-center bg-white/15 shrink-0">
          <i class="pi pi-book text-white text-sm"></i>
        </div>
        <span v-if="sidebarOpen" class="font-bold text-sm truncate tracking-wide"
          :class="theme.isDark ? 'text-white' : 'text-[#7A0026]'">
          BiblioConnect
        </span>
        <button
          class="ml-auto w-7 h-7 rounded-lg flex items-center justify-center transition-all duration-200"
          :class="theme.isDark ? 'text-white/60 hover:text-white hover:bg-white/10' : 'text-[#777] hover:text-[#7A0026] hover:bg-[#F8F6F6]'"
          @click="sidebarOpen = !sidebarOpen"
        >
          <i :class="sidebarOpen ? 'pi pi-chevron-left text-xs' : 'pi pi-chevron-right text-xs'"></i>
        </button>
      </div>

      <!-- Navigation -->
      <nav class="flex-1 px-2 py-3 flex flex-col gap-0.5 overflow-y-auto">
        <RouterLink
          v-for="item in navItems" :key="item.to" :to="item.to"
          custom v-slot="{ href, navigate, isActive }"
        >
          <a :href="href" @click="navigate" :title="!sidebarOpen ? item.label : ''"
            :class="['flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium no-underline transition-all duration-200',
              isActive
                ? (theme.isDark ? 'bg-white/15 text-white font-semibold' : 'bg-[#7A0026]/10 text-[#7A0026] font-semibold')
                : (theme.isDark ? 'text-white/75 hover:bg-white/10 hover:text-white' : 'text-[#777] hover:bg-[#F8F6F6] hover:text-[#7A0026]'),
              !sidebarOpen && 'justify-center']">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"
              :class="['w-5 h-5 shrink-0 transition-colors duration-200',
                isActive ? (theme.isDark ? 'text-white' : 'text-[#7A0026]') : 'opacity-70']">
              <path v-if="item.icon === 'pi pi-home'" stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
              <path v-else-if="item.icon === 'pi pi-list'" stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.007 5.25H3.75v.008h.008V12Zm0 5.25H3.75v.008h.008v-.008Z" />
              <path v-else-if="item.icon === 'pi pi-plus-circle'" stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
              <path v-else-if="item.icon === 'pi pi-inbox'" stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 0 1 2.008 1.24l.885 1.77a2.25 2.25 0 0 0 2.007 1.24h1.98a2.25 2.25 0 0 0 2.007-1.24l.885-1.77a2.25 2.25 0 0 1 2.007-1.24h3.86M2.25 13.5V6a2.25 2.25 0 0 1 2.25-2.25h15A2.25 2.25 0 0 1 21.75 6v7.5" />
              <path v-else-if="item.icon === 'pi pi-users'" stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
              <path v-else-if="item.icon === 'pi pi-book'" stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
              <path v-else-if="item.icon === 'pi pi-tags'" stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581a1.947 1.947 0 0 0 2.754 0l4.318-4.318a1.947 1.947 0 0 0 0-2.754L11.16 3.659A2.25 2.25 0 0 0 9.568 3Z M6 6h.008v.008H6V6Z" />
              <path v-else-if="item.icon === 'pi pi-history'" stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
              <path v-else stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>            <span v-if="sidebarOpen" class="truncate">{{ item.label }}</span>
          </a>
        </RouterLink>
      </nav>

      <!-- Footer sidebar -->
      <div class="px-2 pb-4 pt-2 border-t flex flex-col gap-1"
        :class="theme.isDark ? 'border-white/10' : 'border-[#ECECEC]'">
        <button
          :class="['flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 w-full',
            !sidebarOpen && 'justify-center',
            theme.isDark ? 'text-white/60 hover:bg-white/10 hover:text-white' : 'text-[#777] hover:bg-[#F8F6F6] hover:text-[#2D2D2D]']"
          @click="theme.toggle()">
          <i :class="[theme.isDark ? 'pi pi-sun' : 'pi pi-moon', 'text-base shrink-0']"></i>
          <span v-if="sidebarOpen">{{ theme.isDark ? 'Mode clair' : 'Mode sombre' }}</span>
        </button>
        <button
          :class="['flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 w-full',
            !sidebarOpen && 'justify-center',
            theme.isDark ? 'text-red-300 hover:text-white hover:bg-red-600/20' : 'text-red-500 hover:bg-red-50']"
          @click="auth.logout()">
          <i class="pi pi-sign-out text-base shrink-0"></i>
          <span v-if="sidebarOpen">Déconnexion</span>
        </button>
      </div>
    </aside>

    <!-- ── Contenu principal ── -->
    <div :class="['flex-1 flex flex-col transition-all duration-300 min-w-0',
      sidebarOpen ? 'ml-64' : 'ml-[68px]']">

      <!-- Topbar -->
      <header :class="['sticky top-0 z-40 h-16 flex items-center justify-between px-6 border-b transition-colors duration-300',
        theme.isDark ? 'bg-bordeaux-950/90 border-white/10 backdrop-blur-xl' : 'bg-white border-[#ECECEC] shadow-sm']">
        <div>
          <h1 :class="['text-sm font-semibold', theme.isDark ? 'text-white' : 'text-[#2D2D2D]']">
            {{ route.meta.title ?? 'Tableau de bord' }}
          </h1>
        </div>
        <div class="flex items-center gap-3">
          <!-- Notifications placeholder -->
          <button :class="['w-9 h-9 rounded-xl flex items-center justify-center transition-all duration-200',
            theme.isDark ? 'text-white/60 hover:bg-white/10 hover:text-white' : 'text-[#777] hover:bg-[#ECECEC] hover:text-[#2D2D2D]']">
            <i class="pi pi-bell text-sm"></i>
          </button>
          <!-- Avatar -->
          <div class="flex items-center gap-2.5">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center bg-[#7A0026] text-white text-xs font-bold shadow-sm">
              {{ userInitials }}
            </div>
            <div class="hidden sm:block">
              <p :class="['text-xs font-semibold leading-none mb-0.5', theme.isDark ? 'text-white' : 'text-[#2D2D2D]']">
                {{ userName }}
              </p>
              <p :class="['text-xs leading-none capitalize', theme.isDark ? 'text-white/50' : 'text-[#777]']">
                {{ currentRole }}
              </p>
            </div>
          </div>
        </div>
      </header>

      <!-- Page content -->
      <main class="flex-1 p-6">
        <RouterView />
      </main>
    </div>
  </div>
</template>

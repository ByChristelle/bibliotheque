<script setup>
import { computed, onMounted } from 'vue'
import { useThemeStore } from '@/stores/theme'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useDepositRequestsStore } from '@/stores/depositRequests'
import Button from 'primevue/button'
import { Loader2 } from 'lucide-vue-next'

const theme        = useThemeStore()
const router       = useRouter()
const auth         = useAuthStore()
const depositStore = useDepositRequestsStore()
import { ref } from 'vue'

onMounted(async () => {
  await depositStore.fetchMyRequests()
})

// Stats calculées dynamiquement
const total     = computed(() => depositStore.myRequests.length)
const pending   = computed(() => depositStore.myRequests.filter(d => d.status === 'pending').length)
const validated = computed(() => depositStore.myRequests.filter(d => ['approved_by_manager', 'approved', 'published'].includes(d.status)).length)
const rejected  = computed(() => depositStore.myRequests.filter(d => ['rejected_by_manager', 'rejected'].includes(d.status)).length)

// 3 dernières demandes
const recentDemandes = computed(() => depositStore.myRequests.slice(0, 3))

const statusLabel = {
  pending:             'En attente',
  approved_by_manager: 'Validée',
  rejected_by_manager: 'Refusée',
  published:           'Publiée',
  rejected:            'Rejetée',
}
const statusClass = {
  pending:             'bg-yellow-100 text-yellow-700',
  approved_by_manager: 'bg-green-100 text-green-700',
  rejected_by_manager: 'bg-red-100 text-red-700',
  published:           'bg-blue-100 text-blue-700',
  rejected:            'bg-red-100 text-red-700',
}

const showInactifDialog = ref(false)

function allerNouvelledemande() {
  if (auth.user?.status === 'inactive') {
    showInactifDialog.value = true
    return
  }
  router.push('/espace/nouvelle-demande')
}

</script>

<template>
  <div>
    <!-- Salutation -->
    <div class="mb-6">
      <h1 :class="['text-2xl font-bold', theme.isDark ? 'text-bordeaux-100' : 'text-bordeaux-900']">
        Bonjour, {{ auth.user?.first_name }} 
      </h1>
      <p :class="['text-sm', theme.isDark ? 'text-bordeaux-400' : 'text-bordeaux-600']">
        Voici un aperçu de votre activité
      </p>
    </div>

    <!-- Loader -->
    <div v-if="depositStore.loading" class="flex items-center justify-center py-12">
      <Loader2 class="w-8 h-8 animate-spin text-bordeaux-600" />
      <span class="ml-3 text-sm text-[#94A3B8]">Chargement...</span>
    </div>

    <template v-else>
      <!-- Stats -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div v-for="stat in [
          { label: 'Demandes soumises', value: total,     icon: 'pi pi-send',         color: 'text-[#7A0026]' },
          { label: 'En attente',         value: pending,   icon: 'pi pi-clock',         color: 'text-yellow-500' },
          { label: 'Validées',           value: validated, icon: 'pi pi-check-circle',  color: 'text-green-500' },
          { label: 'Refusées',           value: rejected,  icon: 'pi pi-times-circle',  color: 'text-red-500' },
        ]" :key="stat.label"
          :class="['rounded-2xl p-5 border transition-all duration-200',
            theme.isDark ? 'bg-white/5 border-white/10' : 'bg-white border-[#ECECEC] shadow-sm']">
          <i :class="[stat.icon, stat.color, 'text-2xl mb-2 block']"></i>
          <div :class="['text-3xl font-bold', theme.isDark ? 'text-white' : 'text-[#2D2D2D]']">
            {{ stat.value }}
          </div>
          <div class="text-xs text-[#94A3B8] mt-0.5">{{ stat.label }}</div>
        </div>
      </div>

      <!-- Demandes récentes -->
      <div :class="['rounded-2xl p-6 border', theme.isDark ? 'bg-white/5 border-white/10' : 'bg-white border-[#ECECEC] shadow-sm']">
        <div class="flex items-center justify-between mb-4">
          <h2 :class="['font-semibold text-sm', theme.isDark ? 'text-white' : 'text-[#2D2D2D]']">
            Demandes récentes
          </h2>
          <Button label="Voir tout" text size="small" @click="router.push('/espace/mes-demandes')" />
        </div>

        <!-- Vide -->
        <div v-if="!recentDemandes.length" class="text-center py-8">
          <i class="pi pi-inbox text-3xl text-[#94A3B8] mb-2 block"></i>
          <p class="text-sm text-[#94A3B8]">Aucune demande soumise pour le moment</p>
        </div>

        <div v-else class="flex flex-col gap-2.5">
          <div v-for="d in recentDemandes" :key="d.id"
            :class="['flex items-center justify-between p-3 rounded-xl transition-all',
              theme.isDark ? 'bg-white/5 hover:bg-white/10' : 'bg-[#F8F6F6] hover:bg-[#F0ECEC]']">
            <div class="min-w-0 flex-1">
              <p class="text-sm font-medium truncate" :class="theme.isDark ? 'text-white' : 'text-[#2D2D2D]'">
                {{ d.title }}
              </p>
              <p class="text-xs text-[#94A3B8] mt-0.5">
                {{ new Date(d.created_at).toLocaleDateString('fr-FR', { day:'2-digit', month:'short', year:'numeric' }) }}
              </p>
            </div>
            <span :class="['text-xs px-2.5 py-1 rounded-full font-medium shrink-0 ml-3',
              statusClass[d.status] || 'bg-gray-100 text-gray-600']">
              {{ statusLabel[d.status] || d.status }}
            </span>
          </div>
        </div>
      </div>

      <!-- CTA -->
      <div class="mt-5">
        <Button label="Soumettre une nouvelle référence" icon="pi pi-plus" @click="allerNouvelledemande()" />
      </div>
    </template>


    <Dialog v-model:visible="showInactifDialog" header="Compte inactif" :modal="true" :style="{ width: '420px' }">
  <div class="flex flex-col items-center gap-4 py-2 text-center">
    <div class="w-14 h-14 rounded-full bg-yellow-100 flex items-center justify-center">
      <i class="pi pi-lock text-2xl text-yellow-600"></i>
    </div>
    <div>
      <p class="text-sm font-medium mb-2" :class="theme.isDark ? 'text-white' : 'text-[#2D2D2D]'">
        Votre compte est actuellement <strong>inactif</strong>.
      </p>
      <p class="text-sm text-[#777]">
        Pour soumettre une demande, veuillez contacter l'administration afin d'activer votre compte.
      </p>
      <div class="mt-4 p-3 rounded-xl" :class="theme.isDark ? 'bg-white/5' : 'bg-[#F8F6F6] border border-[#ECECEC]'">
        <p class="text-xs text-[#94A3B8] mb-1">Numéro de contact</p>
        <p class="text-base font-bold text-[#7A0026]">+655-19-15-94</p>
      </div>
    </div>
  </div>
  <template #footer>
    <Button label="Fermer" @click="showInactifDialog = false" />
  </template>
</Dialog>

  </div>
</template>

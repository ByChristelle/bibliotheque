<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useThemeStore } from '@/stores/theme'
import { useAuthStore } from '@/stores/auth'
import { useToast } from 'primevue/usetoast'

const theme  = useThemeStore()
const router = useRouter()
const auth   = useAuthStore()
const toast  = useToast()

const form = ref({ first_name: '', last_name: '', email: '', phone: '', password: '', password_confirmation: '' })
const showPass = ref(false)
const showPassConf = ref(false)

function inputClass(isDark) {
  return [
    'w-full px-4 py-3 rounded-xl border text-sm outline-none transition-all duration-200',
    isDark
      ? 'bg-white/5 border-white/10 text-white placeholder-white/30 focus:border-[#B78493] focus:ring-2 focus:ring-[#7A0026]/20'
      : 'bg-white border-[#ECECEC] text-[#2D2D2D] placeholder-[#aaa] focus:border-[#7A0026] focus:ring-2 focus:ring-[#7A0026]/10'
  ].join(' ')
}

const submit = async () => {
  try {
    const success = await auth.register(form.value)
    if (success) {
      toast.add({ severity: 'success', summary: 'Inscription réussie', detail: auth.message, life: 4000 })
      router.push('/connexion')
    }
  } catch (error) {
    toast.add({ severity: 'error', summary: 'Erreur', detail: "Une erreur est survenue lors de l'inscription.", life: 4000 })
  }
}
</script>

<template>
  <div :class="['min-h-screen flex transition-colors duration-300',
    theme.isDark ? 'bg-bordeaux-950' : 'bg-[#F8F6F6]']">

    <!-- Panneau gauche -->
    <div class="hidden lg:flex lg:w-5/12 xl:w-1/2 bg-[#7A0026] relative overflow-hidden flex-col justify-between p-12">
      <div>
        <div class="flex items-center gap-3 mb-16">
          <div class="w-10 h-10 rounded-2xl bg-white/15 flex items-center justify-center">
            <i class="pi pi-book text-white text-lg"></i>
          </div>
          <span class="text-white font-bold text-lg">BiblioConnect</span>
        </div>
        <h2 class="text-white text-3xl font-bold leading-snug mb-4">
          Rejoignez la<br>communauté de<br>lecteurs
        </h2>
        <p class="text-white/60 text-sm leading-relaxed max-w-xs">
          Créez votre compte et accédez à des milliers de références documentaires en quelques secondes.
        </p>
      </div>
      <div class="absolute -bottom-20 -right-20 w-64 h-64 rounded-full bg-white/5"></div>
      <div class="absolute -top-10 -right-10 w-40 h-40 rounded-full bg-white/5"></div>
      <p class="text-white/40 text-xs">© 2026 BiblioConnect</p>
    </div>

    <!-- Formulaire -->
    <div class="flex-1 flex items-center justify-center px-6 py-12 overflow-y-auto">
      <div class="w-full max-w-sm">

        <div class="flex items-center gap-2 mb-8 lg:hidden">
          <div class="w-8 h-8 rounded-xl bg-[#7A0026] flex items-center justify-center">
            <i class="pi pi-book text-white text-sm"></i>
          </div>
          <span :class="['font-bold', theme.isDark ? 'text-white' : 'text-[#7A0026]']">BiblioConnect</span>
        </div>

        <h1 :class="['text-2xl font-bold mb-1', theme.isDark ? 'text-white' : 'text-[#2D2D2D]']">
          Créer un compte
        </h1>
        <p :class="['text-sm mb-8', theme.isDark ? 'text-white/50' : 'text-[#777]']">
          Remplissez les informations ci-dessous
        </p>

        <form @submit.prevent="submit" class="flex flex-col gap-4">

          <div class="grid grid-cols-2 gap-3">
            <div class="flex flex-col gap-1.5">
              <label :class="['text-xs font-semibold uppercase tracking-widest', theme.isDark ? 'text-white/60' : 'text-[#777]']">Nom</label>
              <input v-model="form.first_name" required placeholder="Dupont"
                :class="inputClass(theme.isDark)" />
            </div>
            <div class="flex flex-col gap-1.5">
              <label :class="['text-xs font-semibold uppercase tracking-widest', theme.isDark ? 'text-white/60' : 'text-[#777]']">Prénom</label>
              <input v-model="form.last_name" required placeholder="Jean"
                :class="inputClass(theme.isDark)" />
            </div>
          </div>

          <div class="flex flex-col gap-1.5">
            <label :class="['text-xs font-semibold uppercase tracking-widest', theme.isDark ? 'text-white/60' : 'text-[#777]']">Email</label>
            <input v-model="form.email" type="email" required placeholder="votre@email.com"
              :class="inputClass(theme.isDark)" />
          </div>

          <div class="flex flex-col gap-1.5">
            <label :class="['text-xs font-semibold uppercase tracking-widest', theme.isDark ? 'text-white/60' : 'text-[#777]']">Téléphone</label>
            <input v-model="form.phone" type="tel" placeholder="+237..."
              :class="inputClass(theme.isDark)" />
          </div>

          <div class="flex flex-col gap-1.5">
            <label :class="['text-xs font-semibold uppercase tracking-widest', theme.isDark ? 'text-white/60' : 'text-[#777]']">Mot de passe</label>
            <div class="relative">
              <input v-model="form.password" :type="showPass ? 'text' : 'password'" required placeholder="••••••••"
                :class="[inputClass(theme.isDark), 'pr-11']" />
              <button type="button" @click="showPass = !showPass" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#aaa] hover:text-[#777]">
                <i :class="showPass ? 'pi pi-eye-slash text-sm' : 'pi pi-eye text-sm'"></i>
              </button>
            </div>
          </div>

          <div class="flex flex-col gap-1.5">
            <label :class="['text-xs font-semibold uppercase tracking-widest', theme.isDark ? 'text-white/60' : 'text-[#777]']">Confirmer</label>
            <div class="relative">
              <input v-model="form.password_confirmation" :type="showPassConf ? 'text' : 'password'" required placeholder="••••••••"
                :class="[inputClass(theme.isDark), 'pr-11']" />
              <button type="button" @click="showPassConf = !showPassConf" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#aaa] hover:text-[#777]">
                <i :class="showPassConf ? 'pi pi-eye-slash text-sm' : 'pi pi-eye text-sm'"></i>
              </button>
            </div>
          </div>

          <!-- Erreurs validation -->
          <div v-if="Object.keys(auth.errors).length" class="p-3 rounded-xl bg-red-50 border border-red-200">
            <p v-for="(msgs, field) in auth.errors" :key="field" class="text-xs text-red-600">{{ msgs[0] }}</p>
          </div>

          <button type="submit" :disabled="auth.loading"
            class="w-full py-3 rounded-xl bg-[#7A0026] text-white text-sm font-semibold hover:bg-[#5e001c] transition-all duration-200 shadow-md hover:shadow-lg disabled:opacity-60 mt-1">
            <span v-if="!auth.loading">Créer mon compte</span>
            <span v-else class="flex items-center justify-center gap-2">
              <i class="pi pi-spin pi-spinner text-sm"></i> Création...
            </span>
          </button>
        </form>

        <p :class="['text-center text-sm mt-6', theme.isDark ? 'text-white/50' : 'text-[#777]']">
          Déjà inscrit ?
          <RouterLink to="/connexion" class="font-semibold no-underline text-[#7A0026] hover:underline ml-1">
            Se connecter
          </RouterLink>
        </p>

        <div class="flex justify-center mt-3">
          <button @click="theme.toggle()"
            :class="['text-xs flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition-all duration-200',
              theme.isDark ? 'text-white/40 hover:text-white/70 hover:bg-white/5' : 'text-[#aaa] hover:text-[#777] hover:bg-[#ECECEC]']">
            <i :class="theme.isDark ? 'pi pi-sun text-xs' : 'pi pi-moon text-xs'"></i>
            {{ theme.isDark ? 'Mode clair' : 'Mode sombre' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

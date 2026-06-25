<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useThemeStore } from '@/stores/theme'
import { useAuthStore } from '@/stores/auth'

const theme  = useThemeStore()
const router = useRouter()
const auth   = useAuthStore()

const form = ref({ email: '', password: '' })
const showPassword = ref(false)

const submit = async () => {
  try {
    const success = await auth.login(form.value)
    if (success) {
      router.push(auth.redirectUserByRole(auth.user.role))
    }
  } catch (error) {
    console.error('erreur de connexion', error)
  }
}
</script>

<template>
  <div :class="['min-h-screen flex transition-colors duration-300',
    theme.isDark ? 'bg-bordeaux-950' : 'bg-[#F8F6F6]']">

    <!-- Panneau gauche décoratif -->
    <div class="hidden lg:flex lg:w-5/12 xl:w-1/2 bg-[#7A0026] relative overflow-hidden flex-col justify-between p-12">
      <div>
        <div class="flex items-center gap-3 mb-16">
          <div class="w-10 h-10 rounded-2xl bg-white/15 flex items-center justify-center">
            <i class="pi pi-book text-white text-lg"></i>
          </div>
          <span class="text-white font-bold text-lg">BiblioConnect</span>
        </div>
        <h2 class="text-white text-3xl font-bold leading-snug mb-4">
          Votre bibliothèque<br>numérique accessible<br>partout
        </h2>
        <p class="text-white/60 text-sm leading-relaxed max-w-xs">
          Consultez, lisez et téléchargez des milliers de références documentaires depuis n'importe quel appareil.
        </p>
      </div>
      <!-- Déco -->
      <div class="absolute -bottom-20 -right-20 w-64 h-64 rounded-full bg-white/5"></div>
      <div class="absolute -top-10 -right-10 w-40 h-40 rounded-full bg-white/5"></div>
      <p class="text-white/40 text-xs">© 2026 BiblioConnect</p>
    </div>

    <!-- Formulaire -->
    <div class="flex-1 flex items-center justify-center px-6 py-12">
      <div class="w-full max-w-sm">

        <!-- Mobile logo -->
        <div class="flex items-center gap-2 mb-8 lg:hidden">
          <div class="w-8 h-8 rounded-xl bg-[#7A0026] flex items-center justify-center">
            <i class="pi pi-book text-white text-sm"></i>
          </div>
          <span :class="['font-bold', theme.isDark ? 'text-white' : 'text-[#7A0026]']">BiblioConnect</span>
        </div>

        <h1 :class="['text-2xl font-bold mb-1', theme.isDark ? 'text-white' : 'text-[#2D2D2D]']">
          Connexion
        </h1>
        <p :class="['text-sm mb-8', theme.isDark ? 'text-white/50' : 'text-[#777]']">
          Accédez à votre espace personnel
        </p>

        <form @submit.prevent="submit" class="flex flex-col gap-5">
          <div class="flex flex-col gap-2">
            <label :class="['text-xs font-semibold uppercase tracking-widest', theme.isDark ? 'text-white/60' : 'text-[#777]']">
              Adresse e-mail
            </label>
            <input
              v-model="form.email" type="email" required
              placeholder="votre@email.com"
              :class="['w-full px-4 py-3 rounded-xl border text-sm outline-none transition-all duration-200',
                theme.isDark
                  ? 'bg-white/5 border-white/10 text-white placeholder-white/30 focus:border-[#B78493] focus:ring-2 focus:ring-[#7A0026]/20'
                  : 'bg-white border-[#ECECEC] text-[#2D2D2D] placeholder-[#aaa] focus:border-[#7A0026] focus:ring-2 focus:ring-[#7A0026]/10']"
            />
          </div>

          <div class="flex flex-col gap-2">
            <label :class="['text-xs font-semibold uppercase tracking-widest', theme.isDark ? 'text-white/60' : 'text-[#777]']">
              Mot de passe
            </label>
            <div class="relative">
              <input
                v-model="form.password" :type="showPassword ? 'text' : 'password'" required
                placeholder="••••••••"
                :class="['w-full px-4 py-3 pr-11 rounded-xl border text-sm outline-none transition-all duration-200',
                  theme.isDark
                    ? 'bg-white/5 border-white/10 text-white placeholder-white/30 focus:border-[#B78493] focus:ring-2 focus:ring-[#7A0026]/20'
                    : 'bg-white border-[#ECECEC] text-[#2D2D2D] placeholder-[#aaa] focus:border-[#7A0026] focus:ring-2 focus:ring-[#7A0026]/10']"
              />
              <button type="button" @click="showPassword = !showPassword"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-[#aaa] hover:text-[#777] transition-colors">
                <i :class="showPassword ? 'pi pi-eye-slash text-sm' : 'pi pi-eye text-sm'"></i>
              </button>
            </div>
            <div class="flex justify-end">
              <a href="#" :class="['text-xs font-medium hover:underline transition-colors', theme.isDark ? 'text-[#B78493]' : 'text-[#7A0026]']">
                Mot de passe oublié ?
              </a>
            </div>
          </div>

          <button
            type="submit" :disabled="auth.loading"
            class="w-full py-3 rounded-xl bg-[#7A0026] text-white text-sm font-semibold hover:bg-[#5e001c] transition-all duration-200 shadow-md hover:shadow-lg disabled:opacity-60 disabled:cursor-not-allowed mt-1"
          >
            <span v-if="!auth.loading">Se connecter</span>
            <span v-else class="flex items-center justify-center gap-2">
              <i class="pi pi-spin pi-spinner text-sm"></i> Connexion...
            </span>
          </button>
        </form>

        <!-- Erreurs -->
        <div v-if="Object.keys(auth.errors).length" class="mt-4 p-3 rounded-xl bg-red-50 border border-red-200">
          <p v-for="(msgs, field) in auth.errors" :key="field" class="text-xs text-red-600">
            {{ msgs[0] }}
          </p>
        </div>

        <p :class="['text-center text-sm mt-6', theme.isDark ? 'text-white/50' : 'text-[#777]']">
          Pas encore de compte ?
          <RouterLink to="/inscription" class="font-semibold no-underline text-[#7A0026] hover:underline ml-1">
            S'inscrire
          </RouterLink>
        </p>

        <!-- Toggle theme -->
        <div class="flex justify-center mt-4">
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

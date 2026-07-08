<script setup>
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

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
  <div class="min-h-screen relative flex items-center justify-center">

    <!-- Image de fond -->
    <div class="fixed inset-0 z-0 bg-cover bg-center bg-no-repeat"
      style="background-image: url('/bibliotheque2.jpg'); background-size: cover;"></div>

    <!-- Overlay -->
    <div class="fixed inset-0 z-0" style="background: rgba(5, 0, 10, 0.50)"></div>

    <!-- Formulaire glassmorphism centré -->
    <div class="relative z-10 w-full max-w-md mx-auto px-4 py-12">
      <div class="rounded-2xl px-8 py-10"
        style="background: rgba(255,255,255,0.08); backdrop-filter: blur(24px); -webkit-backdrop-filter: blur(24px); border: 1px solid rgba(255,255,255,0.18); box-shadow: 0 24px 64px rgba(0,0,0,0.3);">

        <!-- Logo -->
        <div class="flex flex-col items-center mb-8">
          <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-3"
            style="background: rgba(122,0,38,0.8); border: 1px solid rgba(255,255,255,0.2)">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 text-white">
              <path d="M11.25 4.533A9.707 9.707 0 0 0 6 3a9.735 9.735 0 0 0-3.25.555.75.75 0 0 0-.5.707v14.25a.75.75 0 0 0 1 .707A8.237 8.237 0 0 1 6 18.75c1.995 0 3.823.707 5.25 1.886V4.533ZM12.75 20.636A8.214 8.214 0 0 1 18 18.75c.966 0 1.89.166 2.75.47a.75.75 0 0 0 1-.708V4.262a.75.75 0 0 0-.5-.707A9.735 9.735 0 0 0 18 3a9.707 9.707 0 0 0-5.25 1.533v16.103Z" />
            </svg>
          </div>
          <h1 class="text-xl font-bold text-white">BiblioConnect</h1>
          <p class="text-white/50 text-sm mt-1">Bibliothèque numérique</p>
        </div>

        <h2 class="text-2xl font-bold text-white mb-1">Connexion</h2>
        <p class="text-white/60 text-sm mb-6">Accédez à votre espace personnel</p>

        <form @submit.prevent="submit" class="flex flex-col gap-4">

          <div class="flex flex-col gap-1.5">
            <label class="text-xs font-semibold uppercase tracking-widest text-white/60">Email</label>
            <input v-model="form.email" type="email" required placeholder="votre@email.com"
              class="w-full px-4 py-3 rounded-xl text-sm text-white placeholder-white/30 outline-none transition-all duration-200"
              style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);"
              @focus="$event.target.style.borderColor='rgba(255,255,255,0.5)'"
              @blur="$event.target.style.borderColor='rgba(255,255,255,0.2)'" />
          </div>

          <div class="flex flex-col gap-1.5">
            <label class="text-xs font-semibold uppercase tracking-widest text-white/60">Mot de passe</label>
            <div class="relative">
              <input v-model="form.password" :type="showPassword ? 'text' : 'password'" required placeholder="••••••••"
                class="w-full px-4 py-3 pr-11 rounded-xl text-sm text-white placeholder-white/30 outline-none transition-all duration-200"
                style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);"
                @focus="$event.target.style.borderColor='rgba(255,255,255,0.5)'"
                @blur="$event.target.style.borderColor='rgba(255,255,255,0.2)'" />
              <button type="button" @click="showPassword = !showPassword"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-white/40 hover:text-white/80 transition-colors">
                <i :class="showPassword ? 'pi pi-eye-slash text-sm' : 'pi pi-eye text-sm'"></i>
              </button>
            </div>
            <div class="flex justify-end">
              <a href="#" class="text-xs text-white/50 hover:text-white transition-colors no-underline">
                Mot de passe oublié ?
              </a>
            </div>
          </div>

          <!-- Erreurs -->
          <div v-if="Object.keys(auth.errors ?? {}).length" class="p-3 rounded-xl"
            style="background: rgba(220,38,38,0.2); border: 1px solid rgba(220,38,38,0.3)">
            <p v-for="(msgs, field) in auth.errors" :key="field" class="text-xs text-red-300">{{ msgs[0] }}</p>
          </div>

          <button type="submit" :disabled="auth.loading"
            class="w-full py-3 rounded-xl text-white text-sm font-semibold transition-all duration-200 disabled:opacity-50 mt-1 hover:opacity-90"
            style="background: rgba(122,0,38,0.9); border: 1px solid rgba(255,255,255,0.15)">
            <span v-if="!auth.loading">Se connecter</span>
            <span v-else class="flex items-center justify-center gap-2">
              <i class="pi pi-spin pi-spinner text-sm"></i> Connexion...
            </span>
          </button>
        </form>

        <p class="text-center text-sm text-white/50 mt-6">
          Pas encore de compte ?
          <RouterLink to="/inscription" class="font-semibold no-underline text-white hover:text-white/80 ml-1">
            S'inscrire
          </RouterLink>
        </p>

        <div class="flex justify-center mt-3">
          <button @click="router.push('/')"
            class="text-xs text-white/40 hover:text-white/70 transition-colors flex items-center gap-1">
            <i class="pi pi-arrow-left text-xs"></i> Retour à l'accueil
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

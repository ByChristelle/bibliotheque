<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import InputText from 'primevue/inputtext'
import Password from 'primevue/password'
import Button from 'primevue/button'
import { useThemeStore } from '@/stores/theme'

const theme = useThemeStore()
const router = useRouter()

const form = ref({ email: '', password: '' })
const loading = ref(false)

function submit() {
  // TODO: appel API auth
}
</script>

<template>
  <div :class="['min-h-screen flex items-center justify-center relative transition-colors duration-300',
    theme.isDark
      ? 'bg-gradient-to-br from-bordeaux-950 via-bordeaux-900 to-bordeaux-950'
      : 'bg-gradient-to-br from-bordeaux-50 via-bordeaux-100 to-bordeaux-200']">

    <!-- Orbs -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
      <div class="absolute -top-32 -right-20 w-[450px] h-[450px] rounded-full opacity-30"
           :class="theme.isDark ? 'bg-bordeaux-700' : 'bg-bordeaux-400'"
           style="filter: blur(80px)"></div>
      <div class="absolute -bottom-20 -left-20 w-72 h-72 rounded-full opacity-25"
           :class="theme.isDark ? 'bg-bordeaux-900' : 'bg-bordeaux-300'"
           style="filter: blur(70px)"></div>
    </div>

    <!-- Card -->
    <div class="relative z-10 w-full max-w-md px-4">
      <div :class="['rounded-2xl p-8 backdrop-blur-xl border shadow-2xl',
        theme.isDark
          ? 'bg-white/5 border-white/10'
          : 'bg-white/20 border-white/50']">

        <!-- Brand -->
        <div class="text-center mb-6">
          <div class="w-14 h-14 mx-auto mb-3 rounded-2xl flex items-center justify-center bg-gradient-to-br from-bordeaux-700 to-bordeaux-900 shadow-lg">
            <i class="pi pi-book text-white text-2xl"></i>
          </div>
          <h1 :class="['text-xl font-bold', theme.isDark ? 'text-bordeaux-200' : 'text-bordeaux-800']">
            BiblioConnect
          </h1>
          <p :class="['text-xs mt-0.5', theme.isDark ? 'text-bordeaux-400' : 'text-bordeaux-600']">
            Bibliothèque numérique
          </p>
        </div>

        <h2 :class="['text-2xl font-bold mb-1', theme.isDark ? 'text-bordeaux-100' : 'text-bordeaux-900']">
          Connexion
        </h2>
        <p :class="['text-sm mb-6', theme.isDark ? 'text-bordeaux-400' : 'text-bordeaux-600']">
          Accédez à votre espace personnel
        </p>

        <form @submit.prevent="submit" class="flex flex-col gap-4">
          <div class="flex flex-col gap-1.5">
            <label :class="['text-sm font-semibold', theme.isDark ? 'text-bordeaux-300' : 'text-bordeaux-700']">
              Adresse e-mail
            </label>
            <InputText
              v-model="form.email"
              type="email"
              placeholder="votre@email.com"
              class="w-full"
              required
            />
          </div>

          <div class="flex flex-col gap-1.5">
            <label :class="['text-sm font-semibold', theme.isDark ? 'text-bordeaux-300' : 'text-bordeaux-700']">
              Mot de passe
            </label>
            <Password
              v-model="form.password"
              placeholder="••••••••"
              :feedback="false"
              toggleMask
              class="w-full"
              inputClass="w-full"
              required
            />
          </div>

          <div class="flex justify-end">
            <a :class="['text-xs font-medium no-underline hover:underline',
              theme.isDark ? 'text-bordeaux-400' : 'text-bordeaux-600']"
              href="#">
              Mot de passe oublié ?
            </a>
          </div>

          <Button
            type="submit"
            label="Se connecter"
            icon="pi pi-sign-in"
            :loading="loading"
            class="w-full mt-1"
          />
        </form>

        <p :class="['text-center text-sm mt-5', theme.isDark ? 'text-bordeaux-400' : 'text-bordeaux-600']">
          Pas encore de compte ?
          <RouterLink to="/inscription"
            :class="['font-semibold no-underline hover:underline ml-1',
              theme.isDark ? 'text-bordeaux-300' : 'text-bordeaux-700']">
            S'inscrire
          </RouterLink>
        </p>

        <div class="text-center mt-2">
          <Button label="Retour à l'accueil" icon="pi pi-arrow-left" text size="small" @click="router.push('/')" />
        </div>
      </div>
    </div>

    <!-- Toggle theme -->
    <Button
      :icon="theme.isDark ? 'pi pi-sun' : 'pi pi-moon'"
      rounded text
      class="fixed top-4 right-4 z-20"
      @click="theme.toggle()"
    />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
// import InputText from 'primevue/inputtext'
// import Password from 'primevue/password'
// import Button from 'primevue/button'
import { useThemeStore } from '@/stores/theme'
import { useAuthStore } from '@/stores/auth'
import { useToast } from 'primevue'

const theme = useThemeStore()
const router = useRouter()
const auth=useAuthStore()
const toast=useToast()


const form = ref({ first_name: '', last_name:'', email: '', phone:'', password: '', password_confirmation: '' })
// const loading = ref(false)

const submit= async () => {
  try {
    
    const success = await auth.register(form.value)
    if(success){

       toast.add({
        severity: 'success',
        summary: 'Inscription réussie',
        detail: auth.message, //  le message récupéré depuis la réponse
        life: 4000
      })
     router.push('/espace')
    }
  } catch (error) {
    toast.add({
      severity: 'error',
      summary: 'Erreur',
      detail: "Une erreur est survenue lors de l'inscription.",
      life: 4000
    })
    console.error(`erreur d'inscription` , error)
  }
}
</script>

<template>
  <div :class="['min-h-screen flex items-center justify-center relative transition-colors duration-300',
    theme.isDark
      ? 'bg-gradient-to-br from-bordeaux-950 via-bordeaux-900 to-bordeaux-950'
      : 'bg-gradient-to-br from-bordeaux-50 via-bordeaux-100 to-bordeaux-200']">

    <!-- Orbs -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
      <div class="absolute -top-32 -left-20 w-[400px] h-[400px] rounded-full opacity-30"
           :class="theme.isDark ? 'bg-bordeaux-700' : 'bg-bordeaux-400'"
           style="filter: blur(80px)"></div>
      <div class="absolute -bottom-20 -right-20 w-72 h-72 rounded-full opacity-25"
           :class="theme.isDark ? 'bg-bordeaux-900' : 'bg-bordeaux-300'"
           style="filter: blur(70px)"></div>
    </div>

    <div class="relative z-10 w-full max-w-md px-4">
      <div :class="['rounded-2xl p-8 backdrop-blur-xl border shadow-2xl',
        theme.isDark ? 'bg-white/5 border-white/10' : 'bg-white/20 border-white/50']">

        <!-- Brand -->
        <div class="text-center mb-6">
          <div class="w-14 h-14 mx-auto mb-3 rounded-2xl flex items-center justify-center bg-gradient-to-br from-bordeaux-700 to-bordeaux-900 shadow-lg">
            <i class="pi pi-book text-white text-2xl"></i>
          </div>
          <h1 :class="['text-xl font-bold', theme.isDark ? 'text-bordeaux-200' : 'text-bordeaux-800']">
            BiblioConnect
          </h1>
        </div>

        <h2 :class="['text-2xl font-bold mb-1', theme.isDark ? 'text-bordeaux-100' : 'text-bordeaux-900']">
          Créer un compte
        </h2>
        <p :class="['text-sm mb-6', theme.isDark ? 'text-bordeaux-400' : 'text-bordeaux-600']">
          Rejoignez la bibliothèque numérique
        </p>


        <!-- formulaire d'inscription -->
        <form @submit.prevent="submit" class="flex flex-col gap-4">
          <div class="flex flex-col gap-1.5">
            <label :class="['text-sm font-semibold', theme.isDark ? 'text-bordeaux-300' : 'text-bordeaux-700']">
              Nom 
            </label>
            <InputText v-model="form.first_name" placeholder=" Dupont" class="w-full" required />
          </div>
          <div class="flex flex-col gap-1.5">
            <label :class="['text-sm font-semibold', theme.isDark ? 'text-bordeaux-300' : 'text-bordeaux-700']">
             Prenom
            </label>
            <InputText v-model="form.last_name" placeholder="Jean " class="w-full" required />
          </div>

          <div class="flex flex-col gap-1.5">
            <label :class="['text-sm font-semibold', theme.isDark ? 'text-bordeaux-300' : 'text-bordeaux-700']">
              Adresse e-mail
            </label>
            <InputText v-model="form.email" type="email" placeholder="votre@email.com" class="w-full" required />
          </div>
          <div class="flex flex-col gap-1.5">
            <label :class="['text-sm font-semibold', theme.isDark ? 'text-bordeaux-300' : 'text-bordeaux-700']">
              Tel
            </label>
            <InputText v-model="form.phone" type="tel" placeholder="+237-655-19-15-94" class="w-full" required />
          </div>

          <div class="flex flex-col gap-1.5">
            <label :class="['text-sm font-semibold', theme.isDark ? 'text-bordeaux-300' : 'text-bordeaux-700']">
              Mot de passe
            </label>
            <Password
              v-model="form.password"
              placeholder="••••••••"
              toggleMask
              class="w-full"
              inputClass="w-full"
              required
            />
          </div>

          <div class="flex flex-col gap-1.5">
            <label :class="['text-sm font-semibold', theme.isDark ? 'text-bordeaux-300' : 'text-bordeaux-700']">
              Confirmer le mot de passe
            </label>
            <Password
              v-model="form.password_confirmation"
              placeholder="••••••••"
              :feedback="false"
              toggleMask
              class="w-full"
              inputClass="w-full"
              required
            />
          </div>

          <Button
            type="submit"
            label="S'inscrire"
            icon="pi pi-user-plus"
            :loading="auth.loading"
            class="w-full mt-1"
          />
        </form>

        <p :class="['text-center text-sm mt-5', theme.isDark ? 'text-bordeaux-400' : 'text-bordeaux-600']">
          Déjà inscrit ?
          <RouterLink to="/connexion"
            :class="['font-semibold no-underline hover:underline ml-1',
              theme.isDark ? 'text-bordeaux-300' : 'text-bordeaux-700']">
            Se connecter
          </RouterLink>
        </p>

        <div class="text-center mt-2">
          <Button label="Retour à l'accueil" icon="pi pi-arrow-left" text size="small" @click="router.push('/')" />
        </div>
      </div>
    </div>

    <Button
      :icon="theme.isDark ? 'pi pi-sun' : 'pi pi-moon'"
      rounded text
      class="fixed top-4 right-4 z-20"
      @click="theme.toggle()"
    />
  </div>
</template>

import { createApp } from 'vue'
import { createPinia } from 'pinia'

import App from './App.vue'
import router from './router'
import './assets/main.css'

import PrimeVue from 'primevue/config'
import ToastService from 'primevue/toastservice'
import ConfirmationService from 'primevue/confirmationservice'
import BordeauxPreset from './theme/bordeaux.js'
import { useAuthStore } from './stores/auth.js'
const app = createApp(App)

app.use(createPinia())
app.use(PrimeVue, {
  theme: {
    preset: BordeauxPreset,
    options: {
      darkModeSelector: '.dark',
    }
  }
})
app.use(ToastService)
app.use(ConfirmationService)

const pinia = createPinia()

app.use(pinia)
const auth = useAuthStore()

// On vérifie l'authentification AVANT de monter le routeur
auth.checkAuth().finally(() => {
  app.use(router)
  app.mount('#app')
})

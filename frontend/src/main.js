import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from '@/App.vue'
import router from '@/router/index.js'
import '@/assets/main.css'
import PrimeVue from 'primevue/config'
import ToastService from 'primevue/toastservice'
import ConfirmationService from 'primevue/confirmationservice'
import BordeauxPreset from '@/theme/bordeaux.js'

const app = createApp(App)
const pinia = createPinia()

// ✅ 1. Pinia en premier
app.use(pinia)

// ✅ 2. Ensuite les autres plugins
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
app.use(router)

// ✅ 3. useAuthStore() APRÈS app.use(pinia)
// Plus besoin ici, le router guard s'en charge via auth.init()

app.mount('#app')
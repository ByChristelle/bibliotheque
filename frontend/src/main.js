import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from '@/App.vue'
import router from '@/router/index.js'
import '@/assets/main.css'
import PrimeVue from 'primevue/config'
import ToastService from 'primevue/toastservice'
import ConfirmationService from 'primevue/confirmationservice'
import BordeauxPreset from '@/theme/bordeaux.js'
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate'
import { useAuthStore } from './stores/auth'


const app = createApp(App)
const pinia = createPinia()
pinia.use(piniaPluginPersistedstate)
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

const auth= useAuthStore()
await auth.fetchUser()
if (auth.user) auth.listenSuspension()

app.mount('#app')
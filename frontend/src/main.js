import { createApp } from 'vue'
import { createPinia } from 'pinia'

import App from './App.vue'
import router from './router'
import './assets/main.css'

import PrimeVue from 'primevue/config'
import ToastService from 'primevue/toastservice'
import ConfirmationService from 'primevue/confirmationservice'
import BordeauxPreset from './theme/bordeaux.js'

const app = createApp(App)

app.use(createPinia())
app.use(router)
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

app.mount('#app')

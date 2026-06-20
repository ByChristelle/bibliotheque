import { defineStore } from 'pinia';
import api from '../services/axios';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    loading: false,
    errors: {} 
  }),

  actions: {
    //pour inscription 
    async register(formData) {
      this.loading = true;
      this.errors = {};

      try {
        // 1. Initialiser le cookie CSRF (Obligatoire avec Sanctum)
        await api.get('http://localhost:8000/sanctum/csrf-cookie', {withCredentials:true});

        // 2. Envoyer les données d'inscription au Backend
        const response = await api.post('/register', formData);
        
        // 3. Stocker l'utilisateur si le backend le renvoie directement
        this.user = response.data.user; 
        
        return true;
      } catch (error) {
        if (error.response && error.response.status === 422) {
          // Récupère les erreurs de validation de Laravel (ex: email déjà pris)
          this.errors = error.response.data.errors;
        }
        throw error;
      } finally {
        this.loading = false;
      }
    }
  }
});
import { defineStore } from 'pinia';
import api from '../services/axios';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    message:null,
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
        this.message=response.data.message;
        
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
    },

    //pour la connexion
    async login(credentials) {
  this.loading = true;
  this.errors = {};

  try {
    // 1. Initialiser le cookie CSRF (toujours obligatoire avec Sanctum par session)
     await api.get('http://localhost:8000/sanctum/csrf-cookie', {withCredentials:true});


    // 2. Envoyer les identifiants
    const response = await api.post('/login', credentials);
    
    // 3. Stocker les infos de l'utilisateur connecté
    this.user = response.data.user;
    
    return true;
  } catch (error) {
    if (error.response && error.response.status === 422) {
      this.errors = error.response.data.errors;
    }
    throw error;
  } finally {
    this.loading = false;
  }
},

//Pour la redirection des selon le role après connexion

 redirectUseByRole(role){
    const routes ={
        'admin':'/admin/dashboard',
        'responsable_rh':'/rh/dashboard',
        'responsable_demande':'/responsable/dashboard',
        'user':'/espace/dashboard',

    }

    return routes[role] || '/'

}
  }

  
});
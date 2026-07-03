import { defineStore } from 'pinia';
import api from '../services/axios';

export const useAuthStore = defineStore('auth', {
    persist:true,
  state: () => ({
    user: null,
    message:null,
    loading: false,
    errors: {} ,
    pollingInterval:null,
    
  }),

  actions: {


    
startStatusPolling() {
  // Vérifie le statut toutes les 30 secondes (30000ms)
  this.pollingInterval = setInterval(async () => {
    if (this.user) {
      await this.fetchUser();
    } else {
      this.stopStatusPolling();
    }
  }, 30000);
},

stopStatusPolling() {
  if (this.pollingInterval) {
    clearInterval(this.pollingInterval);
    this.pollingInterval = null;
  }
},

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
     this.startStatusPolling(); 
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

 redirectUserByRole(role){
    const routes ={
        'admin':'/admin/dashboard',
        'responsable_rh':'/rh/dashboard',
        'responsable_demande':'/responsable/dashboard',
        'user':'/espace/dashboard',

    }

    return routes[role] || '/'

},



async fetchUser() {
  try {
      const response = await api.get('/me');
    const user = response.data.user;
    
    // VÉRIFICATION : Si le statut est 'suspended', déconnecter immédiatement
    if (user && user.status === 'suspended') {
      this.message = 'Votre compte a été suspendu. Veuillez contacter l\'administrateur.';
      await this.logout();
      return;
    }
     this.user = user
  } catch {
    this.user = null
  }
}
,
async logout() {
  this.stopStatusPolling();
  this.loading = true;
  try {
    // 1. Appeler le serveur pour détruire la session Laravel
    await api.post('/logout');
  } catch (error) {
    console.error("Erreur lors de la déconnexion backend", error);
  } finally {
    // 2. Toujours vider le store et rediriger, même si le serveur a échoué
    this.user = null;
    this.loading = false;
    
    // 3. Redirection vers la page de connexion
    window.location.href = '/connexion'; 
  }
},

  
}
});
import axios from 'axios';

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL,//permet la connexion avec le back
  //favorable pour l'authentification par cookie/session
  withCredentials: true,//envoie les cookies et les info d'authen avec chaque requete
  withXSRFToken: true, //ajout auto le token CSRF aux requete pour les protéger contre les attaques de type CSRF
  headers: {
    Accept: 'application/json',
  },
})

export default api
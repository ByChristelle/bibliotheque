import { defineStore } from 'pinia'
import api from '../services/axios'

export const useUsersStore = defineStore('users', {
  state: () => ({
    users: [],
    loading: false,
    errors: null
  }),

  actions: {
    // Action pour créer un utilisateur
   async createUser(formData) {
  this.loading = true
  this.errors = null
  try {
    // Axios configure automatiquement les bons en-têtes (multipart/form-data) quand il voit un FormData
    const response = await api.post('/users', formData)
    
    if (response.data && response.data.user) {
      this.users.push(response.data.user)
    }
    return true
  } catch (error) {
     console.log('Erreurs 422 :', error.response?.data)
  this.errors = error.response?.data?.errors
  return false
  } finally {
    this.loading = false
  }
}
  }
})
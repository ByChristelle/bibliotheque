import { defineStore } from 'pinia'
import api from '../services/axios'

export const useUsersStore = defineStore('users', {
  state: () => ({
    users: [],
    loading: false,
    errors: null
  }),

  actions: {


    // 1. ACTION POUR RÉCUPÉRER TOUS LES UTILISATEURS
    async fetchUsers() {
      this.loading = true
      try {
        const response = await api.get('/users')
        this.users = response.data.users
      } catch (error) {
        console.error("Erreur lors de la récupération des utilisateurs", error)
      } finally {
        this.loading = false
      }
    },
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
},

async updateUser(userId, formData) {
  this.loading = true
  this.errors = null
  try {
    const response = await api.put(`/users/${userId}`, formData)
    const index = this.users.findIndex(user => user.id === userId)
    if (index !== -1) {
      this.users[index] = response.data.user
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
},


})
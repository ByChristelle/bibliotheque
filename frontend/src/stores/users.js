import { defineStore } from 'pinia'
import api from '../services/axios'

export const useUsersStore = defineStore('users', {
  state: () => ({
    users: [],
    loading: false,
    errors: null , 
    message:null,
  }),

  actions: {
    async fetchUsers() {
      this.loading = true
      try {
        const response = await api.get('/users')
        this.users = response.data.users
        // this.message = response.data.message
        
      } catch (error) {
        console.error("Erreur lors de la récupération des utilisateurs", error)
      } finally {
        this.loading = false
      }
    },

    async createUser(formData) {
      this.loading = true
      this.errors = null
      try {
        const response = await api.post('/users', formData)
        if (response.data && response.data.user) {
          this.users.push(response.data.user)
        this.message = response.data.message

          
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
          this.message=response.data.message
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

    async deleteUser(userId) {
      this.loading = true
      try {
        const response = await api.delete(`/users/${userId}`)
        this.users = this.users.filter(user => user.id !== userId)
        this.message = response.data.message
        return true
      } catch (error) {
        console.error("Erreur lors de l'archivage", error)
        return false
      } finally {
        this.loading = false
      }
    },

    async changeStatus(userId, status) {
      this.loading = true
      this.errors = null
      try {
        const response = await api.put(`/users/${userId}`, { status })
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
  }
})
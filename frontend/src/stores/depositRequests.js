import { defineStore } from 'pinia'
import api from '../services/axios'

export const useDepositRequestsStore = defineStore('depositRequests', {
  state: () => ({
    requests: [],
    loading: false,
    errors: null,
    message: null,
    pendingCount:0,
    selectedRequest: null,
showAssignDialog: false,
assignedRequests: [],
myRequests:[],

  }),

  actions: {

//La récupération pour le compte de la vue de l'admin
    async fetchAll() {
  this.loading = true
  try {
    const response = await api.get('/deposit-requests')
    this.requests     = response.data.deposit_requests
    this.pendingCount = response.data.pending_count
    return true
  } catch (error) {
    console.error('Erreur récupération demandes', error)
    return false
  } finally {
    this.loading = false
  }
},

    // Récupérer les demandes de l'utilisateur connecté
    async fetchMyRequests() {
      this.loading = true
      try {
        const response = await api.get('/my-requests')
        this.myRequests = response.data.deposit_requests
        return true
      } catch (error) {
        console.error("Erreur lors de la récupération des demandes", error)
        this.error = error.response?.data?.message || "Erreur de chargement"
        return false
      } finally {
        this.loading = false
      }
    },

      //Permet la soumission du formulaire
    async submit(formData) {
      this.loading = true
      this.errors = null
      try {
        const response = await api.post('/deposit-requests', formData)
        this.message = response.data.message
        return true
      } catch (error) {
        if (error.response?.status === 422) {
          this.errors = error.response.data.errors
        }
        return false
      } finally {
        this.loading = false
      }
    },


    openAssignDialog(request) {
  this.selectedRequest = request
  this.showAssignDialog = true
},

async assign(requestId, managerId) {
  this.loading = true
  try {
    const response = await api.put(`/deposit-requests/${requestId}/assign`, {
      manager_id: managerId
    })
    // Retire la demande de la liste pending
    const index = this.requests.findIndex(r => r.id === requestId)
    if (index !== -1) {
      this.requests[index] = response.data.deposit_request
    }
    // this.pendingCount = this.requests.filter(r => r.status === 'pending').length

    this.pendingCount = this.requests.filter(r => r.status === 'pending' && !r.assigned_manager_id).length
    this.message = response.data.message
    this.showAssignDialog = false
    this.selectedRequest = null
    return true
  } catch (error) {
    console.error('Erreur affectation', error)
    return false
  } finally {
    this.loading = false
  }
},

//Pour la recuperation 
async fetchMyAssigned() {
  this.loading = true
  try {
    const response = await api.get('/my-assigned-requests')
    this.assignedRequests = response.data.deposit_requests
    return true
  } catch (error) {
    console.error('Erreur récupération demandes affectées', error)
    return false
  } finally {
    this.loading = false
  }
},


async approve(requestId) {
  this.loading = true
  try {
    const response = await api.put(`/deposit-requests/${requestId}/approve`)
    const index = this.assignedRequests.findIndex(r => r.id === requestId)
    if (index !== -1) this.assignedRequests[index].status = 'approved_by_manager'
    this.message = response.data.message
    return true
  } catch (error) {
    console.error('Erreur validation', error)
    return false
  } finally {
    this.loading = false
  }
},

async reject(requestId, justification) {
  this.loading = true
  try {
    const response = await api.put(`/deposit-requests/${requestId}/reject`, { justification })
    const index = this.assignedRequests.findIndex(r => r.id === requestId)
    if (index !== -1) this.assignedRequests[index].status = 'rejected_by_manager'
    this.message = response.data.message
    return true
  } catch (error) {
    console.error('Erreur refus', error)
    return false
  } finally {
    this.loading = false
  }
},



  },
})

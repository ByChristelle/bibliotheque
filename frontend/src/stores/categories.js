import { defineStore } from 'pinia'
import api from '../services/axios'

export const useCategoriesStore = defineStore('categories', {
state :() => ( {
    loading:false, 
    error:null,
    categories:[],
    message:null
}),

actions:{

    async fetchCategories () {
        this.loading=true
        try {
            const response= await api.get('/categories')
            this.categories= response.data.categories.map(cat => ({
                ...cat,
                count: cat.references ? cat.references.length : 0
            }))

            return true
        } catch (error) {
        console.error("Erreur lors de la récupération des categories", error)

        }finally{
            this.loading=false
        }
    
    },


    //La création des categories
    async createCategorie (categorieData){

 this.loading=true
 this.error= null
 try {
    const response = await api.post('/categories' , categorieData)
this.categories.unshift({
    ...response.data.category,
    count:0
})
this.message= response.data.message
    return true
 } catch (error) {
    console.error("Erreur lors de la création de la catégorie" , error)
    return false
 }finally{
    this.loading = false
 }
    },

    //fonction de modification
    async updateCategorie(id, data) {
  this.loading = true
  this.error = null
  try {
    const response = await api.put(`/categories/${id}`, data)
    const index = this.categories.findIndex(c => c.id === id)
    if (index !== -1) {
      this.categories[index] = { ...this.categories[index], ...response.data.category }
    }
    this.message = response.data.message
    return true
  } catch (error) {
    console.error('Erreur modification catégorie', error)
    console.log(error.response?.status, error.response?.data)

    return false
  } finally {
    this.loading = false
  }
}

}

})
import { defineStore } from 'pinia';
import api from '../services/axios';


export const useReferenceStore = defineStore('reference', {
    state: () => ({
        loading:false,
        error:null , 
        message:null ,
        references:[]
    }),


    actions : {
    async  fetchReferences(){

        this.loading= true
        try {
            const response= await api.get('/references')
            this.references=response.data.references
            return true 
            
        } catch (error) {
        console.error("Erreur lors de la récupération des references", error)
        this.error = error.response?.data?.message || error.message
            
        }finally{
            this.loading=false
        }

        }
    }
})
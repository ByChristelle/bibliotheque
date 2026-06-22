<script setup>
import { computed, onMounted, ref } from 'vue'
import { useThemeStore } from '@/stores/theme'
import { useUsersStore } from '@/stores/users'

const theme = useThemeStore()
const usersStore = useUsersStore()

const search = ref('')
const filterRole = ref(null)

const showDialog = ref(false)
const loading = ref(false)

// Suivi de l'état d'édition
const isEditing = ref(false)
const currentUserId = ref(null)

const roles = [
  { label: 'Tous les rôles', value: null },
  { label: 'Utilisateur', value: 'user' },
  { label: 'Responsable', value: 'responsable_demande' },
  { label: 'Responsable RH', value: 'responsable_rh' },
  { label: 'Administrateur', value: 'admin' },
]

onMounted(async () => {
  loading.value = true
  try {
    await usersStore.fetchUsers()
  } finally {
    loading.value = false
  }
})

const formRoles = roles.filter(r => r.value !== null)
const users = computed(() => usersStore.users)

const newUser = ref({
  first_name: '',
  last_name: '',
  email: '',
  phone: '',  
  role: 'user', 
  password: ''
})

// ── ACTION 1 : Ouvrir la modal en mode MODIFIER ─────────────────────────
const editUser = (user) => {
  isEditing.value = true
  currentUserId.value = user.id
  
  newUser.value = {
    first_name: user.first_name,
    last_name: user.last_name,
    email: user.email,
    phone: user.phone || '',
    role: user.role,
  }
  showDialog.value = true
}

// Réinitialiser le formulaire à la fermeture
const closeDialog = () => {
  showDialog.value = false
  isEditing.value = false
  currentUserId.value = null
  newUser.value = { first_name: '', last_name: '', email: '', phone: '', role: 'user', password: '' }
}

// Sauvegarde globale (Création OU Modification)
const saveUser = async () => {
  if (isEditing.value) {
    // Mode Modification
    const success = await usersStore.updateUser(currentUserId.value, newUser.value)
    if (success) closeDialog()
  } else {
    // Mode Création standard
    const formData = new FormData()
    formData.append('first_name', newUser.value.first_name)
    formData.append('last_name', newUser.value.last_name)
    formData.append('email', newUser.value.email)
    formData.append('role', newUser.value.role)
    formData.append('password', newUser.value.password)
    if (newUser.value.phone) formData.append('phone', newUser.value.phone)

    const success = await usersStore.createUser(formData)
    if (success) closeDialog()
  }
}

// ── ACTION 2 : DÉSACTIVER / ACTIVER un utilisateur ─────────────────────
const toggleUserStatus = async (user) => {
  const nextStatus = (user.status === 'active' || user.status === 'actif') ? 'inactive' : 'active'
  if (confirm(`Voulez-vous changer le statut de cet utilisateur en : ${nextStatus} ?`)) {
    await usersStore.changeStatus(user.id, nextStatus)
  }
}

// ── ACTION 3 : ARCHIVER un utilisateur ─────────────────────────────────
const archiveUser = async (user) => {

  
}
</script>

<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 :class="['text-2xl font-bold', theme.isDark ? 'text-bordeaux-100' : 'text-bordeaux-900']">
          Utilisateurs
        </h1>
        <p :class="['text-sm', theme.isDark ? 'text-bordeaux-400' : 'text-bordeaux-600']">
          Gestion complète des comptes et rôles
        </p>
      </div>
      <Button label="Créer un compte" icon="pi pi-user-plus" @click="showDialog = true" />
    </div>

    <div class="flex gap-3 mb-5">
      <InputText v-model="search" placeholder="Rechercher..." class="flex-1 max-w-sm" />
      <Select v-model="filterRole" :options="roles" optionLabel="label" optionValue="value" placeholder="Rôle" class="w-48" />
    </div>

    <div :class="['rounded-2xl overflow-hidden backdrop-blur-xl border',
      theme.isDark ? 'bg-white/5 border-white/10' : 'bg-white/30 border-white/50']">

      <div v-if="loading" class="p-6 flex items-center justify-center">
        <i class="pi pi-spin pi-spinner text-3xl text-bordeaux-700"></i>
        <span class="ml-3 text-sm">Chargement des utilisateurs...</span>
      </div>
      
      <DataTable v-else :value="users" stripedRows>
        <Column header="Nom">
          <template #body="{ data }">
            {{ data.first_name }} {{ data.last_name }}
          </template>
        </Column>
        
        <Column field="email" header="Email" />
        
        <Column field="role" header="Rôle">
          <template #body="{ data }">
            <Tag :value="data.role" severity="secondary" />
          </template>
        </Column>
        
        <Column field="status" header="Statut">
          <template #body="{ data }">
            <Tag 
              :value="data.status === 'active' || data.status === 'actif' ? 'Actif' : (data.status === 'archived' ? 'Archivé' : 'Désactivé')" 
              :severity="data.status === 'active' || data.status === 'actif' ? 'success' : (data.status === 'archived' ? 'danger' : 'warn')" 
            />
          </template>
        </Column>
        
       <Column header="Actions">
  <template #body="{ data }">
    <div class="flex items-center gap-2">
      
      <button 
        type="button"
        @click="editUser(data)" 
        title="Modifier"
        class="p-2 rounded-lg transition-colors duration-200 hover:bg-black/5 dark:hover:bg-white/10 text-blue-600 dark:text-blue-400"
      >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
          <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
        </svg>
      </button>
      
      <button 
        type="button"
        @click="toggleUserStatus(data)"
        :title="(data.status === 'active' || data.status === 'actif') ? 'Désactiver' : 'Activer'"
        :class="[
          'p-2 rounded-lg transition-colors duration-200 hover:bg-black/5 dark:hover:bg-white/10',
          (data.status === 'active' || data.status === 'actif') ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400'
        ]"
      >
        <svg v-if="data.status === 'active' || data.status === 'actif'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
        </svg>
        <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
        </svg>
      </button>
      
      <button 
        type="button"
        @click="archiveUser(data)" 
        title="Archiver" 
        class="p-2 rounded-lg transition-colors duration-200 hover:bg-black/5 dark:hover:bg-white/10 text-rose-600 dark:text-rose-400"
      >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
          <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
        </svg>
      </button>

    </div>
  </template>
</Column>
      </DataTable>
    </div>

    <Dialog v-model:visible="showDialog" modal :header="isEditing ? 'Modifier l\'utilisateur' : 'Créer un nouvel utilisateur'" :style="{ width: '30rem' }" @hide="closeDialog">
      <form @submit.prevent="saveUser" class="flex flex-col gap-4 mt-2">
        
        <div class="grid grid-cols-2 gap-3">
          <div class="flex flex-col gap-1">
            <label class="font-semibold text-sm">Prénom</label>
            <InputText v-model="newUser.first_name" required />
          </div>
          <div class="flex flex-col gap-1">
            <label class="font-semibold text-sm">Nom</label>
            <InputText v-model="newUser.last_name" required />
          </div>
        </div>

        <div class="flex flex-col gap-1">
          <label class="font-semibold text-sm">Adresse Email</label>
          <InputText type="email" v-model="newUser.email" required />
        </div>

        <div class="flex flex-col gap-1">
          <label class="font-semibold text-sm">Téléphone</label>
          <InputText v-model="newUser.phone" placeholder="Ex: +225..." />
        </div>

        <div class="flex flex-col gap-1">
          <label class="font-semibold text-sm">Rôle</label>
          <Select v-model="newUser.role" :options="formRoles" optionLabel="label" optionValue="value" required />
        </div>

        <!-- <div class="flex flex-col gap-1">
          <label class="font-semibold text-sm">
            {{ isEditing ? 'Changer le mot de passe (Optionnel)' : 'Mot de passe initial' }}
          </label>
          <InputText type="password" v-model="newUser.password" :required="!isEditing" />
        </div> -->

        <div class="flex justify-end gap-2 mt-2">
          <Button type="button" label="Annuler" severity="secondary" text @click="closeDialog" />
          <Button type="submit" :label="isEditing ? 'Mettre à jour' : 'Enregistrer'" :loading="usersStore.loading" />
        </div>
        <pre v-if="usersStore.errors" class="text-red-500 text-xs">{{ usersStore.errors }}</pre>
      </form>
    </Dialog>
  </div>
</template>
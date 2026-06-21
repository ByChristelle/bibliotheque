<script setup>
import { ref } from 'vue'
import { useThemeStore } from '@/stores/theme'
// import InputText from 'primevue/inputtext'
// import Button from 'primevue/button'
// import DataTable from 'primevue/datatable'
// import Column from 'primevue/column'
// import Tag from 'primevue/tag'
// import Select from 'primevue/select'
// import Dialog from 'primevue/dialog' 
import { useUsersStore } from '@/stores/users'

const theme = useThemeStore()
const usersStore= useUsersStore()

const search = ref('')
const filterRole = ref(null)

const showDialog = ref(false)
const loading = ref(false) // Pour l'état du bouton d'envoi

const roles = [
  { label: 'Tous les rôles', value: null },
  { label: 'Utilisateur', value: 'user' },
  { label: 'Responsable', value: 'responsable_demande' },
  { label: 'Responsable RH', value: 'responsable_rh' },
  { label: 'Administrateur', value: 'admin' },
]

// Liste des rôles sans l'option "Tous" pour le formulaire de création
const formRoles = roles.filter(r => r.value !== null)

const users = ref([
  { id: 1, name: 'Jean Kouassi', email: 'jean@email.com', role: 'user', status: 'actif', created: '01/01/2026' },
  { id: 2, name: 'Marie Tall', email: 'marie@email.com', role: 'responsable_demande', status: 'actif', created: '15/02/2026' },
  { id: 3, name: 'Admin', email: 'admin@biblio.com', role: 'admin', status: 'actif', created: '01/01/2026' },
])

// Pour la création d'un nouvelle utilisateur
const newUser = ref({
  first_name: '',
  last_name: '',
  email: '',
  phone: '',  
  role: 'user', 
  password: '',
  avatar: null 
})

const onFileChange = (event) => {
  const file = event.target.files[0]
  if(file){
    newUser.value.avatar=file
  } 
}

const addUser = async () => {
  // On crée un FormData pour pouvoir envoyer le fichier
  const formData = new FormData()
  formData.append('first_name', newUser.value.first_name)
  formData.append('last_name', newUser.value.last_name)
  formData.append('email', newUser.value.email)
  // formData.append('phone', newUser.value.phone)
  formData.append('role', newUser.value.role)
  formData.append('password', newUser.value.password)

  if (newUser.value.phone) {
  formData.append('phone', newUser.value.phone)
}
  
  // if (newUser.value.avatar) {
  //   formData.append('avatar', newUser.value.avatar)
  // }

  // On envoie le FormData au store
  const success = await usersStore.createUser(formData)
  
  if (success) {
    showDialog.value = false
    newUser.value = { first_name: '', last_name: '', email: '', phone: '', role: 'user', password: '', avatar: null }
  }
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
      <DataTable :value="users" stripedRows>
        <Column field="name" header="Nom" />
        <Column field="email" header="Email" />
        <Column field="role" header="Rôle">
          <template #body="{ data }">
            <Tag :value="data.role" severity="secondary" />
          </template>
        </Column>
        <Column field="status" header="Statut">
          <template #body="{ data }">
            <Tag :value="data.status" :severity="data.status === 'actif' ? 'success' : 'danger'" />
          </template>
        </Column>
        <Column field="created" header="Inscription" />
        <Column header="Actions">
          <template #body>
            <div class="flex gap-2">
              <Button icon="pi pi-pencil" text rounded size="small" />
              <Button icon="pi pi-shield" text rounded size="small" severity="warn" title="Modifier le rôle" />
              <Button icon="pi pi-ban" text rounded size="small" severity="warning" />
              <Button icon="pi pi-trash" text rounded size="small" severity="danger" />
            </div>
          </template>
        </Column>
      </DataTable>
    </div>
    <!-- Modal de création  -->
   <Dialog v-model:visible="showDialog" modal header="Créer un nouvel utilisateur" :style="{ width: '30rem' }">
    <form @submit.prevent="addUser" class="flex flex-col gap-4 mt-2" enctype="multipart/form-data">
      
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
<!-- 
      <div class="flex flex-col gap-1">
        <label class="font-semibold text-sm">Photo de profil</label> -->
        <!-- Input type file classique, customisé ou PrimeVue FileUpload en mode simple -->
        <!-- <input type="file" accept="image/*" @change="onFileChange" class="text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-bordeaux-50 file:text-bordeaux-700 hover:file:bg-bordeaux-100" />
      </div> -->

      <div class="flex flex-col gap-1">
        <label class="font-semibold text-sm">Mot de passe initial</label>
        <InputText type="password" v-model="newUser.password" required />
      </div>

      <div class="flex justify-end gap-2 mt-2">
        <Button type="button" label="Annuler" severity="secondary" text @click="showDialog = false" />
        <Button type="submit" label="Enregistrer" :loading="usersStore.loading" />
        <pre v-if="usersStore.errors" class="text-red-500 text-xs">{{ usersStore.errors }}</pre>

      </div>
    </form>
  </Dialog>
  </div>
</template>
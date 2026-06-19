<script setup>
import { ref } from 'vue'
import { useThemeStore } from '@/stores/theme'
import InputText from 'primevue/inputtext'
import Button from 'primevue/button'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Tag from 'primevue/tag'

const theme = useThemeStore()

const search = ref('')

const users = ref([
  { id: 1, name: 'Jean Kouassi', email: 'jean@email.com', role: 'Utilisateur', status: 'actif', created: '01/01/2026' },
  { id: 2, name: 'Marie Tall', email: 'marie@email.com', role: 'Responsable', status: 'actif', created: '15/02/2026' },
  { id: 3, name: 'Paul Diallo', email: 'paul@email.com', role: 'Utilisateur', status: 'inactif', created: '10/03/2026' },
  { id: 4, name: 'Sophie Kone', email: 'sophie@email.com', role: 'Utilisateur', status: 'actif', created: '22/04/2026' },
])
</script>

<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 :class="['text-2xl font-bold', theme.isDark ? 'text-bordeaux-100' : 'text-bordeaux-900']">
          Utilisateurs
        </h1>
        <p :class="['text-sm', theme.isDark ? 'text-bordeaux-400' : 'text-bordeaux-600']">
          {{ users.length }} comptes enregistrés
        </p>
      </div>
      <Button label="Créer un compte" icon="pi pi-user-plus" />
    </div>

    <!-- Barre de recherche -->
    <div class="mb-5">
      <InputText v-model="search" placeholder="Rechercher un utilisateur..." class="w-72" />
    </div>

    <!-- Tableau -->
    <div :class="['rounded-2xl overflow-hidden backdrop-blur-xl border',
      theme.isDark ? 'bg-white/5 border-white/10' : 'bg-white/30 border-white/50']">
      <DataTable :value="users" stripedRows>
        <Column field="name" header="Nom" />
        <Column field="email" header="Email" />
        <Column field="role" header="Rôle" />
        <Column field="status" header="Statut">
          <template #body="{ data }">
            <Tag
              :value="data.status"
              :severity="data.status === 'actif' ? 'success' : 'danger'"
            />
          </template>
        </Column>
        <Column field="created" header="Inscription" />
        <Column header="Actions">
          <template #body>
            <div class="flex gap-2">
              <Button icon="pi pi-pencil" text rounded size="small" />
              <Button icon="pi pi-ban" text rounded size="small" severity="warning" />
              <Button icon="pi pi-trash" text rounded size="small" severity="danger" />
            </div>
          </template>
        </Column>
      </DataTable>
    </div>
  </div>
</template>

<script setup>
import { RouterView } from 'vue-router'
import Toast from 'primevue/toast'
import ConfirmDialog from 'primevue/confirmdialog'
import { onMounted } from 'vue';

</script>

<template>
  <RouterView />
  <Toast />
  <ConfirmDialog />
</template>

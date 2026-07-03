<script setup>
import { useThemeStore } from '@/stores/theme'
import Button from 'primevue/button'
import Dialog from 'primevue/dialog'

const theme = defineModel('theme', { default: null })
const themeStore = useThemeStore()
const isDark = computed(() => themeStore.isDark)

import { computed } from 'vue'

const props = defineProps({
  visible:  { type: Boolean, default: false },
  demande:  { type: Object,  default: null  },
})
const emit = defineEmits(['update:visible'])

function close() { emit('update:visible', false) }

const docTypeLabel = { livre:'Livre', memoire:'Mémoire', these:'Thèse', article:'Article', revue:'Revue', rapport:'Rapport', guide:'Guide', autre:'Autre' }
const langLabel    = { fr:'Français', en:'Anglais', autre:'Autre' }
const statusLabel  = { pending:'En attente', approved_by_manager:'Validée · resp.', rejected_by_manager:'Refusée · resp.', published:'Publiée', rejected:'Rejetée', assigned:'Affectée' }
const statusClass  = { pending:'bg-yellow-100 text-yellow-700', approved_by_manager:'bg-green-100 text-green-700', rejected_by_manager:'bg-red-100 text-red-700', published:'bg-blue-100 text-blue-700', rejected:'bg-red-100 text-red-700', assigned:'bg-blue-100 text-blue-700' }

const dtl = t => docTypeLabel[t] || t
const ll  = l => langLabel[l]    || l
const sl  = s => statusLabel[s]  || s
const sc  = s => statusClass[s]  || 'bg-gray-100 text-gray-600'
</script>

<template>
  <Dialog :visible="visible" @update:visible="close" header="Détail de la demande" :modal="true" :style="{ width: '680px' }" :scrollable="true">
    <div v-if="demande" class="flex flex-col gap-6 py-2">

      <!-- Bandeau titre -->
      <div class="flex items-start justify-between gap-3 p-4 rounded-xl" :class="isDark ? 'bg-white/5' : 'bg-slate-50'">
        <div>
          <p class="text-xs font-semibold uppercase tracking-widest text-[#94A3B8] mb-1">Titre de la demande</p>
          <h3 class="text-base font-bold" :class="isDark ? 'text-white' : 'text-[#2D2D2D]'">{{ demande.title }}</h3>
          <p class="text-xs mt-1 text-[#94A3B8]">
            Soumis par
            <span class="font-medium" :class="isDark ? 'text-white/70' : 'text-[#555]'">
              {{ demande.applicant ? `${demande.applicant.first_name} ${demande.applicant.last_name}` : 'Inconnu' }}
            </span>
            · {{ new Date(demande.created_at).toLocaleDateString('fr-FR', { day:'2-digit', month:'long', year:'numeric' }) }}
          </p>
        </div>
        <span :class="['text-xs px-3 py-1.5 rounded-full font-semibold shrink-0', sc(demande.status)]">{{ sl(demande.status) }}</span>
      </div>

      <div v-if="demande.reference_brouillon" class="flex flex-col gap-5">

        <!-- Identification -->
        <div>
          <div class="flex items-center gap-2 mb-3">
            <span class="text-xs font-bold uppercase tracking-widest text-[#94A3B8]">Identification</span>
            <span class="flex-1 h-px" :class="isDark ? 'bg-white/10' : 'bg-[#ECECEC]'"></span>
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div v-for="item in [
              { label: 'Sous-titre',      val: demande.reference_brouillon.subtitle },
              { label: 'Auteur(s)',        val: demande.reference_brouillon.authors },
              { label: 'Catégorie',        val: demande.reference_brouillon.category?.name },
              { label: 'Type de document', val: dtl(demande.reference_brouillon.document_type) },
            ]" :key="item.label" class="p-3 rounded-xl" :class="isDark ? 'bg-white/5' : 'bg-white border border-[#ECECEC]'">
              <p class="text-xs text-[#94A3B8] mb-0.5">{{ item.label }}</p>
              <p class="text-sm font-medium" :class="isDark ? 'text-white' : 'text-[#2D2D2D]'">{{ item.val || '—' }}</p>
            </div>
          </div>
        </div>

        <!-- Détails bibliographiques -->
        <div>
          <div class="flex items-center gap-2 mb-3">
            <span class="text-xs font-bold uppercase tracking-widest text-[#94A3B8]">Détails bibliographiques</span>
            <span class="flex-1 h-px" :class="isDark ? 'bg-white/10' : 'bg-[#ECECEC]'"></span>
          </div>
          <div class="grid grid-cols-3 gap-3">
            <div v-for="item in [
              { label: 'Langue',   val: ll(demande.reference_brouillon.language) },
              { label: 'Année',    val: demande.reference_brouillon.publication_year },
              { label: 'Pages',    val: demande.reference_brouillon.pages },
              { label: 'Éditeur',  val: demande.reference_brouillon.publisher },
              { label: 'ISBN',     val: demande.reference_brouillon.isbn, span: 2 },
            ]" :key="item.label"
              class="p-3 rounded-xl"
              :class="[isDark ? 'bg-white/5' : 'bg-white border border-[#ECECEC]', item.span ? `col-span-${item.span}` : '']">
              <p class="text-xs text-[#94A3B8] mb-0.5">{{ item.label }}</p>
              <p class="text-sm font-medium" :class="isDark ? 'text-white' : 'text-[#2D2D2D]'">{{ item.val || '—' }}</p>
            </div>
          </div>
        </div>

        <!-- Contenu -->
        <div>
          <div class="flex items-center gap-2 mb-3">
            <span class="text-xs font-bold uppercase tracking-widest text-[#94A3B8]">Contenu</span>
            <span class="flex-1 h-px" :class="isDark ? 'bg-white/10' : 'bg-[#ECECEC]'"></span>
          </div>
          <div class="flex flex-col gap-3">
            <div class="p-3 rounded-xl" :class="isDark ? 'bg-white/5' : 'bg-white border border-[#ECECEC]'">
              <p class="text-xs text-[#94A3B8] mb-1">Mots-clés</p>
              <div v-if="demande.reference_brouillon.keywords" class="flex flex-wrap gap-1.5">
                <span v-for="kw in demande.reference_brouillon.keywords.split(',')" :key="kw"
                  class="text-xs px-2.5 py-1 rounded-full"
                  :class="isDark ? 'bg-white/10 text-white/70' : 'bg-[#F8F6F6] text-[#555] border border-[#ECECEC]'">
                  {{ kw.trim() }}
                </span>
              </div>
              <p v-else class="text-sm" :class="isDark ? 'text-white/40' : 'text-[#94A3B8]'">—</p>
            </div>
            <div class="p-3 rounded-xl" :class="isDark ? 'bg-white/5' : 'bg-white border border-[#ECECEC]'">
              <p class="text-xs text-[#94A3B8] mb-1">Résumé</p>
              <p class="text-sm leading-relaxed" :class="isDark ? 'text-white/80' : 'text-[#2D2D2D]'">
                {{ demande.reference_brouillon.abstract || '—' }}
              </p>
            </div>
          </div>
        </div>

        <!-- Fichiers -->
        <div v-if="demande.reference_brouillon.cover_image || demande.reference_brouillon.file_path">
          <div class="flex items-center gap-2 mb-3">
            <span class="text-xs font-bold uppercase tracking-widest text-[#94A3B8]">Fichiers joints</span>
            <span class="flex-1 h-px" :class="isDark ? 'bg-white/10' : 'bg-[#ECECEC]'"></span>
          </div>
          <div class="flex gap-4 flex-wrap items-start">
            <div v-if="demande.reference_brouillon.cover_image" class="flex flex-col items-center gap-2">
              <img :src="`http://localhost:8000/storage/${demande.reference_brouillon.cover_image}`"
                alt="Couverture" class="w-24 h-36 object-cover rounded-xl shadow" />
              <p class="text-xs text-[#94A3B8]">Couverture</p>
            </div>
            <div v-if="demande.reference_brouillon.file_path">
              <a :href="`http://localhost:8000/storage/${demande.reference_brouillon.file_path}`" target="_blank"
                class="flex items-center gap-2 px-4 py-3 rounded-xl text-sm font-medium no-underline transition-all"
                :class="isDark ? 'bg-white/10 text-white hover:bg-white/15' : 'bg-[#F8F6F6] text-[#2D2D2D] border border-[#ECECEC] hover:bg-[#ECECEC]'">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-4 h-4 text-red-500">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
                Voir le document PDF
              </a>
            </div>
          </div>
        </div>

      </div>
      <div v-else class="text-center py-8 text-sm text-[#94A3B8]">Aucune information de référence disponible.</div>

    </div>
    <template #footer>
      <Button label="Fermer" text @click="close" />
    </template>
  </Dialog>
</template>

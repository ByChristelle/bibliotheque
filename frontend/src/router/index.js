import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth' // <-- Correction de l'import manquant

import PublicLayout from '@/layouts/PublicLayout.vue'
import AuthLayout from '@/layouts/AuthLayout.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    // ── Public ──────────────────────────────────────────
    {
      path: '/',
      component: PublicLayout,
      children: [
        { path: '', name: 'home', component: () => import('@/views/public/HomeView.vue') },
        { path: 'catalogue', name: 'catalogue', component: () => import('@/views/public/CatalogueView.vue') },
        { path: 'references/:id', name: 'reference-detail', component: () => import('@/views/public/ReferenceDetailView.vue') },
      ],
    },

    // ── Auth ────────────────────────────────────────────
    { path: '/connexion', name: 'login', component: () => import('@/views/auth/LoginView.vue'), meta: { requiresGuest: true } },
    { path: '/inscription', name: 'register', component: () => import('@/views/auth/RegisterView.vue'), meta: { requiresGuest: true } },

    // ── Utilisateur inscrit ─────────────────────────────
    {
      path: '/espace',
      component: AuthLayout,
      meta: { requiresAuth: true, allowedRoles: ['user'] }, // <-- Utilisation cohérente de allowedRoles
      children: [
        { path: 'dashboard', name: 'user-dashboard', component: () => import('@/views/user/DashboardView.vue'), meta: { title: 'Tableau de bord' } },
        { path: 'mes-demandes', name: 'mes-demandes', component: () => import('@/views/user/MesDemandesView.vue'), meta: { title: 'Mes demandes' } },
        { path: 'nouvelle-demande', name: 'nouvelle-demande', component: () => import('@/views/user/NouvelleDemandeView.vue'), meta: { title: 'Nouvelle demande' } },
      ],
    },

    // ── Responsable ─────────────────────────────────────
    {
      path: '/responsable',
      component: AuthLayout,
      meta: { requiresAuth: true, allowedRoles: ['responsable_demande'] },
      children: [
        { path: 'dashboard', name: 'responsable-dashboard', component: () => import('@/views/responsable/DashboardResponsableView.vue'), meta: { title: 'Tableau de bord' } },
        { path: 'demandes', name: 'demandes-affectees', component: () => import('@/views/responsable/DemandesAffecteesView.vue'), meta: { title: 'Demandes affectées' } },
      ],
    },

    // ── RH ──────────────────────────────────────────────
    {
      path: '/rh',
      component: AuthLayout,
      meta: { requiresAuth: true, allowedRoles: ['responsable_rh'] },
      children: [
        { path: 'dashboard', name: 'rh-dashboard', component: () => import('@/views/rh/DashboardRHView.vue'), meta: { title: 'Tableau de bord RH' } },
        { path: 'utilisateurs', name: 'rh-utilisateurs', component: () => import('@/views/rh/GestionUtilisateursView.vue'), meta: { title: 'Utilisateurs' } },
      ],
    },

    // ── Admin ────────────────────────────────────────────
    {
      path: '/admin',
      component: AuthLayout,
      meta: { requiresAuth: true, allowedRoles: ['admin'] },
      children: [
        { path: 'dashboard', name: 'admin-dashboard', component: () => import('@/views/admin/DashboardAdminView.vue'), meta: { title: 'Tableau de bord' } },
        { path: 'demandes', name: 'admin-demandes', component: () => import('@/views/admin/GestionDemandesView.vue'), meta: { title: 'Demandes' } },
        { path: 'references', name: 'admin-references', component: () => import('@/views/admin/GestionReferencesView.vue'), meta: { title: 'Références' } },
        { path: 'utilisateurs', name: 'admin-utilisateurs', component: () => import('@/views/admin/GestionUtilisateursView.vue'), meta: { title: 'Utilisateurs' } },
        { path: 'categories', name: 'admin-categories', component: () => import('@/views/admin/GestionCategoriesView.vue'), meta: { title: 'Catégories' } },
        { path: 'logs', name: 'admin-logs', component: () => import('@/views/admin/LogsActiviteView.vue'), meta: { title: "Journaux d'activité" } },
      ],
    },

    // ── Fallback ─────────────────────────────────────────
    { path: '/:pathMatch(.*)*', redirect: '/' },
  ],
})

router.beforeEach((to, from, next) => {
  const auth = useAuthStore()

  // 1. Si déjà connecté -> interdit d'aller sur login/register
  if (to.matched.some(record => record.meta.requiresGuest) && auth.user) {
    const safeRoute = auth.redirectUserByRole(auth.user.role)
    return next(safeRoute)
  }

  // 2. Si la route (ou un de ses parents) exige d'être connecté
  if (to.matched.some(record => record.meta.requiresAuth) && !auth.user) {
    return next('/connexion')
  }

  // 3. Vérification approfondie des rôles autorisés (parents inclus)
  const structuralRoles = to.matched.reduce((roles, record) => {
    if (record.meta.allowedRoles) {
      roles.push(...record.meta.allowedRoles)
    }
    return roles;
  }, [])

  if (structuralRoles.length > 0 && !structuralRoles.includes(auth.user?.role)) {
    // Rôle invalide détecté -> Expulsion vers sa zone autorisée
    const safeRoute = auth.redirectUserByRole(auth.user?.role)
    return next(safeRoute)
  }

  next()
})

export default router
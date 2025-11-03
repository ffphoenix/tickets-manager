import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
    },
    {
      path: '/about',
      name: 'about',
      // route level code-splitting
      // this generates a separate chunk (About.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: () => import('../views/AboutView.vue'),
    },
    {
      path: '/events',
      name: 'events-list',
      component: () => import('../views/Events/EventsListView.vue'),
    },
    {
      path: '/events/new',
      name: 'event-create',
      component: () => import('../views/Events/EventCreateView.vue'),
    },
    {
      path: '/events/:id',
      name: 'event-detail',
      component: () => import('../views/Events/EventDetailView.vue'),
      props: true,
    },
  ],
})

export default router

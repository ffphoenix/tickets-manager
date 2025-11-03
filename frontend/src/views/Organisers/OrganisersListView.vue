<script setup lang="ts">
import { onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useOrganisersStore } from '@/stores/organisers'

const router = useRouter()
const organisersStore = useOrganisersStore()

const organisers = computed(() => organisersStore.all)
const loading = computed(() => organisersStore.isLoading())
const error = computed(() => organisersStore.error)

async function load() {
  await organisersStore.fetchAll()
}

async function onDelete(id: string) {
  if (!confirm('Delete this organiser?')) return
  const ok = await organisersStore.remove(id)
  if (!ok) alert(organisersStore.error || 'Delete failed')
}

onMounted(load)
</script>

<template>
  <main>
    <h1>Organisers</h1>
    <p>
      <button @click="() => router.push('/organisers/new')">Create Organiser</button>
    </p>

    <div v-if="loading">Loading…</div>
    <div v-else-if="error" style="color: red">{{ error }}</div>

    <table v-else border="1" cellpadding="6" cellspacing="0">
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Created</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="o in organisers" :key="o.id">
          <td>{{ o.id }}</td>
          <td>{{ o.name }}</td>
          <td>{{ new Date(o.createdAt).toLocaleString() }}</td>
          <td>
            <button @click="() => router.push(`/organisers/${o.id}`)">View</button>
            <button @click="() => router.push(`/organisers/${o.id}/edit`)">Edit</button>
            <button @click="() => onDelete(o.id)">Delete</button>
          </td>
        </tr>
        <tr v-if="organisers.length === 0">
          <td colspan="4">No organisers found</td>
        </tr>
      </tbody>
    </table>
  </main>
</template>

<style scoped>
main { padding: 1rem; }
 table { width: 100%; }
 th { text-align: left; }
 button { margin-right: .5rem; }
</style>

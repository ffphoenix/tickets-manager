<script setup lang="ts">
import { onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useEventsStore } from '@/stores/events'

const router = useRouter()
const eventsStore = useEventsStore()

const events = computed(() => eventsStore.all)
const loading = computed(() => eventsStore.isLoading())
const error = computed(() => eventsStore.error)

async function load() {
  await eventsStore.fetchAll()
}

async function onDelete(id: string) {
  if (!confirm('Delete this event?')) return
  const ok = await eventsStore.remove(id)
  if (!ok) alert(eventsStore.error || 'Delete failed')
}

function toLocal(iso: string) {
  try { return new Date(iso).toLocaleString() } catch { return iso }
}

onMounted(load)
</script>

<template>
  <main>
    <h1>Events</h1>
    <p>
      <button @click="() => router.push('/events/new')">Create Event</button>
    </p>

    <div v-if="loading">Loading…</div>
    <div v-else-if="error" style="color: red">{{ error }}</div>

    <table v-else border="1" cellpadding="6" cellspacing="0">
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Organiser</th>
          <th>Start</th>
          <th>End</th>
          <th>Created</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="e in events" :key="e.id">
          <td>{{ e.id }}</td>
          <td>{{ e.name }}</td>
          <td>{{ e.organiserId }}</td>
          <td>{{ toLocal(e.startAt) }}</td>
          <td>{{ toLocal(e.endAt) }}</td>
          <td>{{ toLocal(e.createdAt) }}</td>
          <td>
            <button @click="() => router.push(`/events/${e.id}`)">View</button>
            <button @click="() => router.push(`/events/${e.id}/edit`)">Edit</button>
            <button @click="() => onDelete(e.id)">Delete</button>
          </td>
        </tr>
        <tr v-if="events.length === 0">
          <td colspan="7">No events found</td>
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

<script setup lang="ts">
import { onMounted, computed, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useEventsStore } from '@/stores/events'

const route = useRoute()
const router = useRouter()
const eventsStore = useEventsStore()

const id = computed(() => String(route.params.id || ''))
const event = computed(() => (id.value ? eventsStore.byId(id.value) : null))
const loading = computed(() => (id.value ? eventsStore.isLoading(id.value) : false))
const error = computed(() => eventsStore.error)

function toLocal(iso: string) {
  try { return new Date(iso).toLocaleString() } catch { return iso }
}

async function load() {
  if (id.value) await eventsStore.fetchOne(id.value)
}

onMounted(load)

watch(() => route.params.id, () => {
  load()
})
</script>

<template>
  <main>
    <div style="display:flex; gap:.5rem; align-items:center; margin-bottom: .5rem">
      <button @click="() => router.push('/events')">← Back to Events</button>
      <button v-if="id" @click="() => router.push(`/events/${id}/edit`)">Edit</button>
    </div>
    <h1>Event Details</h1>

    <div v-if="loading">Loading…</div>
    <div v-else-if="error" style="color: red">{{ error }}</div>

    <div v-else-if="event">
      <p><strong>ID:</strong> {{ event.id }}</p>
      <p><strong>Name:</strong> {{ event.name }}</p>
      <p><strong>Organiser ID:</strong> {{ event.organiserId }}</p>
      <p><strong>Start At:</strong> {{ toLocal(event.startAt) }}</p>
      <p><strong>End At:</strong> {{ toLocal(event.endAt) }}</p>
      <p><strong>Created At:</strong> {{ toLocal(event.createdAt) }}</p>
      <p>
        <button @click="() => router.push(`/events/${event.id}/edit`)">Edit</button>
      </p>
    </div>
  </main>
</template>

<style scoped>
main { padding: 1rem; }
button { margin-bottom: 1rem; }
</style>

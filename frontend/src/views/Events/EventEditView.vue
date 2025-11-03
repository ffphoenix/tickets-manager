<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useEventsStore } from '@/stores/events'

const route = useRoute()
const router = useRouter()
const eventsStore = useEventsStore()

const id = computed(() => String(route.params.id || ''))
const eventItem = computed(() => (id.value ? eventsStore.byId(id.value) : null))

const name = ref('')
const startAtLocal = ref('') // yyyy-MM-ddTHH:mm
const endAtLocal = ref('')

const fetching = computed(() => (id.value ? eventsStore.isLoading(id.value) : false))
const loading = computed(() => (id.value ? eventsStore.isUpdating(id.value) : false))
const error = computed(() => eventsStore.error)

function toLocalInputValue(iso: string): string {
  try {
    if (!iso) return ''
    const d = new Date(iso)
    const pad = (n: number) => String(n).padStart(2, '0')
    const yyyy = d.getFullYear()
    const mm = pad(d.getMonth() + 1)
    const dd = pad(d.getDate())
    const hh = pad(d.getHours())
    const min = pad(d.getMinutes())
    return `${yyyy}-${mm}-${dd}T${hh}:${min}`
  } catch {
    return ''
  }
}

function toIso(local: string): string {
  if (!local) return ''
  const d = new Date(local)
  return d.toISOString()
}

async function load() {
  if (!id.value) return
  const existed = eventsStore.byId(id.value)
  if (!existed) await eventsStore.fetchOne(id.value)
  const current = eventsStore.byId(id.value)
  if (current) {
    name.value = current.name
    startAtLocal.value = toLocalInputValue(current.startAt)
    endAtLocal.value = toLocalInputValue(current.endAt)
  }
}

async function submitForm() {
  eventsStore.error = null
  const payload = {
    name: name.value.trim(),
    startAt: toIso(startAtLocal.value),
    endAt: toIso(endAtLocal.value),
  }
  const updated = await eventsStore.update(id.value, payload)
  if (updated) router.push(`/events/${updated.id}`)
}

onMounted(load)
watch(() => route.params.id, () => { load() })
</script>

<template>
  <main>
    <button @click="() => router.push(`/events/${id}`)">← Back to Details</button>
    <h1>Edit Event</h1>

    <div v-if="fetching">Loading…</div>
    <div v-else>
      <form @submit.prevent="submitForm" style="max-width: 600px">
        <div style="margin-bottom: .5rem">
          <label>Name<br />
            <input v-model="name" required />
          </label>
        </div>
        <div style="margin-bottom: .5rem">
          <label>Start At<br />
            <input v-model="startAtLocal" type="datetime-local" required />
          </label>
        </div>
        <div style="margin-bottom: .5rem">
          <label>End At<br />
            <input v-model="endAtLocal" type="datetime-local" required />
          </label>
        </div>

        <div v-if="error" style="color: red; margin-bottom: .5rem">{{ error }}</div>

        <button type="submit" :disabled="loading">{{ loading ? 'Saving…' : 'Save' }}</button>
        <button type="button" @click="() => router.push(`/events/${id}`)">Cancel</button>
      </form>
    </div>
  </main>
</template>

<style scoped>
main { padding: 1rem; }
input { width: 100%; padding: .5rem; }
button { margin-right: .5rem; }
</style>

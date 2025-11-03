<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { type CreateEventPayload } from '@/services/eventsApi.ts'
import { useRouter } from 'vue-router'
import { useEventsStore } from '@/stores/events'
import { useOrganisersStore } from '@/stores/organisers'

const router = useRouter()
const eventsStore = useEventsStore()
const organisersStore = useOrganisersStore()

const organiserId = ref('')
const name = ref('')
const startAtLocal = ref('') // yyyy-MM-ddTHH:mm
const endAtLocal = ref('')
const eventId = ref('') // optional

const loading = computed(() => eventsStore.creating)
const error = computed(() => eventsStore.error)

const organisers = computed(() => organisersStore.all)
const organisersLoading = computed(() => organisersStore.loading)
const organisersError = computed(() => organisersStore.error)

onMounted(() => {
  organisersStore.fetchAll()
})

function toIso(local: string): string {
  // local is like '2025-11-03T12:00'
  if (!local) return ''
  const d = new Date(local)
  return d.toISOString()
}

async function submitForm() {
  eventsStore.error = null
  const payload: CreateEventPayload = {
    organiserId: organiserId.value.trim(),
    name: name.value.trim(),
    startAt: toIso(startAtLocal.value),
    endAt: toIso(endAtLocal.value),
  }
  if (eventId.value.trim()) payload.eventId = eventId.value.trim()

  const created = await eventsStore.create(payload)
  if (created) router.push(`/events/${created.id}`)
}
</script>

<template>
  <main>
    <h1>Create Event</h1>
    <form @submit.prevent="submitForm" style="max-width: 600px">
      <div style="margin-bottom: .5rem">
        <label>Organiser<br />
          <template v-if="organisersLoading">
            <div>Loading organisers…</div>
          </template>
          <template v-else-if="organisersError">
            <div style="color: #c00">{{ organisersError }}</div>
            <!-- Fallback to manual input when organisers fail to load -->
            <input v-model="organiserId" required placeholder="Organiser UUID" />
          </template>
          <template v-else-if="organisers.length > 0">
            <select v-model="organiserId" required>
              <option value="" disabled>Select organiser…</option>
              <option v-for="o in organisers" :key="o.id" :value="o.id">
                {{ o.name }} ({{ o.id }})
              </option>
            </select>
          </template>
          <template v-else>
            <!-- No organisers available yet -->
            <input v-model="organiserId" required placeholder="Organiser UUID" />
            <small>No organisers found. Enter an existing organiser UUID manually.</small>
          </template>
        </label>
      </div>
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

      <button type="submit" :disabled="loading">{{ loading ? 'Creating…' : 'Create' }}</button>
      <button type="button" @click="() => router.push('/events')">Cancel</button>
    </form>
  </main>
</template>

<style scoped>
main { padding: 1rem; }
input { width: 100%; padding: .5rem; }
button { margin-right: .5rem; }
</style>

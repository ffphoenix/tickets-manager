<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useOrganisersStore } from '@/stores/organisers'
import type { UpdateOrganiserPayload } from '@/services/organisersApi'

const route = useRoute()
const router = useRouter()
const organisersStore = useOrganisersStore()

const id = computed(() => String(route.params.id || ''))
const organiser = computed(() => (id.value ? organisersStore.byId(id.value) : null))

const name = ref('')

const loading = computed(() => (id.value ? organisersStore.isUpdating(id.value) : false))
const fetching = computed(() => (id.value ? organisersStore.isLoading(id.value) : false))
const error = computed(() => organisersStore.error)

async function load() {
  if (!id.value) return
  const existed = organisersStore.byId(id.value)
  if (!existed) await organisersStore.fetchOne(id.value)
  const current = organisersStore.byId(id.value)
  if (current) {
    name.value = current.name
  }
}

async function submitForm() {
  organisersStore.error = null
  const payload: UpdateOrganiserPayload = { name: name.value.trim() }
  const updated = await organisersStore.update(id.value, payload)
  if (updated) router.push(`/organisers/${updated.id}`)
}

onMounted(load)
watch(() => route.params.id, () => { load() })
</script>

<template>
  <main>
    <button @click="() => router.push(`/organisers/${id}`)">← Back to Details</button>
    <h1>Edit Organiser</h1>

    <div v-if="fetching">Loading…</div>
    <div v-else>
      <form @submit.prevent="submitForm" style="max-width: 480px">
        <div style="margin-bottom: .5rem">
          <label>Name<br />
            <input v-model="name" required />
          </label>
        </div>

        <div v-if="error" style="color: red; margin-bottom: .5rem">{{ error }}</div>

        <button type="submit" :disabled="loading">{{ loading ? 'Saving…' : 'Save' }}</button>
        <button type="button" @click="() => router.push(`/organisers/${id}`)">Cancel</button>
      </form>
    </div>
  </main>
</template>

<style scoped>
main { padding: 1rem; }
input { width: 100%; padding: .5rem; }
button { margin-right: .5rem; }
</style>

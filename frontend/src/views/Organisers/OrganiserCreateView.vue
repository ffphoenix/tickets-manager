<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useOrganisersStore } from '@/stores/organisers'
import type { CreateOrganiserPayload } from '@/services/organisersApi'

const router = useRouter()
const organisersStore = useOrganisersStore()

const name = ref('')
const organiserId = ref('') // optional client-provided id

const loading = computed(() => organisersStore.creating)
const error = computed(() => organisersStore.error)

async function submitForm() {
  organisersStore.error = null
  const payload: CreateOrganiserPayload = { name: name.value.trim() }
  if (organiserId.value.trim()) payload.organiserId = organiserId.value.trim()

  const created = await organisersStore.create(payload)
  if (created) router.push(`/organisers/${created.id}`)
}
</script>

<template>
  <main>
    <button @click="() => router.push('/organisers')">← Back to Organisers</button>
    <h1>Create Organiser</h1>
    <form @submit.prevent="submitForm" style="max-width: 480px">
      <div style="margin-bottom: .5rem">
        <label>Name<br />
          <input v-model="name" required />
        </label>
      </div>
      <div v-if="error" style="color: red; margin-bottom: .5rem">{{ error }}</div>

      <button type="submit" :disabled="loading">{{ loading ? 'Creating…' : 'Create' }}</button>
      <button type="button" @click="() => router.push('/organisers')">Cancel</button>
    </form>
  </main>
</template>

<style scoped>
main { padding: 1rem; }
input { width: 100%; padding: .5rem; }
button { margin-right: .5rem; }
</style>

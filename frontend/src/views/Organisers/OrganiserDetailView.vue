<script setup lang="ts">
import { onMounted, computed, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useOrganisersStore } from '@/stores/organisers'

const route = useRoute()
const router = useRouter()
const organisersStore = useOrganisersStore()

const id = computed(() => String(route.params.id || ''))
const organiser = computed(() => (id.value ? organisersStore.byId(id.value) : null))
const loading = computed(() => (id.value ? organisersStore.isLoading(id.value) : false))
const error = computed(() => organisersStore.error)

async function load() {
  if (id.value) await organisersStore.fetchOne(id.value)
}

onMounted(load)
watch(() => route.params.id, () => { load() })
</script>

<template>
  <main>
    <button @click="() => router.push('/organisers')">← Back to Organisers</button>
    <h1>Organiser Details</h1>

    <div v-if="loading">Loading…</div>
    <div v-else-if="error" style="color: red">{{ error }}</div>

    <div v-else-if="organiser">
      <p><strong>ID:</strong> {{ organiser.id }}</p>
      <p><strong>Name:</strong> {{ organiser.name }}</p>
      <p><strong>Created At:</strong> {{ new Date(organiser.createdAt).toLocaleString() }}</p>

      <p>
        <button @click="() => router.push(`/organisers/${organiser.id}/edit`)">Edit</button>
      </p>
    </div>
  </main>
</template>

<style scoped>
main { padding: 1rem; }
button { margin-bottom: 1rem; }
</style>

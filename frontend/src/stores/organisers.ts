import { defineStore } from 'pinia'
import { OrganisersApi, type OrganiserDto } from '@/services/organisersApi'

export const useOrganisersStore = defineStore('organisers', {
  state: () => ({
    items: {} as Record<string, OrganiserDto>,
    ids: [] as string[],
    loading: false as boolean,
    error: null as string | null,
  }),

  getters: {
    all(state): OrganiserDto[] {
      return state.ids.map((id) => state.items[id]).filter(Boolean)
    },
    byId: (state) => (id: string): OrganiserDto | null => state.items[id] ?? null,
  },

  actions: {
    async fetchAll(): Promise<void> {
      this.loading = true
      this.error = null
      try {
        const list = await OrganisersApi.list()
        const nextItems: Record<string, OrganiserDto> = {}
        const nextIds: string[] = []
        for (const o of list) {
          nextItems[o.id] = o
          nextIds.push(o.id)
        }
        this.items = nextItems
        this.ids = nextIds
      } catch (e: any) {
        this.error = e?.message || 'Failed to load organisers'
      } finally {
        this.loading = false
      }
    },
  },
})

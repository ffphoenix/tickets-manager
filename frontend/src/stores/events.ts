import { defineStore } from 'pinia'
import { EventsApi, type CreateEventPayload, type EventDto } from '@/services/eventsApi.ts'

export const useEventsStore = defineStore('events', {
  state: () => ({
    items: {} as Record<string, EventDto>,
    ids: [] as string[],
    loadingList: false as boolean,
    loadingById: {} as Record<string, boolean>,
    creating: false as boolean,
    deletingById: {} as Record<string, boolean>,
    error: null as string | null,
  }),

  getters: {
    all(state): EventDto[] {
      return state.ids.map((id) => state.items[id]).filter(Boolean)
    },
    byId: (state) => {
      return (id: string): EventDto | null => state.items[id] ?? null
    },
    isLoading: (state) => (id?: string) => {
      if (id) return !!state.loadingById[id]
      return state.loadingList
    },
    isDeleting: (state) => (id: string) => !!state.deletingById[id],
  },

  actions: {
    async fetchAll(): Promise<void> {
      this.loadingList = true
      this.error = null
      try {
        const list = await EventsApi.list()
        const nextItems: Record<string, EventDto> = {}
        const nextIds: string[] = []
        for (const e of list) {
          nextItems[e.id] = e
          nextIds.push(e.id)
        }
        this.items = nextItems
        this.ids = nextIds
      } catch (e: any) {
        this.error = e?.message || 'Failed to load events'
      } finally {
        this.loadingList = false
      }
    },

    async fetchOne(id: string): Promise<EventDto | null> {
      this.loadingById[id] = true
      this.error = null
      try {
        const e = await EventsApi.get(id)
        this.items[e.id] = e
        if (!this.ids.includes(e.id)) this.ids.push(e.id)
        return e
      } catch (e: any) {
        this.error = e?.message || 'Failed to load event'
        return null
      } finally {
        this.loadingById[id] = false
      }
    },

    async create(payload: CreateEventPayload): Promise<EventDto | null> {
      this.creating = true
      this.error = null
      try {
        const created = await EventsApi.create(payload)
        this.items[created.id] = created
        if (!this.ids.includes(created.id)) this.ids.unshift(created.id)
        return created
      } catch (e: any) {
        this.error = e?.message || 'Failed to create event'
        return null
      } finally {
        this.creating = false
      }
    },

    async remove(id: string): Promise<boolean> {
      this.deletingById[id] = true
      this.error = null
      try {
        await EventsApi.delete(id)
        // Remove from state
        const { [id]: _, ...rest } = this.items
        this.items = rest
        this.ids = this.ids.filter((x) => x !== id)
        return true
      } catch (e: any) {
        this.error = e?.message || 'Failed to delete event'
        return false
      } finally {
        this.deletingById[id] = false
      }
    },
  },
})

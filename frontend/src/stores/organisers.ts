import { defineStore } from 'pinia'
import { OrganisersApi, type OrganiserDto, type CreateOrganiserPayload, type UpdateOrganiserPayload } from '@/services/organisersApi'

export const useOrganisersStore = defineStore('organisers', {
  state: () => ({
    items: {} as Record<string, OrganiserDto>,
    ids: [] as string[],
    loadingList: false as boolean,
    loadingById: {} as Record<string, boolean>,
    creating: false as boolean,
    updatingById: {} as Record<string, boolean>,
    deletingById: {} as Record<string, boolean>,
    error: null as string | null,
  }),

  getters: {
    all(state): OrganiserDto[] {
      return state.ids.map((id) => state.items[id]).filter(Boolean)
    },
    byId: (state) => (id: string): OrganiserDto | null => state.items[id] ?? null,
    isLoading: (state) => (id?: string) => {
      if (id) return !!state.loadingById[id]
      return state.loadingList
    },
    isUpdating: (state) => (id: string) => !!state.updatingById[id],
    isDeleting: (state) => (id: string) => !!state.deletingById[id],
  },

  actions: {
    async fetchAll(): Promise<void> {
      this.loadingList = true
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
        this.loadingList = false
      }
    },

    async fetchOne(id: string): Promise<OrganiserDto | null> {
      this.loadingById[id] = true
      this.error = null
      try {
        const o = await OrganisersApi.get(id)
        this.items[o.id] = o
        if (!this.ids.includes(o.id)) this.ids.push(o.id)
        return o
      } catch (e: any) {
        this.error = e?.message || 'Failed to load organiser'
        return null
      } finally {
        this.loadingById[id] = false
      }
    },

    async create(payload: CreateOrganiserPayload): Promise<OrganiserDto | null> {
      this.creating = true
      this.error = null
      try {
        const created = await OrganisersApi.create(payload)
        this.items[created.id] = created
        if (!this.ids.includes(created.id)) this.ids.unshift(created.id)
        return created
      } catch (e: any) {
        this.error = e?.message || 'Failed to create organiser'
        return null
      } finally {
        this.creating = false
      }
    },

    async update(id: string, payload: UpdateOrganiserPayload): Promise<OrganiserDto | null> {
      this.updatingById[id] = true
      this.error = null
      try {
        const updated = await OrganisersApi.update(id, payload)
        this.items[updated.id] = updated
        if (!this.ids.includes(updated.id)) this.ids.push(updated.id)
        return updated
      } catch (e: any) {
        this.error = e?.message || 'Failed to update organiser'
        return null
      } finally {
        this.updatingById[id] = false
      }
    },

    async remove(id: string): Promise<boolean> {
      this.deletingById[id] = true
      this.error = null
      try {
        await OrganisersApi.delete(id)
        const { [id]: _, ...rest } = this.items
        this.items = rest
        this.ids = this.ids.filter((x) => x !== id)
        return true
      } catch (e: any) {
        this.error = e?.message || 'Failed to delete organiser'
        return false
      } finally {
        this.deletingById[id] = false
      }
    },
  },
})

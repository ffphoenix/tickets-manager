import { config } from '@/config'

const BASE_URL = config.apiBaseUrl

export type OrganiserDto = {
  id: string
  name: string
  createdAt: string // ISO
}

export type CreateOrganiserPayload = {
  name: string
  organiserId?: string
}

export type UpdateOrganiserPayload = {
  name: string
}

async function handle<T>(res: Response): Promise<T> {
  if (!res.ok) {
    let message = `${res.status} ${res.statusText}`
    try {
      const data = await res.json()
      if (data && typeof (data as any).error === 'string') message = (data as any).error
    } catch {}
    throw new Error(message)
  }
  return (await res.json()) as T
}

export const OrganisersApi = {
  async list(): Promise<OrganiserDto[]> {
    const res = await fetch(`${BASE_URL}/organisers`)
    return handle<OrganiserDto[]>(res)
  },

  async get(id: string): Promise<OrganiserDto> {
    const res = await fetch(`${BASE_URL}/organisers/${encodeURIComponent(id)}`)
    return handle<OrganiserDto>(res)
  },

  async create(payload: CreateOrganiserPayload): Promise<OrganiserDto> {
    const res = await fetch(`${BASE_URL}/organisers`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
    })
    return handle<OrganiserDto>(res)
  },

  async update(id: string, payload: UpdateOrganiserPayload): Promise<OrganiserDto> {
    const res = await fetch(`${BASE_URL}/organisers/${encodeURIComponent(id)}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
    })
    return handle<OrganiserDto>(res)
  },

  async delete(id: string): Promise<void> {
    const res = await fetch(`${BASE_URL}/organisers/${encodeURIComponent(id)}`, {
      method: 'DELETE',
    })
    if (!res.ok && res.status !== 204) {
      let message = `${res.status} ${res.statusText}`
      try {
        const data = await res.json()
        if (data && typeof (data as any).error === 'string') message = (data as any).error
      } catch {}
      throw new Error(message)
    }
  },
}

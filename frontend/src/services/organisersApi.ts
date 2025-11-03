import { config } from '@/config'

const BASE_URL = config.apiBaseUrl

export type OrganiserDto = {
  id: string
  name: string
  createdAt: string // ISO
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
}

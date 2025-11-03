import { config } from '@/config'

const BASE_URL = config.apiBaseUrl;

export type EventDto = {
  id: string
  name: string
  organiserId: string
  startAt: string // ISO
  endAt: string // ISO
  createdAt: string // ISO
};

export type CreateEventPayload = {
  organiserId: string
  name: string
  startAt: string // ISO
  endAt: string // ISO
  eventId?: string
};

async function handle<T>(res: Response): Promise<T> {
  if (!res.ok) {
    let message = `${res.status} ${res.statusText}`;
    try {
      const data = await res.json();
      if (data && typeof data.error === 'string') message = data.error;
    } catch {}
    throw new Error(message);
  }
  return (await res.json()) as T;
}

export const EventsApi = {
  async list(): Promise<EventDto[]> {
    const res = await fetch(`${BASE_URL}/events`);
    return handle<EventDto[]>(res);
  },

  async get(id: string): Promise<EventDto> {
    const res = await fetch(`${BASE_URL}/events/${encodeURIComponent(id)}`);
    return handle<EventDto>(res);
  },

  async create(payload: CreateEventPayload): Promise<EventDto> {
    const res = await fetch(`${BASE_URL}/events`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
    });
    return handle<EventDto>(res);
  },

  async delete(id: string): Promise<void> {
    const res = await fetch(`${BASE_URL}/events/${encodeURIComponent(id)}`, {
      method: 'DELETE',
    });
    if (!res.ok && res.status !== 204) {
      let message = `${res.status} ${res.statusText}`;
      try {
        const data = await res.json();
        if (data && typeof data.error === 'string') message = data.error;
      } catch {}
      throw new Error(message);
    }
  },
};

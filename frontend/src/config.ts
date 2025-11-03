// Centralized runtime configuration for the frontend
// Values are sourced from Vite env (import.meta.env) and have sensible defaults for local dev.

const env = import.meta.env as unknown as {
  VITE_API_BASE_URL?: string
  BASE_URL?: string
  MODE?: string
  DEV?: boolean
  PROD?: boolean
};

function normalizeBaseUrl(url: string): string {
  // Remove trailing slash for consistency
  return url.replace(/\/$/, '');
}

export const config = {
  // Base URL for backend API
  apiBaseUrl: normalizeBaseUrl(
    env.VITE_API_BASE_URL && env.VITE_API_BASE_URL.trim() !== ''
      ? env.VITE_API_BASE_URL
      : 'http://localhost:8000'
  ),
  // App base (useful if deployed under sub-path)
  baseUrl: env.BASE_URL || '/',
  // Environment flags
  mode: env.MODE || 'development',
  dev: !!env.DEV,
  prod: !!env.PROD,
} as const;

export type AppConfig = typeof config;

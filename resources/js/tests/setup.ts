import { vi } from 'vitest';

const route = (name: string, params: Record<string, unknown> = {}) => {
  let url = name.replace(/\./g, '/');
  for (const key in params) {
    url = url.replace(`{${key}}`, String(params[key]));
  }
  return `http://localhost/${url}`;
};

vi.stubGlobal('route', route);

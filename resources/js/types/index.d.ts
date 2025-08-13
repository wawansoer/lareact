import { LucideIcon } from 'lucide-react';
import type { Config } from 'ziggy-js';

export interface Auth {
  user: User;
}

export interface BreadcrumbItem {
  title: string;
  href: string;
}

export interface NavGroup {
  title: string;
  items: NavItem[];
}

export interface NavItem {
  title: string;
  href: string;
  icon?: LucideIcon | null;
  isActive?: boolean;
}

export interface SharedData {
  name: string;
  quote: { message: string; author: string };
  auth: Auth;
  ziggy: Config & { location: string };
  sidebarOpen: boolean;
  [key: string]: unknown;
}

export interface User {
  id: number;
  name: string;
  email: string;
  avatar?: string;
  email_verified_at: string | null;
  created_at: string;
  updated_at: string;
  [key: string]: unknown; // This allows for additional properties...
}

export type PageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
  auth: {
    user: User;
  };
};

export type Paginated<T> = {
  data: T[];
  links: {
    first: string;
    last: string;
    prev: string | null;
    next: string | null;
  };
  meta: {
    current_page: number;
    from: number;
    last_page: number;
    links: {
      url: string | null;
      label: string;
      active: boolean;
    }[];
    path: string;
    per_page: number;
    to: number;
    total: number;
  };
};

export interface Tenant {
  id: string;
  name: string;
  created_at: string;
  updated_at: string;
}

export interface Role {
  id: number;
  name: string;
  created_at: string;
  updated_at: string;
}

export interface Permission {
  id: number;
  name: string;
  created_at: string;
  updated_at: string;
}

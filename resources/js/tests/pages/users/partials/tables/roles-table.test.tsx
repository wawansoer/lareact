import { RolesTable } from '@/pages/users/partials/tables/roles-table';
import { Paginated, Role } from '@/types';
import { router, usePage } from '@inertiajs/react';
import { fireEvent, render, screen } from '@testing-library/react';
import { beforeEach, describe, expect, it, Mock, vi } from 'vitest';

vi.mock('@inertiajs/react', () => ({
  router: {
    get: vi.fn((url, params, options) => {
      if (options && options.onFinish) {
        options.onFinish();
      }
    }),
    post: vi.fn(),
    delete: vi.fn(),
  },
  usePage: vi.fn(),
}));

const mockRoles: Paginated<Role> = {
  data: [
    { id: 1, name: 'role 1', created_at: '', updated_at: '', tenant: { id: '1', name: 'tenant 1', created_at: '', updated_at: '' } },
    { id: 2, name: 'role 2', created_at: '', updated_at: '', tenant: { id: '1', name: 'tenant 1', created_at: '', updated_at: '' } },
    { id: 3, name: 'role 3', created_at: '', updated_at: '', tenant: { id: '1', name: 'tenant 1', created_at: '', updated_at: '' } },
  ],
  total: 3,
  current_page: 1,
  per_page: 10,
  first_page_url: '',
  last_page_url: '',
  last_page: 1,
  next_page_url: null,
  prev_page_url: null,
  path: '',
  from: 1,
  to: 3,
  links: [],
};

describe('RolesTable', () => {
  beforeEach(() => {
    (usePage as Mock).mockReturnValue({
      url: '/users?tab=roles',
    });
  });

  it('should call router.post with selected ids when bulk deleting', async () => {
    render(<RolesTable roles={mockRoles} />);

    await screen.findByText('role 1');

    const checkboxes = screen.getAllByLabelText('Select row');
    fireEvent.click(checkboxes[0]);
    fireEvent.click(checkboxes[1]);

    const deleteButton = screen.getByText('Delete Selected (2)');
    fireEvent.click(deleteButton);

    const confirmButton = screen.getByText('Delete 2 Role(s)');
    fireEvent.click(confirmButton);

    expect(router.post).toHaveBeenCalledWith(route('roles.bulk-delete'), { ids: [1, 2] }, expect.any(Object));
  });
});

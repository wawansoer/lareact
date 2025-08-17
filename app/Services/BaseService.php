<?php

namespace App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

abstract class BaseService
{
    /**
     * The model instance.
     */
    protected Model $model;

    /**
     * The columns to be searched in a global search.
     *
     * @var array<int, string>
     */
    protected array $globalSearchColumns = ['name'];

    /**
     * The relationships to be eager loaded.
     *
     * @var array<int, string>
     */
    protected array $relationships = [];

    /**
     * Get the model instance.
     */
    abstract public function getModel(): Model;

    public function __construct()
    {
        $this->model = $this->getModel();
    }

    /**
     * Get a paginated list of resources.
     */
    public function getPaginatedData(Request $request): LengthAwarePaginator
    {
        $query = $this->model->query();

        if (! empty($this->relationships)) {
            $query->with($this->relationships);
        }

        $this->applySorting($query, $request);
        $this->applyColumnFilters($query, $request);
        $this->applyGlobalFilter($query, $request);

        return $query->paginate($request->input('per_page', 10))->withQueryString();
    }

    /**
     * Apply sorting to the query.
     */
    protected function applySorting(Builder $query, Request $request): void
    {
        if ($request->has('sort')) {
            [$sortColumn, $sortDirection] = explode(',', $request->input('sort'));
            $query->orderBy($sortColumn, $sortDirection);
        }
    }

    /**
     * Apply column-specific filters to the query.
     */
    protected function applyColumnFilters(Builder $query, Request $request): void
    {
        foreach ($request->except(['page', 'per_page', 'sort', 'global', 'tab']) as $column => $value) {
            if (! empty($value)) {
                $query->where($column, 'like', "%{$value}%");
            }
        }
    }

    /**
     * Apply a global filter to the query.
     */
    protected function applyGlobalFilter(Builder $query, Request $request): void
    {
        if ($request->has('global')) {
            $globalFilter = $request->input('global');
            $query->where(function (Builder $q) use ($globalFilter) {
                foreach ($this->globalSearchColumns as $column) {
                    $q->orWhere($column, 'like', "%{$globalFilter}%");
                }
            });
        }
    }
}

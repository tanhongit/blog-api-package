<?php

namespace CSlant\Blog\Api\Supports\Filters;

class FilterCategory
{
    /**
     * @param  array<mixed|string, mixed>  $request
     *
     * @return array<string, mixed>
     */
    public static function setFilters(array $request): array
    {
        return [
            'page' => $request['page'] ?? 1,
            'per_page' => $request['per_page'] ?? 10,
            'search' => $request['search'] ?? null,
            'exclude' => $request['exclude'] ?? null,
            'include' => $request['include'] ?? null,
            'order' => $request['order'] ?? 'desc',
            'order_by' => $request['order_by'] ?? 'name',
            'hide_empty' => $request['include'] ?? null,
            'parent' => $request['parent'] ?? null,
            'post' => $request['post'] ?? null,
            'slug' => $request['slug'] ?? null,
        ];
    }
}

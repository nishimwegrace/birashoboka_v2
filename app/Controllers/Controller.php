<?php

namespace App\Controllers;

class Controller
{
    protected static function paginationOptions(): array
    {
        $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
        $perPage = isset($_GET['per_page']) ? min(100, max(1, (int) $_GET['per_page'])) : 20;
        return [$page, $perPage];
    }

    protected static function paginate($query, string $message): void
    {
        [$page, $perPage] = self::paginationOptions();
        $total = $query->count();
        $items = $query->forPage($page, $perPage)->get();

        apiResponse(true, $message, [
            'items' => $items,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => (int) ceil($total / $perPage),
            ],
        ]);
    }

    protected static function applySearchAndSort($query, array $searchColumns, array $allowedSort = []): void
    {
        if (!empty($_GET['search'])) {
            $search = trim($_GET['search']);
            $query->where(function ($inner) use ($search, $searchColumns) {
                foreach ($searchColumns as $column) {
                    $inner->orWhere($column, 'like', '%' . $search . '%');
                }
            });
        }

        $sortBy = $_GET['sort_by'] ?? 'created_at';
        $sortOrder = strtolower($_GET['sort_order'] ?? 'desc');
        if (!in_array($sortOrder, ['asc', 'desc'], true)) {
            $sortOrder = 'desc';
        }

        if (in_array($sortBy, $allowedSort, true)) {
            $query->orderBy($sortBy, $sortOrder);
        }
    }
}

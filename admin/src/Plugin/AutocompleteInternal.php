<?php

namespace Simp\Pindrop\Modules\admin\src\Plugin;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use Simp\Pindrop\Database\DatabaseException;
use Simp\Pindrop\Database\DatabaseService;

class AutocompleteInternal
{

    public function __construct(protected DatabaseService $database)
    {
    }

    /**
     * @throws DatabaseException
     */
    public function matchUsers(string $query, int $limit = 10, $sort = 'DESC', $sort_by = null): array
    {
        $queryString = "SELECT * FROM users WHERE username LIKE :q1 OR email LIKE :q2 ";
        if ($sort_by) {
            $queryString .= " ORDER BY $sort_by $sort";
        }
        if ($sort) {
            $queryString .= " ORDER BY created_at $sort";
        }

        $queryString .= " LIMIT $limit";

        $results = $this->database->fetchAll($queryString, ...$d = ['q1' => "%$query%", 'q2' => "%$query%"]);
        return array_map(function ($result) {
            return [
                'value' => "{$result['email']} ({$result['id']})",
                'label' => "{$result['email']} ({$result['id']})"
            ];
        }, $results);
    }
}
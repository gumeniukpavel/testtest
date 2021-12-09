<?php

namespace Tests\Core;

use Illuminate\Testing\TestResponse;

class CustomTestResponse extends TestResponse
{
    public function dd()
    {
        dd([
            'status' => $this->getStatusCode(),
            'content' => $this->getOriginalContent()
        ]);
    }

    public function assertPaginationResponse(int $page = 1, int $totalItems = 1, int $pageSize = 10)
    {
        $this->assertJson([
            'totalItems' => $totalItems,
            'totalPages' => ceil($totalItems / $pageSize),
            'pageSize' => $pageSize,
            'items' => [],
        ]);
    }
}

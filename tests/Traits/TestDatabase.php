<?php

declare(strict_types=1);

namespace Tests\Traits;

use Illuminate\Foundation\Testing\TestResponse;

trait TestDatabase
{

    protected abstract function model();

    protected abstract function routeStore();

    protected abstract function routeUpdate();

    protected function assertStoreDatabaseHas(array $data,
                                              string $tableName,
                                              string $fieldName,
                                              string $fieldValue,
                                              string $fieldNameRespoinseId)
    {
        $response = $this->json('POST', $this->routeStore(), $data);
        $this->assertDatabaseHas($tableName, [
            $fieldName => $fieldValue,
            $fieldNameRespoinseId => $this->getIdFromResponseDatabase($response)
        ]);
    }

    protected function assertUpdateDatabaseHas(array $data,
                                              string $tableName,
                                              string $fieldName,
                                              string $fieldValue,
                                              string $fieldNameRespoinseId)
    {
        $response = $this->json('PUT', $this->routeUpdate(), $data);
        $this->assertDatabaseHas($tableName, [
            $fieldName => $fieldValue,
            $fieldNameRespoinseId => $this->getIdFromResponseDatabase($response)
        ]);
    }

    protected function assertUpdateDatabaseMissing(array $data,
                                               string $tableName,
                                               string $fieldName,
                                               string $fieldValue,
                                               string $fieldNameRespoinseId)
    {
        $response = $this->json('PUT', $this->routeUpdate(), $data);
        $this->assertDatabaseMissing($tableName, [
            $fieldName => $fieldValue,
            $fieldNameRespoinseId => $this->getIdFromResponseDatabase($response)
        ]);
    }

    private function getIdFromResponseDatabase(TestResponse $response)
    {
        return $response->json('id') ?? $response->json('data.id');
    }

}

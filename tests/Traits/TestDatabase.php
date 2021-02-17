<?php

declare(strict_types=1);

namespace Tests\Traits;

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
            $fieldNameRespoinseId => $response->json('id')
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
            $fieldNameRespoinseId => $response->json('id')
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
            $fieldNameRespoinseId => $response->json('id')
        ]);
    }

}

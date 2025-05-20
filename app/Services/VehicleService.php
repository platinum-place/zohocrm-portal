<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Throwable;

class VehicleService extends ZohoCRMService
{
    /**
     * @throws RequestException
     * @throws Throwable
     * @throws ConnectionException
     */
    public function brandList(): ?array
    {
        $fields = ['id', 'Name'];

        return $this->getRecords('Marcas', $fields);
    }

    /**
     * @throws RequestException
     * @throws Throwable
     * @throws ConnectionException
     */
    public function modelsList(string $brandId, ?int $page = 1, ?int $perPage = 200): ?array
    {
        $criteria = "Marca:equals:$brandId";

        return $this->searchRecords('Modelos', $criteria, $page, $perPage);
    }
}

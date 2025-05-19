<?php

namespace App\Services\Zoho;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;

class ZohoVehicle extends ZohoService
{
    /**
     * @throws RequestException
     * @throws \Throwable
     * @throws ConnectionException
     */
    public function brandList(): ?array
    {
        $fields = ['id', 'Name'];

        return $this->getRecords('Marcas', $fields);
    }

    /**
     * @throws RequestException
     * @throws \Throwable
     * @throws ConnectionException
     */
    public function modelsList(string $brandId, ?int $page = 1, ?int $perPage = 200): ?array
    {
        $criteria = "Marca:equals:$brandId";

        return $this->searchRecords('Modelos', $criteria, $page, $perPage);
    }
}

<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Throwable;

class ProductService extends ZohoCRMService
{
    /**
     * @throws RequestException
     * @throws Throwable
     * @throws ConnectionException
     */
    public function getList(?int $page = 1, ?int $perPage = 200): ?array
    {
        $criteria = "Corredor:equals:" . env('ZOHO_ACCOUNT_ID');

        return $this->searchRecords('Products', $criteria, $page, $perPage);
    }

    /**
     * @throws RequestException
     * @throws Throwable
     * @throws ConnectionException
     */
    public function get(string $id): ?array
    {
        $fields = ['id', 'Vendor_Name','Product_Name'];

        return $this->getRecords('Products', $fields,$id);
    }
}

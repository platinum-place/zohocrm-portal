<?php

namespace App\Services;

use App\Services\Zoho\ZohoCRMService;
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
        $criteria = 'Corredor:equals:3222373000092390001';

        return $this->searchRecords('Products', $criteria, $page, $perPage);
    }

    /**
     * @throws RequestException
     * @throws Throwable
     * @throws ConnectionException
     */
    public function get(string $id): ?array
    {
        $fields = ['id', 'Vendor_Name', 'Product_Name'];

        return $this->getRecords('Products', $fields, $id);
    }
}

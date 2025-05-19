<?php

namespace App\Services\Zoho;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Throwable;

class ZohoQuoteService extends ZohoService
{
    /**
     * @throws RequestException
     * @throws Throwable
     * @throws ConnectionException
     */
    public function get(string $id): ?array
    {
        $fields = ['id'];

        return $this->getRecords('Quotes', $fields, $id);
    }

    /**
     * @throws RequestException
     * @throws Throwable
     * @throws ConnectionException
     */
    public function update(string $id, array $data): ?array
    {
        return $this->updateRecords('Quotes', $id, $data);
    }
}

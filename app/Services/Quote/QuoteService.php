<?php

namespace App\Services\Quote;

use App\Services\Zoho\ZohoCRMService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Throwable;

class QuoteService extends ZohoCRMService
{
    /**
     * @throws RequestException
     * @throws Throwable
     * @throws ConnectionException
     */
    public function get(string $id): ?array
    {
        $fields = ['id', 'Quoted_Items'];

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

    /**
     * @throws RequestException
     * @throws Throwable
     * @throws ConnectionException
     */
    public function uploadAttachment(string $id, string $filePath): void
    {
        $this->uploadAnAttachment('Quotes', $id, $filePath);
    }

    /**
     * @throws RequestException
     * @throws Throwable
     * @throws ConnectionException
     */
    public function getAttachments(string $id): ?array
    {
        $fields = ['id', 'File_Name'];

        return $this->attachmentList('Quotes', $id, $fields)['data'];
    }

    /**
     * @throws RequestException
     * @throws Throwable
     * @throws ConnectionException
     */
    public function downloadAttachment(string $id, string $attachmentId): ?string
    {
        return $this->getAttachment('Quotes', $id, $attachmentId);
    }

    /**
     * @throws RequestException
     * @throws Throwable
     * @throws ConnectionException
     */
    public function searchQuote(string $search): ?array
    {
        $criteria = "((RNC_C_dula:equals:$search) and (Plan:equals:Auto))";

        return $this->searchRecords('Quotes', $criteria)['data'];
    }

    /**
     * @throws RequestException
     * @throws Throwable
     * @throws ConnectionException
     */
    public function getLifeProducts(string $product): ?array
    {
        $criteria = '((Corredor:equals:3222373000092390001'.") and (Product_Category:equals:$product))";

        return $this->searchRecords('Products', $criteria)['data'];
    }

    /**
     * @throws RequestException
     * @throws Throwable
     * @throws ConnectionException
     */
    public function getProductTaxes(string $productId): ?array
    {
        $criteria = "Plan:equals:$productId";

        return $this->searchRecords('Tasas', $criteria)['data'];
    }

    /**
     * @throws RequestException
     * @throws Throwable
     * @throws ConnectionException
     */
    public function getProductAccountMaintenances(string $productId): ?array
    {
        $criteria = "Servicio:equals:$productId";

        return $this->searchRecords('Mantenimientos_cuentas', $criteria)['data'];
    }

    /**
     * @throws RequestException
     * @throws Throwable
     * @throws ConnectionException
     */
    public function create(array $data): ?array
    {
        return $this->insertRecords('Quotes', $data);
    }
}

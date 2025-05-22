<?php

namespace App\Http\Controllers;

use App\Services\ZohoCRMService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Throwable;

class ProductController extends Controller
{
    public function __construct(protected ZohoCRMService $crm) {}

    /**
     * @throws RequestException
     * @throws Throwable
     * @throws ConnectionException
     */
    public function list()
    {
        try {
            $criteria = 'Corredor:equals:3222373000092390001';
            $list = $this->crm->searchRecords('Products', $criteria);
        } catch (\Exception $e) {
            return response()->json(['Error' => $e->getMessage()], 404);
        }

        return response()->json(
            collect($list['data'])->map(fn ($value) => [
                $value['id'] => $value['Product_Category'],
            ])
        );
    }

    /**
     * @throws RequestException
     * @throws Throwable
     * @throws ConnectionException
     */
    public function show(string $id)
    {
        try {
            $fields = ['id', 'Vendor_Name', 'Product_Name'];
            $product = $this->crm->getRecords('Products', $fields, $id)['data'][0];
        } catch (\Exception $e) {
            return response()->json(['Error' => $e->getMessage()], 404);
        }

        return response()->json([
            $product['id'] => [
                [
                    $product['Vendor_Name']['id'] => $product['Product_Name'],
                ],
            ],
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Services\ZohoCRMService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Throwable;

class ProductController extends Controller
{
    public function __construct(protected ZohoCRMService $crm)
    {
    }

    public function list()
    {
        try {
            $criteria = 'Corredor:equals:3222373000092390001';
            $list = $this->crm->searchRecords('Products', $criteria);
        } catch (\Exception $e) {
            return response()->json(['Error' => $e->getMessage()], 404);
        }

        $sortedProducts = collect($list['data'])
            ->map(fn($product) => [
                'IdProducto' => (int)$product['id'],
                'Producto' => $product['Product_Category'],
            ])
            ->sortBy(fn($product) => reset($product))
            ->values()
            ->toArray();

        return response()->json($sortedProducts);
    }

    public function show(string $id)
    {
        try {
            $fields = ['id', 'Vendor_Name', 'Product_Name'];
            $product = $this->crm->getRecords('Products', $fields, $id)['data'][0];
        } catch (\Exception $e) {
            return response()->json(['Error' => $e->getMessage()], 404);
        }

        return response()->json([
            'IdAseguradora' => (int)$product['Vendor_Name']['id'],
            'Aseguradora' => $product['Vendor_Name']['name'],
        ]);
    }
}

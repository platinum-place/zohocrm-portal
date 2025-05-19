<?php

namespace App\Http\Controllers;

use App\Services\Zoho\ZohoProduct;
use App\Services\Zoho\ZohoVehicle;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;

class ProductController extends Controller
{
    public function __construct(protected ZohoProduct $zohoProduct)
    {
    }

    /**
     * @throws RequestException
     * @throws \Throwable
     * @throws ConnectionException
     */
    public function list()
    {
        $list = $this->zohoProduct->getList();

        return response()->json(
            collect($list['data'])->map(fn($value) => [
                $value['id'] => $value['Product_Category']
            ])
        );
    }

    public function show(string $id)
    {
        $plan = $this->zohoProduct->get($id);

        $productData = $plan['data'][0];

        return response()->json([
            $productData['id'] => [
                [
                    $productData['Vendor_Name']['id'] => $productData['Product_Name']
                ]
            ]
        ]);
    }
}

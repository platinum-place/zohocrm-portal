<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Throwable;

class ProductController extends Controller
{
    public function __construct(protected ProductService $service) {}

    /**
     * @throws RequestException
     * @throws Throwable
     * @throws ConnectionException
     */
    public function list()
    {
        $list = $this->service->getList();

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
        $product = $this->service->get($id);

        return response()->json([
            $product['id'] => [
                [
                    $product['Vendor_Name']['id'] => $product['Product_Name'],
                ],
            ],
        ]);
    }
}

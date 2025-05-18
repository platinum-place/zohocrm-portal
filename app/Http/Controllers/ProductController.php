<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function list()
    {
        return response()->json([
            '01' => 'Automovil',
            '02' => 'Vida',
        ]);
    }

    public function show(string $id)
    {
        return response()->json([
            '01' => [
                '01' => 'MAPFRE',
                '02' => 'ANGLOAMERICANA',
            ],
        ]);
    }
}

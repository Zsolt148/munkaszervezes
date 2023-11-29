<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class CitiesController extends Controller
{
    public function getCities($zip)
    {
        $data = config('zips');

        foreach ($data as $item) {
            if ($item['zip'] == $zip) {
                return response()->json(['cities' => $item['cities']]);
            }
        }

        return response()->json(404);
    }
}

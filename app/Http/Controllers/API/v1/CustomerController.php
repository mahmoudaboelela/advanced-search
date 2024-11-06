<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Traits\DynamicFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    use DynamicFilter;
    public function index(Request $request)
    {
        $filters =$request->get('filters', []);
        return $this->getDynamicData(new Customer(),$filters);
    }
}

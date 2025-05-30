<?php

namespace App\Http\Controllers\Inputs;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Doctor;
use Illuminate\Http\Request;

class ComboboxController extends Controller
{
    public function comboBoxCustomer(Request $request)
    {
        $customers = Customer::query()
            ->select('id', 'firstname', 'lastname')
            ->where('firstname', 'like', '%' . $request->input('search') . '%')
            ->orWhere('lastname', 'like', '%' . $request->input('search') . '%')
            ->limit(10)
            ->get();
        return response()->json($customers);
    }
    public function comboBoxDoctor(Request $request)
    {
        $doctors = Doctor::query()
            ->select('id', 'name')
            ->where('name', 'like', '%' . $request->input('search') . '%')
            ->orWhere('code', 'like', '%' . $request->input('search') . '%')
            ->limit(10)
            ->get();
        return response()->json($doctors);
    }
}

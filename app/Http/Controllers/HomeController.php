<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Provincy;
use App\Models\City;
use App\Models\District;
use App\Models\Village;
use App\Models\Maktab;

class HomeController extends Controller
{
    public function index()
    {
        return view('welcome');
    }

    public function getBySector($id)
    {
        $maktabs = Maktab::where('sector_id', $id)->get();

        return response()->json($maktabs);
    }

    public function getCities($provinceCode)
    {
        $cities = City::where('province_code', $provinceCode)
            ->select('code', 'name')
            ->orderBy('name')
            ->get()
            ->map(function ($item) {
                $item->name = ucwords(strtolower($item->name));
                return $item;
            });

        return response()->json($cities);
    }

    public function getDistricts($cityCode)
    {
        $districts = District::where('city_code', $cityCode)
            ->select('code', 'name')
            ->orderBy('name')
            ->get()
            ->map(function ($item) {
                $item->name = ucwords(strtolower($item->name));
                return $item;
            });

        return response()->json($districts);
    }

    public function getVillages($districtCode)
    {
        $villages = Village::where('district_code', $districtCode)
            ->select('code', 'name')
            ->orderBy('name')
            ->get()
            ->map(function ($item) {
                $item->name = ucwords(strtolower($item->name));
                return $item;
            });

        return response()->json($villages);
    }

}

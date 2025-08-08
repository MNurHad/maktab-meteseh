<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Provincy;
use App\Models\City;
use App\Models\District;
use App\Models\Village;
use App\Models\Maktab;
use Illuminate\Support\Facades\Cache;

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
        $cacheKey = 'cities_' . $provinceCode;

        $cities = Cache::rememberForever($cacheKey, function () use ($provinceCode) {
            return City::where('province_code', $provinceCode)
                ->select('code', 'name')
                ->orderBy('name')
                ->get()
                ->map(function ($item) {
                    $item->name = ucwords(strtolower($item->name));
                    return $item;
                });
        });

        return response()->json($cities);
    }

    public function getDistricts($cityCode)
    {
        $cacheKey = 'districts_' . $cityCode;

        $districts = Cache::rememberForever($cacheKey, function () use ($cityCode) {
            return District::where('city_code', $cityCode)
                ->select('code', 'name')
                ->orderBy('name')
                ->get()
                ->map(function ($item) {
                    $item->name = ucwords(strtolower($item->name));
                    return $item;
                });
        });

        return response()->json($districts);
    }

    public function getVillages($districtCode)
    {
        $cacheKey = 'villages_' . $districtCode;

        $villages = Cache::rememberForever($cacheKey, function () use ($districtCode) {
            return Village::where('district_code', $districtCode)
                ->select('code', 'name')
                ->orderBy('name')
                ->get()
                ->map(function ($item) {
                    $item->name = ucwords(strtolower($item->name));
                    return $item;
                });
        });

        return response()->json($villages);
    }


}

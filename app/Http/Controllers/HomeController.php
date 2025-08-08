<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Provincy;
use App\Models\City;
use App\Models\District;
use App\Models\Village;
use App\Models\Maktab;
use Illuminate\Support\Facades\Cache;
use DB;

class HomeController extends Controller
{
    public function index()
    {
        return view('welcome');
    }

    public function searchMaktab(Request $request)
    {
        $keyword = strtolower($request->input('keyword', ''));
        $perPage = intval($request->input('per_page', 10));
        $page = max(intval($request->input('page', 1)), 1);

        $query = DB::table('assign_maktabs as am')
            ->join('maktabs as m', 'm.id', '=', 'am.maktab_id')
            ->join('coordinators as c', 'c.id', '=', 'm.coordinator_id')
            ->join('sectors as s', 's.id', '=', 'm.sector_id')
            ->select(
                'am.id',
                'am.leader',
                'am.phone_leader',
                's.sektor',
                'c.name as koordinator_sektor',
                'c.phone as wa_koordinator',
                'm.host_data',
                'am.group_data'
            )
            ->when($keyword, function ($query) use ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->whereRaw('LOWER(am.leader) LIKE ?', ["%$keyword%"])
                    ->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(group_data, '$.provincy'))) LIKE ?", ["%$keyword%"])
                    ->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(group_data, '$.city'))) LIKE ?", ["%$keyword%"])
                    ->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(group_data, '$.district'))) LIKE ?", ["%$keyword%"]);
                });
            });

        $total = $query->count();
        $results = $query->offset(($page - 1) * $perPage)
                        ->limit($perPage)
                        ->get();

        $formatted = $results->map(function ($item) {
            $group = json_decode($item->group_data ?? '{}', true);
            $host  = json_decode($item->host_data ?? '{}', true);

            return [
                'sektor'             => $item->sektor ?? '-',
                'koordinator_sektor' => $item->koordinator_sektor ?? '-',

                'ketua_rombongan'    => $item->leader ?? '-',
                'wa_ketua'           => $item->phone_leader ?? '62',

                'kota'               => $group['city'] ?? '-',
                'kecamatan'          => $group['district'] ?? '-',
                'provinsi'           => $group['provincy'] ?? '-',
                'jumlah_jamaah'      => $group['jamaah'] ?? 0,

                'tuan_rumah'         => $host['owner'] ?? '-',
                'alamat_maktab'      => $host['address'] ?? '-',

                'wa_koordinator'     => $item->wa_koordinator ?? '62',
                'wa_tuan_rumah'      => $host['phone'] ?? '62',
            ];
        });

        return response()->json([
            'statusCode' => 200,
            'message' => $formatted->isEmpty()
                ? 'Data tidak ditemukan'
                : 'Data ditemukan',
            'data' => $formatted,
            'pagination' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => ceil($total / $perPage),
            ]
        ], 200);
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

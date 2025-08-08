<?php

namespace App\Http\Controllers\cms;

use App\Http\Controllers\CmsController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Str;
use App\Services\AuthorizesRequests;
use App\Models\Group;
use App\Models\Maktab;
use App\Models\Provincy;
use App\Models\City;
use App\Models\District;
use App\Models\Village;
use DB;

class GroupController extends CmsController
{
    use AuthorizesRequests;
     /**
    * @var string
    */
    protected $resourceName = 'groups';

    /**
     * @var Group
     */
    protected $groups;

    /**
     * AdminController constructor.
     * @param Group $groups
     */
    public function __construct(Group $groups)
    {
        $this->groups = $groups;
    }

    protected function useDatatables()
    {
        return $this->groups->getDatatables();
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->setTitle('Assign maktab')
            ->setBreadcrumbs([
                ['name' => 'Home', 'route' => 'admin.home', 'current' => true],
                ['name' => 'Assign Maktab', 'route' => null, 'current' => true]
            ]);

        return view("cms.{$this->resourceName}.index", $this->getData());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $vehicleTypes = [
            'bus_besar' => 'Bus Besar',
            'bus_medium' => 'Bus Medium',
            'elf_hiace_van' => 'Elf/ Hiace/ Van',
            'mandiri' => 'Mandiri ( perorangan )',
            'mobil_pribadi' => 'Mobil Pribadi',
            'rombongan_kereta_api' => 'Rombongan Kereta Api',
        ];

        $this->setTitle('Assign maktab')
            ->setBreadcrumbs([
                ['name' => 'Home', 'route' => 'admin.home', 'current' => true],
                ['name' => 'Assign Maktab', 'route' => null, 'current' => true]
            ])
            ->setData('vehicleTypes', $vehicleTypes);

        return view("cms.{$this->resourceName}.create", $this->getData());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'maktab_id'      => 'required|exists:maktabs,id',
            'leader'         => 'required|string|max:255',
            'phone'          => 'required|string|max:20',
            'planing_at'     => 'nullable|date',
            'actual_at'      => 'nullable|date',
            'province'       => 'required|string',
            'city'           => 'required|string',
            'district'       => 'required|string',
            'village'        => 'required|string',
            'vehicle'        => 'required|string',
            'jamaah'         => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $maktab = Maktab::findOrFail($request->maktab_id);

            $group = new Group();
            $group->maktab_id = $request->maktab_id;
            $group->leader = $request->leader;
            $group->phone_leader = $request->phone;
            $group->planing_at  = $request->planing_at;
            $group->actual_at   = $request->actual_at;

            $groupData = [
                'province'    => masterName(Provincy::class, $request->province),
                'city'        => masterName(City::class, $request->city),
                'district'    => masterName(District::class, $request->district),
                'village'     => masterName(Village::class, $request->village),
                'alamat'      => $request->alamat,
                'jamaah'      => $request->jamaah,
            ];

            $group->group_data = $groupData;

            $vehicle_data = [
                'vehicle' => $request->vehicle,
            ];

            $group->vehicle_data = $vehicle_data;

            $group->save();

            $hostData = $maktab->host_data;
            $currentCapacity = (int)($hostData['capacity'] ?? 0);
            $newCapacity = $currentCapacity - $request->jamaah;

            if ($newCapacity < 0) {
                return response()->json(['message' => 'Jumlah jamaah melebihi kapasitas maktab.'], 422);
            }

            $hostData['capacity'] = $newCapacity;
            $maktab->host_data = $hostData;
            $maktab->is_available = $newCapacity > 0;
            $maktab->save();

            DB::commit();

            return response()->json(['message' => 'Group berhasil disimpan dan kapasitas maktab diperbarui'], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan data',
                'error' => [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTrace()
                ]
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $group = DB::table('assign_maktabs as g')
            ->join('maktabs as m', 'g.maktab_id', '=', 'm.id')
            ->join('sectors as s', 's.id', '=', 'm.sector_id')
            ->join('coordinators as c', 'c.id', '=', 'm.coordinator_id')
            ->select([
                'g.*',
                'c.name as cp_name',
                'c.phone as cp_phone',
                's.sektor',
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(m.host_data, '$.address')) as address"),
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(m.host_data, '$.owner')) as host_name"),
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(m.host_data, '$.phone')) as host_phone"),
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(m.host_data, '$.capacity')) as capacity"),
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(g.group_data, '$.provincy')) as provincy"),
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(g.group_data, '$.city')) as city"),
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(g.group_data, '$.district')) as district"),
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(g.group_data, '$.village')) as village"),
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(g.group_data, '$.vehicle')) as vehicle"),
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(g.group_data, '$.alamat')) as alamat"),
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(g.group_data, '$.jamaah')) as jamaah"),
            ])
            ->where('g.id', $id)
            ->firstOrFail();

        $vehicleTypes = [
            'bus_besar' => 'Bus Besar',
            'bus_medium' => 'Bus Medium',
            'elf_hiace_van' => 'Elf/ Hiace/ Van',
            'mandiri' => 'Mandiri ( perorangan )',
            'mobil_pribadi' => 'Mobil Pribadi',
            'rombongan_kereta_api' => 'Rombongan Kereta Api',
        ];

        $this->setTitle('Edit Assign Maktab')
            ->setData('group', $group)
            ->setData('vehicleTypes', $vehicleTypes)
            ->setBreadcrumbs([
                ['name' => 'Home', 'route' => 'admin.home'],
                ['name' => 'Assign Maktab', 'route' => route('admin.groups.index')],
                ['name' => 'Edit', 'route' => null],
            ])
            ->setData('vehicleTypes', $vehicleTypes)
            ->setData('group', $group);

        return view("cms.{$this->resourceName}.edit", $this->getData());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $group = Group::findOrFail($id);
        $group->delete();

        return response()->json(null, 200);
    }
}

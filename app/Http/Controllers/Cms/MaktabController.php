<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\CmsController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Str;
use App\Services\AuthorizesRequests;
use App\Models\Maktab;

class MaktabController extends CmsController
{
    use AuthorizesRequests;
     /**
    * @var string
    */
    protected $resourceName = 'maktabs';

    /**
     * @var Maktab
     */
    protected $maktabs;

    /**
     * AdminController constructor.
     * @param Maktab $maktabs
     */
    public function __construct(Maktab $maktabs)
    {
        $this->maktabs = $maktabs;
    }

    protected function useDatatables()
    {
        return $this->maktabs->getDatatables();
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->setTitle('Maktab')
            ->setBreadcrumbs([
                ['name' => 'Home', 'route' => 'admin.home', 'current' => true],
                ['name' => 'Maktab', 'route' => null, 'current' => true]
            ]);

        return view("cms.{$this->resourceName}.index", $this->getData());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
         $hostTypes = [
            'Simple', 'Large', 'Spacious', 'Moderate', 'Masjid', 'Musholla', 'Luxurious'
        ];

         $this->setTitle('maktab')
            ->setBreadcrumbs([
                ['name' => 'Home', 'route' => 'admin.home', 'current' => true],
                ['name' => 'Maktab', 'route' => null, 'current' => true]
            ])
            ->setData('hostTypes', $hostTypes);

        return view("cms.{$this->resourceName}.create", $this->getData());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'sector_id'      => 'required|exists:sectors,id',
            'coordinator_id' => 'required|exists:coordinators,id',
            'host_name'      => 'required|string|max:255',
            'host_phone'     => 'required|string|max:20',
            'capacity'       => 'required|integer|min:1',
            'host_type'      => 'required|string',
            'address'        => 'required|string',
        ]);

        $maktab = new Maktab();
        $maktab->sector_id = $request->sector_id;
        $maktab->coordinator_id = $request->coordinator_id;

        $maktab->type = $request->host_type;

        $maktab->host_data = [
            'owner'     => $request->host_name,
            'phone'    => $request->host_phone,
            'address'  => $request->address,
            'capacity' => $request->capacity,
        ];

        $maktab->save();

        return response()->json(['message' => 'Created Maktab Successfully'], 200);
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
        $maktab = Maktab::findOrFail($id);
        $hostData = $maktab->host_data ?? [];

        $hostTypes = [
            'Simple', 'Large', 'Spacious', 'Moderate', 'Masjid', 'Musholla', 'Luxurious'
        ];

        $this->setTitle('maktab')
            ->setBreadcrumbs([
                ['name' => 'Home', 'route' => 'admin.home', 'current' => true],
                ['name' => 'Maktab', 'route' => null, 'current' => true]
            ])
            ->setData('hostTypes', $hostTypes)
            ->setData('maktab', $maktab)
            ->setData('host', $hostData);

        return view("cms.{$this->resourceName}.edit", $this->getData());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'sector_id'      => 'required|exists:sectors,id',
            'coordinator_id' => 'required|exists:coordinators,id',
            'host_name'      => 'required|string|max:255',
            'host_phone'     => 'required|string|max:20',
            'capacity'       => 'required|integer|min:1',
            'host_type'      => 'required|string',
            'address'        => 'required|string',
        ]);

        $maktab = Maktab::findOrFail($id);
        $maktab->sector_id = $request->sector_id;
        $maktab->coordinator_id = $request->coordinator_id;

        $maktab->type = $request->host_type;

        $maktab->host_data = [
            'owner'     => $request->host_name,
            'phone'    => $request->host_phone,
            'address'  => $request->address,
            'capacity' => $request->capacity,
        ];

        $maktab->save();

        return response()->json(['message' => 'Updated Maktab Successfully'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $maktab = Maktab::findOrFail($id);
        $maktab->delete();

        return response()->json(null, 200);
    }
}

<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\CmsController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Str;
use App\Services\AuthorizesRequests;
use App\Models\Sector;

class SectorController extends CmsController
{
    use AuthorizesRequests;
     /**
    * @var string
    */
    protected $resourceName = 'sectors';

    /**
     * @var Sector
     */
    protected $coordinators;

    /**
     * AdminController constructor.
     * @param Sector $sectors
     */
    public function __construct(Sector $sectors)
    {
        $this->sectors = $sectors;
    }

    protected function useDatatables()
    {
        return $this->sectors->getDatatables();
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->setTitle('Sektor')
            ->setBreadcrumbs([
                ['name' => 'Home', 'route' => 'admin.home', 'current' => true],
                ['name' => 'Sekto', 'route' => null, 'current' => true]
            ]);

        return view("cms.{$this->resourceName}.index", $this->getData());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->setTitle('Sektor')
            ->setBreadcrumbs([
                ['name' => 'Home', 'route' => 'admin.home', 'current' => true],
                ['name' => 'Sektor', 'route' => 'admin.sectors.index', 'current' => true],
                ['name' => 'Create', 'route' => null, 'current' => true]
            ]);

        return view("cms.{$this->resourceName}.create", $this->getData());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sektor' => 'required|string|max:255',
        ]);

        $sektor = new Sector();
        $sektor->sektor = $validated['sektor'];
        $sektor->save();

        return response()->json(['message' => 'Updated Sektor Successfully'], 200);
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
        $sektor = Sector::findOrFail($id);

        $this->setTitle('Sektor')
        ->setBreadcrumbs([
            ['name' => 'Home', 'route' => 'admin.home', 'current' => true],
            ['name' => 'Sektor', 'route' => 'admin.sectors.index', 'current' => true],
            ['name' => $sektor->sektor, 'route' => null, 'current' => true],
            ['name' => 'Edit', 'route' => null, 'current' => true]
        ])
        ->setData('sektor', $sektor);

        return view("cms.{$this->resourceName}.edit", $this->getData());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'sektor' => 'required|string|max:255',
        ]);

        $sektor = Sector::findOrFail($id);

        $sektor->update([
            'sektor' => $request->sektor
        ]);

        return response()->json(['message' => 'Updated Sektor Successfully'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $sektor = Sector::findOrFail($id);
        $sektor->delete();

        return response()->json(null, 200);
    }
}

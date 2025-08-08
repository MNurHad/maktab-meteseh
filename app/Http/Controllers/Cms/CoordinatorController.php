<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\CmsController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Str;
use App\Services\AuthorizesRequests;
use App\Models\Coordinator;

class CoordinatorController extends CmsController
{
    use AuthorizesRequests;
     /**
    * @var string
    */
    protected $resourceName = 'coordinators';

    /**
     * @var Category
     */
    protected $coordinators;

    /**
     * AdminController constructor.
     * @param Category $categories
     */
    public function __construct(Coordinator $coordinators)
    {
        $this->coordinators = $coordinators;
    }

    protected function useDatatables()
    {
        return $this->coordinators->getDatatables();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->setTitle('Coordinator')
            ->setBreadcrumbs([
                ['name' => 'Home', 'route' => 'admin.home', 'current' => true],
                ['name' => 'Coordinator', 'route' => null, 'current' => true]
            ]);

        return view("cms.{$this->resourceName}.index", $this->getData());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->setTitle('Coordinator')
            ->setBreadcrumbs([
                ['name' => 'Home', 'route' => 'admin.home', 'current' => true],
                ['name' => 'Coordinator', 'route' => 'admin.coordinators.index', 'current' => true],
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
            'sector_id' => 'required|exists:sectors,id',
            'name'      => 'required|string|max:255',
            'phone'     => 'required|numeric|starts_with:62',
        ]);

        try {
            $coordinator = Coordinator::create([
                'sector_id' => $validated['sector_id'],
                'name'      => $validated['name'],
                'phone'     => $validated['phone'],
            ]);

            return response()->json([
                'message' => 'Coordinator berhasil ditambahkan.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menyimpan data coordinator.',
                'error' => $e->getMessage()
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
        $coor = Coordinator::findOrFail($id);

        $this->setTitle('Coordinator')
        ->setBreadcrumbs([
            ['name' => 'Home', 'route' => 'admin.home', 'current' => true],
            ['name' => 'Coordinator', 'route' => 'admin.coordinators.index', 'current' => true],
            ['name' => 'Create', 'route' => null, 'current' => true]
        ])
        ->setData('coor', $coor);

        return view("cms.{$this->resourceName}.edit", $this->getData());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $coor = Coordinator::findOrFail($id);

        $validated = $request->validate([
            'sector_id' => 'required|exists:sectors,id',
            'name'      => 'required|string|max:255',
            'phone'     => 'required|numeric|starts_with:62',
        ]);

        try {
            $coor->update([
                'sector_id' => $validated['sector_id'],
                'name'      => $validated['name'],
                'phone'     => $validated['phone'],
            ]);

            return response()->json([
                'message' => 'Data coordinator berhasil diperbarui.',
                'data' => $coor
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal memperbarui data coordinator.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $coor = Coordinator::findOrFail($id);
        $coor->delete();

        return response()->json(null, 200);
    }
}

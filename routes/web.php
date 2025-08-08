<?php

use Illuminate\Support\Facades\Route;
use App\Models\Coordinator;
use App\Http\Controllers\CmsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Cms\AuthController;
use App\Http\Controllers\Cms\CoordinatorController as Coor;
use App\Http\Controllers\Cms\SectorController as Sector;
use App\Http\Controllers\Cms\MaktabController as Maktab;
use App\Http\Controllers\Cms\GroupController as Group;

Route::get('/', [HomeController::class, 'index'])->name('root');
Route::get('get-cities/{provinceCode}', [HomeController::class, 'getCities'])->name('getCities');
Route::get('get-districts/{cityCode}', [HomeController::class, 'getDistricts'])->name('getDistricts');
Route::get('get-villages/{districtCode}', [HomeController::class, 'getVillages'])->name('getVillages');
Route::get('maktab-by-sector/{id}', [HomeController::class, 'getBySector'])->name('getBySector');

Route::prefix('secret')
    ->name('admin.')
    ->group(function () {
        Route::get('login', [AuthController::class, 'showLoginForm'])->name('showLogin')->middleware('guest');
        Route::post('sync', [AuthController::class, 'login'])->name('loginProses');
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

        Route::middleware('auth')->group(function () {
            Route::get('home', [CmsController::class, 'home'])->name('home');
            Route::get('home/data', [CmsController::class, 'setDataHome'])->name('home.data');
            // end route custom

            // route resource
            collect([
                [
                    'uri' => 'coordinators',
                    'controller' => Coor::class,
                    'except' => ['show'],
                ],
                [
                    'uri' => 'sectors',
                    'controller' => Sector::class,
                    'except' => ['show'],
                ],
                [
                    'uri' => 'maktabs',
                    'controller' => Maktab::class,
                    'except' => ['show'],
                ],
                [
                    'uri' => 'groups',
                    'controller' => Group::class,
                    'except' => ['show'],
                ]
            ])->each(function ($route) {
                Route::resource($route['uri'], $route['controller'])->except($route['except']);
            });

            Route::get('coordinators/datatables', [Coor::class, 'datatables'])->name('coordinators.datatables');
            Route::get('sectors/datatables', [Sector::class, 'datatables'])->name('sectors.datatables');
            Route::get('maktabs/datatables', [Maktab::class, 'datatables'])->name('maktabs.datatables');
            Route::get('groups/datatables', [Group::class, 'datatables'])->name('groups.datatables');

            Route::get('select/coordinator/{id}', function ($id) {
                $coor = Coordinator::where('sector_id', $id)->first();

                if (!$coor) {
                    return response()->json(['message' => 'Coordinator not found'], 404);
                }

                return response()->json([
                    'id'    => $coor->id,
                    'name'  => $coor->name,
                    'phone' => $coor->phone,
                ]);
            })->name('maktab.coor-data');

            Route::get('select/sector-data/{id}', function ($id) {
                $coordinator = Coordinator::where('sector_id', $id)->first();

                if ($coordinator) {
                    return response()->json([
                        'id' => $coordinator->id,
                        'name' => $coordinator->name,
                        'phone' => $coordinator->phone,
                    ]);
                }

                return response()->json(null, 404);
            })->name('maktabs.coordinator_sektor');
        });
    });
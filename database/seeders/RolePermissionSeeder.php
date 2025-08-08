<?php

namespace Database\Seeders;

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $actionsByResource = [
            'dashboard'     => ['viewAny', 'view'],
            'coordinators'  => ['viewAny', 'view', 'update', 'delete'],
            'sectors'       => ['viewAny', 'view', 'update', 'delete'],
            'maktabs'       => ['viewAny', 'view', 'update', 'delete'],
            'countrys'      => ['viewAny', 'view', 'update'],
            'provincies'    => ['viewAny', 'view'],
            'reports'       => ['viewAny', 'view'],
            'logs'          => ['viewAny', 'view'],
        ];

        foreach ($actionsByResource as $resource => $actions) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(['guard_name' => 'admin', 'name' => "{$resource}.{$action}"]);
            }
        }

        $role = Role::firstOrCreate(['guard_name' => 'admin', 'name' => 'super-admin']);
        $role->givePermissionTo(Permission::all());
        User::find(1)->assignRole('admin');
    }
}

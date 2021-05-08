<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissions extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions

        Permission::create(['guard_name'=>'api', 'name' => 'view dashboard']);

        Permission::create(['guard_name'=>'api', 'name' => 'edit profiles']);
        Permission::create(['guard_name'=>'api', 'name' => 'view profiles']);

        Permission::create(['guard_name'=>'api', 'name' => 'edit ideas']);
        Permission::create(['guard_name'=>'api', 'name' => 'view ideas']);
        Permission::create(['guard_name'=>'api', 'name' => 'view unpublished ideas']);
        Permission::create(['guard_name'=>'api', 'name' => 'delete ideas']);
        Permission::create(['guard_name'=>'api', 'name' => 'restore ideas']);
        Permission::create(['guard_name'=>'api', 'name' => 'publish ideas']);

        Permission::create(['guard_name'=>'api', 'name' => 'approve ideas']);

        Permission::create(['guard_name'=>'api', 'name' => 'edit places']);
        Permission::create(['guard_name'=>'api', 'name' => 'view places']);
        Permission::create(['guard_name'=>'api', 'name' => 'delete places']);
        Permission::create(['guard_name'=>'api', 'name' => 'restore places']);
        Permission::create(['guard_name'=>'api', 'name' => 'publish places']);

        Permission::create(['guard_name'=>'api', 'name' => 'edit categories']);
        Permission::create(['guard_name'=>'api', 'name' => 'view categories']);
        Permission::create(['guard_name'=>'api', 'name' => 'delete categories']);
        Permission::create(['guard_name'=>'api', 'name' => 'restore categories']);
        Permission::create(['guard_name'=>'api', 'name' => 'publish categories']);



        // create roles and assign created permissions

        $role = Role::create(['guard_name'=>'api', 'name' => 'explorer']);
        $role->givePermissionTo([
            'view ideas',
            'view places',
            'view profiles',
            'edit profiles',
        ]);

//        // this can be done as separate statements
//        $role = Role::create(['name' => 'writer']);
//        $role->givePermissionTo([
//            'edit own articles',
//            'publish own articles',
//            'unpublish own articles',
//            'view unpublished own articles',
//            'delete own articles',
//
//            'edit own ideas',
//            'view unpublished ideas',
//            'publish ideas',
//            'unpublish ideas',
//            'delete own ideas',
//        ]);

        // this can be done as separate statements
        $role = Role::create(['guard_name'=>'api', 'name' => 'organizer']);
        $role->givePermissionTo([
            'edit ideas',
            'publish ideas',
            'delete ideas',
        ]);

        // or may be done by chaining
        $role = Role::create(['guard_name'=>'api', 'name' => 'moderator']);
        $role->givePermissionTo([
                'view unpublished ideas',
                'approve ideas',
                'view dashboard',
            ]);

        $role = Role::create(['guard_name'=>'api', 'name' => 'super-admin']);
        $role->givePermissionTo(Permission::all());
    }
}

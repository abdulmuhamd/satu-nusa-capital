<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RbacSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $modules = ['users','pages','services','posts','settings'];
        $actions = ['view','create','update','delete'];

        foreach ($modules as $m) {
            foreach ($actions as $a) {
                if ($m === 'settings' && in_array($a, ['create','delete'])) continue;
                Permission::firstOrCreate(['name' => "{$m}.{$a}"]);
            }
        }

        $admin  = Role::firstOrCreate(['name' => 'admin']);
        $editor = Role::firstOrCreate(['name' => 'editor']);
        $writer = Role::firstOrCreate(['name' => 'writer']);

        $admin->syncPermissions(Permission::all());

        $editor->syncPermissions([
            'pages.view','pages.create','pages.update','pages.delete',
            'services.view','services.create','services.update','services.delete',
            'posts.view','posts.create','posts.update','posts.delete',
            'settings.view','settings.update',
        ]);

        $writer->syncPermissions([
            'posts.view','posts.create','posts.update',
        ]);

        // Admin default (ubah email/password sesuai kebutuhan)
        $user = User::firstOrCreate(
            ['email' => 'admin@local.test'],
            ['name' => 'Administrator', 'password' => bcrypt('Admin12345!')]
        );

        $user->syncRoles(['admin']);
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        $permissions = [
            'manage_movies',
            'manage_tv_shows',
            'manage_users',
            'manage_forum',
            'manage_reviews',
            'manage_roles',
            'manage_articles',
            'manage_carousel',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // create roles and assign created permissions
        $movieMod = Role::findOrCreate('Movie Moderator');
        $movieMod->givePermissionTo(['manage_movies', 'manage_tv_shows', 'manage_reviews']);

        $forumMod = Role::findOrCreate('Forum Moderator');
        $forumMod->givePermissionTo(['manage_forum']);

        $contentMod = Role::findOrCreate('Content Moderator');
        $contentMod->givePermissionTo(['manage_articles', 'manage_carousel', 'manage_reviews']);

        $superAdmin = Role::findOrCreate('Super Admin');
        // gets all permissions via Gate::before rule; see AuthServiceProvider
    }
}

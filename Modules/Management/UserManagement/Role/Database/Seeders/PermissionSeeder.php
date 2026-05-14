<?php

namespace Modules\Management\UserManagement\Role\Database\Seeders;

use Modules\Management\UserManagement\Role\Database\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // Define all module routes and their permissions
        $permissions = [
            // User Management
            'User Management' => [
                ['name' => 'View Users', 'slug' => 'user-view', 'route' => '/admin#/user/all'],
                ['name' => 'Create User', 'slug' => 'user-create', 'route' => '/admin#/user/create'],
                ['name' => 'Edit User', 'slug' => 'user-edit', 'route' => '/admin#/user/edit'],
                ['name' => 'Delete User', 'slug' => 'user-delete', 'route' => '/api/v1/users/destroy'],
                ['name' => 'View User Details', 'slug' => 'user-details', 'route' => '/admin#/user/details'],
                ['name' => 'Import Users', 'slug' => 'user-import', 'route' => '/api/v1/users/import'],
            ],
            // Role Management
            'Role Management' => [
                ['name' => 'View Roles', 'slug' => 'role-view', 'route' => '/admin#/role/all'],
                ['name' => 'Create Role', 'slug' => 'role-create', 'route' => '/admin#/role/create'],
                ['name' => 'Edit Role', 'slug' => 'role-edit', 'route' => '/admin#/role/edit'],
                ['name' => 'Delete Role', 'slug' => 'role-delete', 'route' => '/api/v1/roles/destroy'],
                ['name' => 'View Role Details', 'slug' => 'role-details', 'route' => '/admin#/role/details'],
                ['name' => 'Manage Permissions', 'slug' => 'role-permission-manage', 'route' => '/api/v1/permissions'],
                ['name' => 'Import Roles', 'slug' => 'role-import', 'route' => '/api/v1/roles/import'],
            ],
            // Blog Management
            'Blog Management' => [
                ['name' => 'View Blogs', 'slug' => 'blog-view', 'route' => '/admin#/blog/all'],
                ['name' => 'Create Blog', 'slug' => 'blog-create', 'route' => '/admin#/blog/create'],
                ['name' => 'Edit Blog', 'slug' => 'blog-edit', 'route' => '/admin#/blog/edit'],
                ['name' => 'Delete Blog', 'slug' => 'blog-delete', 'route' => '/api/v1/blogs/destroy'],
                ['name' => 'Publish Blog', 'slug' => 'blog-publish', 'route' => '/api/v1/blogs/publish'],
                ['name' => 'Import Blogs', 'slug' => 'blog-import', 'route' => '/api/v1/blogs/import'],
            ],
            // Blog Category Management
            'Blog Category Management' => [
                ['name' => 'View Blog Categories', 'slug' => 'blog-category-view', 'route' => '/admin#/blog-category/all'],
                ['name' => 'Create Blog Category', 'slug' => 'blog-category-create', 'route' => '/admin#/blog-category/create'],
                ['name' => 'Edit Blog Category', 'slug' => 'blog-category-edit', 'route' => '/admin#/blog-category/edit'],
                ['name' => 'Delete Blog Category', 'slug' => 'blog-category-delete', 'route' => '/api/v1/blog-categories/destroy'],
                ['name' => 'Import Blog Categories', 'slug' => 'blog-category-import', 'route' => '/api/v1/blog-categories/import'],
            ],
            // Project Management
            'Project Management' => [
                ['name' => 'View Projects', 'slug' => 'project-view', 'route' => '/admin#/project/all'],
                ['name' => 'Create Project', 'slug' => 'project-create', 'route' => '/admin#/project/create'],
                ['name' => 'Edit Project', 'slug' => 'project-edit', 'route' => '/admin#/project/edit'],
                ['name' => 'Delete Project', 'slug' => 'project-delete', 'route' => '/api/v1/projects/destroy'],
                ['name' => 'Import Projects', 'slug' => 'project-import', 'route' => '/api/v1/projects/import'],
            ],
            // Product Management
            'Product Management' => [
                ['name' => 'View Products', 'slug' => 'product-view', 'route' => '/admin#/product/all'],
                ['name' => 'Create Product', 'slug' => 'product-create', 'route' => '/admin#/product/create'],
                ['name' => 'Edit Product', 'slug' => 'product-edit', 'route' => '/admin#/product/edit'],
                ['name' => 'Delete Product', 'slug' => 'product-delete', 'route' => '/api/v1/products/destroy'],
                ['name' => 'Import Products', 'slug' => 'product-import', 'route' => '/api/v1/products/import'],
            ],
            // Contact Management
            'Contact Management' => [
                ['name' => 'View Contacts', 'slug' => 'contact-view', 'route' => '/admin#/contact/all'],
                ['name' => 'View Contact Details', 'slug' => 'contact-details', 'route' => '/admin#/contact/details'],
                ['name' => 'Delete Contact', 'slug' => 'contact-delete', 'route' => '/api/v1/contacts/destroy'],
                ['name' => 'Import Contacts', 'slug' => 'contact-import', 'route' => '/api/v1/contacts/import'],
            ],
            // Credential Management
            'Credential Management' => [
                ['name' => 'View Credentials', 'slug' => 'credential-view', 'route' => '/admin#/credential/all'],
                ['name' => 'Create Credential', 'slug' => 'credential-create', 'route' => '/admin#/credential/create'],
                ['name' => 'Edit Credential', 'slug' => 'credential-edit', 'route' => '/admin#/credential/edit'],
                ['name' => 'Delete Credential', 'slug' => 'credential-delete', 'route' => '/api/v1/credentials/destroy'],
                ['name' => 'Import Credentials', 'slug' => 'credential-import', 'route' => '/api/v1/credentials/import'],
            ],
            // Personal Note Management
            'Personal Note Management' => [
                ['name' => 'View Notes', 'slug' => 'note-view', 'route' => '/admin#/note/all'],
                ['name' => 'Create Note', 'slug' => 'note-create', 'route' => '/admin#/note/create'],
                ['name' => 'Edit Note', 'slug' => 'note-edit', 'route' => '/admin#/note/edit'],
                ['name' => 'Delete Note', 'slug' => 'note-delete', 'route' => '/api/v1/notes/destroy'],
                ['name' => 'Import Notes', 'slug' => 'note-import', 'route' => '/api/v1/notes/import'],
            ],
            // Todo Management
            'Todo Management' => [
                ['name' => 'View Todos', 'slug' => 'todo-view', 'route' => '/admin#/todo/all'],
                ['name' => 'Create Todo', 'slug' => 'todo-create', 'route' => '/admin#/todo/create'],
                ['name' => 'Edit Todo', 'slug' => 'todo-edit', 'route' => '/admin#/todo/edit'],
                ['name' => 'Delete Todo', 'slug' => 'todo-delete', 'route' => '/api/v1/todos/destroy'],
                ['name' => 'Import Todos', 'slug' => 'todo-import', 'route' => '/api/v1/todos/import'],
            ],
            // Settings
            'Settings' => [
                ['name' => 'View Settings', 'slug' => 'settings-view', 'route' => '/admin#/settings'],
                ['name' => 'Edit Settings', 'slug' => 'settings-edit', 'route' => '/api/v1/settings/update'],
            ],
            // Dashboard
            'Dashboard' => [
                ['name' => 'View Dashboard', 'slug' => 'dashboard-view', 'route' => '/admin#/dashboard'],
            ],

        ];

        // Insert permissions
        foreach ($permissions as $category => $items) {
            foreach ($items as $permission) {
                Permission::firstOrCreate(
                    ['slug' => $permission['slug']],
                    [
                        'name' => $permission['name'],
                        'route' => $permission['route'],
                        'category' => $category,
                        'status' => 'active'
                    ]
                );
            }
        }
    }
}

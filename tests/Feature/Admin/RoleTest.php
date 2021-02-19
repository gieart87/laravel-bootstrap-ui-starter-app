<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Permission;
use App\Models\Role;

class RoleTest extends TestCase
{
    use WithFaker, RefreshDatabase;
 
    protected $admin;
    protected $user;

    /**
     * Setup every thing before running the tests
     *
     * @return void
     */
    public function setUp() : void
    {
        parent::setUp();
        $this->setUpFaker();

        $this->setupPermissions();

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->user = User::factory()->create();
    }
    

    /**
     * Setup the permissions
     *
     * @return void
     */
    private function setupPermissions()
    {
        $permissions = [
            'view_roles',
            'add_roles',
            'edit_roles',
            'delete_roles',
        ];
        
        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        Role::findOrCreate('admin')
            ->givePermissionTo($permissions);

        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->registerPermissions();
    }

    /**
     * Setup the roles
     *
     * @return void
     */
    private function setupRoles()
    {
        Role::factory()->create(
            [
                'name' => 'Role1',
            ]
        );

        Role::factory()->create(
            [
                'name' => 'Role2',
            ]
        );
    }

    // ==================
    // Positive Cases
    // ==================

    /**
     * Test admin can view the roles
     *
     * @return void
     */
    public function testAdminCanViewTheRoleIndex()
    {
        $this->setupRoles();

        $response = $this
            ->actingAs($this->admin)
            ->get('/admin/roles');
        
        $response->assertStatus(200);
        $response->assertSee('Role1');
        $response->assertSee('Role2');
    }

    /**
     * Test admin can add a role
     *
     * @return void
     */
    public function testAdminCanAddARole()
    {
        $permission = Permission::where('name', 'view_roles')->first();

        $params = [
            'name' => $this->faker->name(),
            'permissions' => [$permission->name],
        ];

        $response = $this
            ->actingAs($this->admin)
            ->post('/admin/roles', $params);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/admin/roles');
        $response->assertSessionHas('success', __('roles.success_create_message'));

        $role = Role::where('name', $params['name'])->first();
        $this->assertNotNull($role);
        $this->assertEquals($params['name'], $role->name);
    }

    /**
     * Test admin can update a role
     *
     * @return void
     */
    public function testAdminCanUpdateARole()
    {
        $existRole = Role::factory()->create();
        $permission = Permission::where('name', 'view_roles')->first();

        $params = [
            'name' => $this->faker->name(),
            'permissions' => [$permission->name],
        ];
        $response = $this
            ->actingAs($this->admin)
            ->put('/admin/roles/'. $existRole->id, $params);

        $updatedRole = Role::find($existRole->id);
        $this->assertEquals($params['name'], $updatedRole->name);
        $this->assertEquals($existRole->id, $updatedRole->id);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/admin/roles');
        $response->assertSessionHas('success', __('roles.success_updated_message', ['name' => $existRole->name]));
    }

    /**
     * Test admin can delete a role
     *
     * @return void
     */
    public function testAdminCanDeleteARole()
    {
        $existRole = Role::factory()->create();

        $response = $this
            ->actingAs($this->admin)
            ->delete('/admin/roles/'. $existRole->id);

        $role = Role::where('id', $existRole->id)->first();
        $this->assertNull($role);

        $response->assertStatus(302);
        $response->assertRedirect('/admin/roles');
        $response->assertSessionHas('success', __('roles.success_deleted_message', ['name' => $existRole->name]));
    }
    
    // ==================
    // Negative Cases
    // ==================

    /**
     * Test admin can not add a role with blank name
     *
     * @return void
     */
    public function testAdminCanNotAddARoleWithBlankName()
    {
        $params = [];

        $response = $this
            ->actingAs($this->admin)
            ->post('/admin/roles', $params);

        $response->assertStatus(302);
        $response->assertSessionHasErrors();

        $errors = session('errors');
        $this->assertEquals('The name field is required.', $errors->get('name')[0]);
    }
    
    /**
     * Test admin can not add a role without any permission assigned (min: 1)
     *
     * @return void
     */
    public function testAdminCanNotAddARoleWithoutAnyPermissionAssigned()
    {
        $params = [
            'name' => $this->faker->name(),
            'permissions' => [],
        ];

        $response = $this
            ->actingAs($this->admin)
            ->post('/admin/roles', $params);

        $response->assertStatus(302);
        $response->assertSessionHasErrors();

        $errors = session('errors');
        $this->assertEquals('The permissions field is required.', $errors->get('permissions')[0]);
    }
    
    /**
     * Test admin can not add a duplicated role
     *
     * @return void
     */
    public function testAdminCanNotAddADuplicatedRole()
    {
        $existRole = Role::factory()->create();

        $params = [
            'name' => $existRole->name,
        ];

        $response = $this
            ->actingAs($this->admin)
            ->post('/admin/roles', $params);

        $response->assertStatus(302);
        $response->assertSessionHasErrors();

        $errors = session('errors');
        $this->assertEquals('The name has already been taken.', $errors->get('name')[0]);
    }
}

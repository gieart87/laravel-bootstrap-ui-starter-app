<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;

class UserTest extends TestCase
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
            'view_users',
            'add_users',
            'edit_users',
            'delete_users',
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
    private function setupUsers()
    {
        User::factory()->create(
            [
                'name' => 'User1',
            ]
        );

        User::factory()->create(
            [
                'name' => 'User2',
            ]
        );
    }

    // ==================
    // Positive Cases
    // ==================

    /**
     * Test admin can view the users
     *
     * @return void
     */
    public function testAdminCanViewTheUserIndex()
    {
        $this->setupUsers();

        $response = $this
            ->actingAs($this->admin)
            ->get('/admin/users');
        
        $response->assertStatus(200);
        $response->assertSee('User1');
        $response->assertSee('User1');
    }


    /**
     * Test admin can add a user
     *
     * @return void
     */
    public function testAdminCanAddAUser()
    {
        $params = [
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
            'permissions' => [],
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this
            ->actingAs($this->admin)
            ->post('/admin/users', $params);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/admin/users');
        $response->assertSessionHas('success', __('users.success_create_message'));

        $user = User::where('name', $params['name'])->first();
        $this->assertNotNull($user);
        $this->assertEquals($params['name'], $user->name);
    }

    /**
     * Test admin can update a user
     *
     * @return void
     */
    public function testAdminCanUpdateAUser()
    {
        $existUser = User::factory()->create();

        $params = [
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
            'permissions' => [],
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this
            ->actingAs($this->admin)
            ->put('/admin/users/'. $existUser->id, $params);

        $updateUser = User::find($existUser->id);
        $this->assertEquals($params['name'], $updateUser->name);
        $this->assertEquals($existUser->id, $updateUser->id);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/admin/users');
        $response->assertSessionHas('success', __('users.success_updated_message', ['name' => $existUser->name]));
    }

    /**
     * Test admin can delete a user
     *
     * @return void
     */
    public function testAdminCanDeleteARole()
    {
        $existUser = User::factory()->create();

        $response = $this
            ->actingAs($this->admin)
            ->delete('/admin/users/'. $existUser->id);

        $user = User::where('id', $existUser->id)->first();
        $this->assertNull($user);

        $response->assertStatus(302);
        $response->assertRedirect('/admin/users');
        $response->assertSessionHas('success', __('users.success_deleted_message', ['name' => $existUser->name]));
    }
    
    // ==================
    // Negative Cases
    // ==================

    /**
     * Test admin can not add a user with blank name
     *
     * @return void
     */
    public function testAdminCanNotAddAUserWithBlankName()
    {
        $params = [];

        $response = $this
            ->actingAs($this->admin)
            ->post('/admin/users', $params);

        $response->assertStatus(302);
        $response->assertSessionHasErrors();

        $errors = session('errors');

        $this->assertEquals('The name field is required.', $errors->get('name')[0]);
        $this->assertEquals('The email field is required.', $errors->get('email')[0]);
    }
    
    /**
     * Test admin can not add a duplicated user email
     *
     * @return void
     */
    public function testAdminCanNotAddADuplicatedUserEmail()
    {
        $existUser = User::factory()->create();

        $params = [
            'email' => $existUser->email,
        ];

        $response = $this
            ->actingAs($this->admin)
            ->post('/admin/users', $params);

        $response->assertStatus(302);
        $response->assertSessionHasErrors();

        $errors = session('errors');
        $this->assertEquals('The email has already been taken.', $errors->get('email')[0]);
    }
}

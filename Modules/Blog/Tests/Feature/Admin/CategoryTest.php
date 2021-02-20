<?php

namespace Modules\Blog\Tests\Feature\Admin;

use Modules\Blog\Tests\BlogTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;


use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Modules\Blog\Entities\Category;

class CategoryTest extends BlogTestCase
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

        $this->setupBlogPermissions();

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->user = User::factory()->create();
    }

    /**
     * Init the categories
     *
     * @return void
     */
    private function initCategories()
    {
        Category::factory()->create(
            [
                'name' => 'category one',
            ]
        );

        Category::factory()->create(
            [
                'name' => 'category two',
            ]
        );
    }

    // ==================
    // Positive Cases
    // ==================

    /**
     * Test admin can view the categories
     *
     * @return void
     */
    public function testAdminCanViewTheCategoryIndex()
    {
        $this->initCategories();

        $response = $this
            ->actingAs($this->admin)
            ->get('/admin/blog/categories');
        
        $response->assertStatus(200);
        $response->assertSee('category one');
        $response->assertSee('category two');
    }

    /**
     * Test admin can add a category
     *
     * @return void
     */
    public function testAdminCanAddACategory()
    {
        $params = [
            'name' => $this->faker->name(),
        ];

        $response = $this
            ->actingAs($this->admin)
            ->post('/admin/blog/categories', $params);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();

        $category = Category::first();
        $this->assertNotNull($category);
        $this->assertEquals($params['name'], $category->name);

        $response->assertRedirect('/admin/blog/categories');
        $response->assertSessionHas('success', __('blog::categories.success_create_message'));
    }

    /**
     * Test admin can update a category
     *
     * @return void
     */
    public function testAdminCanUpdateACategory()
    {
        $existCategory = Category::factory()->create();

        $params = [
            'name' => $this->faker->name(),
        ];

        $response = $this
            ->actingAs($this->admin)
            ->put('/admin/blog/categories/'. $existCategory->id, $params);

        $updatedCategory = Category::find($existCategory->id);
        $this->assertEquals($params['name'], $updatedCategory->name);
        $this->assertEquals($existCategory->id, $updatedCategory->id);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/admin/blog/categories');
        $response->assertSessionHas('success', __('blog::categories.success_update_message'));
    }

    /**
     * Test admin can delete a category
     *
     * @return void
     */
    public function testAdminCanDeleteACategory()
    {
        $existCategory = Category::factory()->create();

        $response = $this
            ->actingAs($this->admin)
            ->delete('/admin/blog/categories/'. $existCategory->id);

        $category = Category::where('id', $existCategory->id)->first();
        $this->assertNull($category);

        $response->assertStatus(302);
        $response->assertRedirect('/admin/blog/categories');
        $response->assertSessionHas('success', __('blog::categories.success_delete_message'));
    }

    // ==================
    // Negative Cases
    // ==================
    public function testNonAdminUserCannotViewTheCategoryIndex()
    {
        $response = $this
            ->actingAs($this->user)
            ->get('/admin/blog/categories');

        $response->assertStatus(403);
    }

    /**
     * Test admin can not add a post with blank title
     *
     * @return void
     */
    public function testAdminCanNotAddACategoryWithBlankName()
    {
        $params = [];

        $response = $this
            ->actingAs($this->admin)
            ->post('/admin/blog/categories', $params);

        $response->assertStatus(302);
        $response->assertSessionHasErrors();

        $errors = session('errors');

        $this->assertEquals('The name field is required.', $errors->get('name')[0]);
    }

    /**
     * Test admin can not add a duplicated category name
     *
     * @return void
     */
    public function testAdminCanNotAddADuplicatedCategoryName()
    {
        $existCategory = Category::factory()->create();

        $params = [
            'name' => $existCategory->name,
        ];

        $response = $this
            ->actingAs($this->admin)
            ->post('/admin/blog/categories', $params);

        $response->assertStatus(302);
        $response->assertSessionHasErrors();

        $errors = session('errors');
        $this->assertEquals('The name has already been taken.', $errors->get('name')[0]);
    }
}

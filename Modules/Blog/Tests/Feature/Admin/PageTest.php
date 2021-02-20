<?php

namespace Modules\Blog\Tests\Feature\Admin;

use Modules\Blog\Tests\BlogTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;


use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Modules\Blog\Entities\Post;

class PageTest extends BlogTestCase
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
     * Init the posts
     *
     * @return void
     */
    private function initPosts()
    {
        Post::factory()->create(
            [
                'title' => 'Title one',
                'post_type' => 'page',
            ]
        );

        Post::factory()->create(
            [
                'title' => 'Title two',
                'post_type' => 'page',
            ]
        );
    }

    // ==================
    // Positive Cases
    // ==================

    /**
     * Test admin can view the pages
     *
     * @return void
     */
    public function testAdminCanViewThePageIndex()
    {
        $this->initPosts();

        $response = $this
            ->actingAs($this->admin)
            ->get('/admin/blog/pages');
        
        $response->assertStatus(200);
        $response->assertSee('Title one');
        $response->assertSee('Title two');
    }

    /**
     * Test admin can add a page
     *
     * @return void
     */
    public function testAdminCanAddAPage()
    {
        $params = [
            'title' => $this->faker->name(),
        ];

        $response = $this
            ->actingAs($this->admin)
            ->post('/admin/blog/pages', $params);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();

        $page = Post::where('post_type', Post::PAGE)->first();
        $this->assertNotNull($page);
        $this->assertEquals($params['title'], $page->title);

        $response->assertRedirect('/admin/blog/pages/'. $page->id .'/edit');
        $response->assertSessionHas('success', __('blog::pages.success_create_message'));
    }

    /**
     * Test admin can update a page
     *
     * @return void
     */
    public function testAdminCanUpdateAPage()
    {
        $existPage = Post::factory()->create(['post_type' => Post::PAGE]);

        $params = [
            'title' => $this->faker->name(),
        ];

        $response = $this
            ->actingAs($this->admin)
            ->put('/admin/blog/pages/'. $existPage->id, $params);

        $updatedPost = Post::where('post_type', Post::PAGE)->where('id', $existPage->id)->first();
        $this->assertEquals($params['title'], $updatedPost->title);
        $this->assertEquals($existPage->id, $updatedPost->id);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/admin/blog/pages/'. $existPage->id . '/edit');
        $response->assertSessionHas('success', __('blog::pages.success_update_message'));
    }

    /**
     * Test admin can delete a page
     *
     * @return void
     */
    public function testAdminCanDeleteAPage()
    {
        $existPage = Post::factory()->create(['post_type' => Post::PAGE]);

        $response = $this
            ->actingAs($this->admin)
            ->delete('/admin/blog/pages/'. $existPage->id);

        $page = Post::where('id', $existPage->id)->first();
        $this->assertNull($page);

        $response->assertStatus(302);
        $response->assertRedirect('/admin/blog/pages');
        $response->assertSessionHas('success', __('blog::pages.success_delete_message'));
    }

    // ==================
    // Negative Cases
    // ==================
    public function testNonAdminUserCannotViewThePageIndex()
    {
        $response = $this
            ->actingAs($this->user)
            ->get('/admin/blog/pages');

        $response->assertStatus(403);
    }

    /**
     * Test admin can not add a page with blank title
     *
     * @return void
     */
    public function testAdminCanNotAddAPageWithBlankTitle()
    {
        $params = [];

        $response = $this
            ->actingAs($this->admin)
            ->post('/admin/blog/pages', $params);

        $response->assertStatus(302);
        $response->assertSessionHasErrors();

        $errors = session('errors');

        $this->assertEquals('The title field is required.', $errors->get('title')[0]);
    }

    /**
     * Test admin can not add a duplicated page title
     *
     * @return void
     */
    public function testAdminCanNotAddADuplicatedPageTitle()
    {
        $existPage = Post::factory()->create(['post_type' => Post::PAGE]);

        $params = [
            'title' => $existPage->title,
        ];

        $response = $this
            ->actingAs($this->admin)
            ->post('/admin/blog/pages', $params);

        $response->assertStatus(302);
        $response->assertSessionHasErrors();

        $errors = session('errors');
        $this->assertEquals('The title has already been taken.', $errors->get('title')[0]);
    }
}

<?php

namespace Modules\Blog\Tests\Feature\Admin;

use Modules\Blog\Tests\BlogTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;


use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Modules\Blog\Entities\Post;

class PostTest extends BlogTestCase
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
            ]
        );

        Post::factory()->create(
            [
                'title' => 'Title two',
            ]
        );
    }

    // ==================
    // Positive Cases
    // ==================

    /**
     * Test admin can view the posts
     *
     * @return void
     */
    public function testAdminCanViewThePostIndex()
    {
        $this->initPosts();

        $response = $this
            ->actingAs($this->admin)
            ->get('/admin/blog/posts');
        
        $response->assertStatus(200);
        $response->assertSee('Title one');
        $response->assertSee('Title two');
    }

    /**
     * Test admin can add a post
     *
     * @return void
     */
    public function testAdminCanAddAPost()
    {
        $params = [
            'title' => $this->faker->name(),
        ];

        $response = $this
            ->actingAs($this->admin)
            ->post('/admin/blog/posts', $params);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();

        $post = Post::first();
        $this->assertNotNull($post);
        $this->assertEquals($params['title'], $post->title);

        $response->assertRedirect('/admin/blog/posts/'. $post->id .'/edit');
        $response->assertSessionHas('success', __('blog::posts.success_create_message'));
    }

    /**
     * Test admin can add a post
     *
     * @return void
     */
    public function testAdminCanAddAPostWithMetaKeywordAndDescription()
    {
        $params = [
            'title' => $this->faker->name(),
            'keywords' => ['keywords 1', 'keywords 2'],
            'description' => 'meta desc',
        ];

        $response = $this
            ->actingAs($this->admin)
            ->post('/admin/blog/posts', $params);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();

        $post = Post::first();

        $this->assertNotNull($post);
        $this->assertEquals($params['title'], $post->title);
        $this->assertEquals($params['description'], $post->metas['description']);
        $this->assertEquals($params['keywords'], $post->metas['keywords']);

        $response->assertRedirect('/admin/blog/posts/'. $post->id .'/edit');
        $response->assertSessionHas('success', __('blog::posts.success_create_message'));
    }

    public function testAdminCanViewTheUpdatePostFormWithNoMetaDescription()
    {
        $existPost = Post::factory()->create([
            'metas' => [
                'keywords' => ['keywords 1', 'keywords 2'],
            ]
        ]);
        
        $response = $this
            ->actingAs($this->admin)
            ->get('/admin/blog/posts/'. $existPost->id . '/edit');
        
        $response->assertStatus(200);
        $response->assertSessionHasNoErrors();
    }

    /**
     * Test admin can update a post
     *
     * @return void
     */
    public function testAdminCanUpdateAPost()
    {
        $existPost = Post::factory()->create();

        $params = [
            'title' => $this->faker->name(),
        ];

        $response = $this
            ->actingAs($this->admin)
            ->put('/admin/blog/posts/'. $existPost->id, $params);

        $updatedPost = Post::find($existPost->id);
        $this->assertEquals($params['title'], $updatedPost->title);
        $this->assertEquals($existPost->id, $updatedPost->id);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/admin/blog/posts/'. $existPost->id . '/edit');
        $response->assertSessionHas('success', __('blog::posts.success_update_message'));
    }

    /**
     * Test admin can delete a post
     *
     * @return void
     */
    public function testAdminCanDeleteAPost()
    {
        $existPost = Post::factory()->create();

        $response = $this
            ->actingAs($this->admin)
            ->delete('/admin/blog/posts/'. $existPost->id);

        $post = Post::where('id', $existPost->id)->first();
        $this->assertNull($post);

        $response->assertStatus(302);
        $response->assertRedirect('/admin/blog/posts');
        $response->assertSessionHas('success', __('blog::posts.success_delete_message'));
    }

    // ==================
    // Negative Cases
    // ==================
    public function testNonAdminUserCannotViewThePostIndex()
    {
        $response = $this
            ->actingAs($this->user)
            ->get('/admin/blog/posts');

        $response->assertStatus(403);
    }

    /**
     * Test admin can not add a post with blank title
     *
     * @return void
     */
    public function testAdminCanNotAddAPostWithBlankTitle()
    {
        $params = [];

        $response = $this
            ->actingAs($this->admin)
            ->post('/admin/blog/posts', $params);

        $response->assertStatus(302);
        $response->assertSessionHasErrors();

        $errors = session('errors');

        $this->assertEquals('The title field is required.', $errors->get('title')[0]);
    }

    /**
     * Test admin can not add a duplicated post title
     *
     * @return void
     */
    public function testAdminCanNotAddADuplicatedPostTitle()
    {
        $existPost = Post::factory()->create();

        $params = [
            'title' => $existPost->title,
        ];

        $response = $this
            ->actingAs($this->admin)
            ->post('/admin/blog/posts', $params);

        $response->assertStatus(302);
        $response->assertSessionHasErrors();

        $errors = session('errors');
        $this->assertEquals('The title has already been taken.', $errors->get('title')[0]);
    }
}

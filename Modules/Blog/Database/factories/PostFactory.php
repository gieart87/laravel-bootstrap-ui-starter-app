<?php
namespace Modules\Blog\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

use App\Models\User;

class PostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\Blog\Entities\Post::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user = User::factory()->create();
        $title = $this->faker->sentence();

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'post_type' => 'Post',
            'code' => Str::random(10),
            'publish_date' => date('Y-m-d H:i:s'),
            'status' => 1,
            'excerpt' => $this->faker->paragraph(),
            'body' => $this->faker->paragraph(),
            'user_id' => $user->id,
        ];
    }
}


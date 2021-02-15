<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('blog')->group(function () {
    Route::get('/', 'BlogController@index');
});

Route::prefix('admin/blog')->namespace('\Modules\Blog\Http\Controllers\Admin')->middleware(['auth'])->group(function () { // phpcs:ignore
    
    Route::get('posts/trashed', 'PostController@trashed');
    Route::get('posts/{id}/restore', 'PostController@restore');
    Route::resource('posts', 'PostController');
});

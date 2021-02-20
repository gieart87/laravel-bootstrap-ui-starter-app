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

Route::prefix('admin/blog')->as('blog-')->namespace('\Modules\Blog\Http\Controllers\Admin')->middleware(['auth'])->group(function () { // phpcs:ignore
    
    Route::get('posts/trashed', 'PostController@trashed')->name('posts.trashed');
    Route::get('posts/{id}/restore', 'PostController@restore')->name('posts.restore');
    Route::resource('posts', 'PostController');

    Route::get('pages/trashed', 'PageController@trashed')->name('pages.trashed');
    Route::get('pages/{id}/restore', 'PageController@restore')->name('pages.restore');
    Route::resource('pages', 'PageController');

    Route::resource('categories', 'CategoryController');
});

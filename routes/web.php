<?php

use App\Http\Controllers;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

require __DIR__ . '/auth.php';

Route::get('/', [Controllers\HomeController::class, 'index'])
    ->name('home.index');

Route::get('/upload', [Controllers\UploadController::class, 'index'])
    ->name('upload.index');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// tags
Route::get('/tags/{slug}', [Controllers\TagController::class, 'show'])
    ->name('tags.show');

// users
Route::get('/user/{user:username}', [Controllers\UserController::class, 'bins'])
    ->name('user.bins');

Route::get('/user/{user:username}/favorites', [Controllers\UserController::class, 'favorites'])
    ->name('user.favorites');

Route::get('/profile/{user:profile_image}', [Controllers\UserController::class, 'profileImage'])
    ->where('profile_image', '.*')
    ->name('user.profileImage');

// account
Route::get('/account/settings', [Controllers\AccountController::class, 'settings'])
    ->middleware(['auth'])
    ->name('account.settings');

Route::get('/account/password', [Controllers\AccountController::class, 'password'])
    ->middleware(['auth'])
    ->name('account.password');

Route::post('/account', [Controllers\AccountController::class, 'update'])
    ->middleware(['auth', 'password.confirm'])
    ->name('account.update');

Route::post('/account/delete-image', [Controllers\AccountController::class, 'deleteProfileImage'])
    ->middleware(['auth', 'password.confirm'])
    ->name('account.deleteProfileImage');

// bins
Route::get('/b/{bin:nanoid}', [Controllers\BinController::class, 'private'])
    ->name('bin.private');

Route::post('/bins', [Controllers\BinController::class, 'store'])
    ->name('bin.store');

Route::get('/bins/{bin:nanoid}', [Controllers\BinController::class, 'show'])
    ->name('bin.show');

Route::post('/bins/{bin:nanoid}/tag', [Controllers\BinController::class, 'tag'])
    ->middleware(['can:manage,bin'])
    ->name('bin.tag');

Route::post('/bins/{bin:nanoid}/untag', [Controllers\BinController::class, 'untag'])
    ->middleware(['can:manage,bin'])
    ->name('bin.untag');

Route::post('/bins/{bin:nanoid}/publish', [Controllers\BinController::class, 'publish'])
    ->middleware(['auth', 'can:manage,bin'])
    ->name('bin.publish');

Route::post('/bins/{bin:nanoid}/unpublish', [Controllers\BinController::class, 'unpublish'])
    ->middleware(['auth', 'can:manage,bin'])
    ->name('bin.unpublish');

Route::get('/bins/{bin:nanoid}/edit', [Controllers\BinController::class, 'edit'])
    ->middleware(['can:manage,bin'])
    ->name('bin.edit');

Route::put('/bins/{bin:nanoid}', [Controllers\BinController::class, 'update'])
    ->middleware(['can:manage,bin'])
    ->name('bin.update');

Route::delete('/bins/{bin:nanoid}', [Controllers\BinController::class, 'destroy'])
    ->middleware(['can:manage,bin'])
    ->name('bin.destroy');

Route::post('/bins/{bin:nanoid}/comments', [Controllers\BinController::class, 'comments'])
    ->middleware(['auth'])
    ->name('bin.comments');

Route::post('/bins/{bin:nanoid}/favorite', [Controllers\BinController::class, 'favorite'])
    ->middleware(['auth'])
    ->name('bin.favorite');

Route::post('/bins/{bin:nanoid}/upvote', [Controllers\BinController::class, 'upvote'])
    ->middleware(['auth'])
    ->name('bin.upvote');

Route::post('/bins/{bin:nanoid}/downvote', [Controllers\BinController::class, 'downvote'])
    ->middleware(['auth'])
    ->name('bin.downvote');

Route::post('/bins/{bin:nanoid}/images', [Controllers\BinController::class, 'images'])
    ->middleware(['can:manage,bin'])
    ->name('bin.images');

Route::get('/bins/{bin:nanoid}/download', [Controllers\BinController::class, 'download'])
    ->name('bin.download');

// comments
Route::post('/comments/{comment:nanoid}/replies', [Controllers\CommentController::class, 'replies'])
    ->middleware(['auth'])
    ->name('comment.replies');

Route::delete('/comments/{comment:nanoid}', [Controllers\CommentController::class, 'destroy'])
    ->middleware(['auth'])
    ->name('comment.destroy');

Route::post('/comments/{comment:nanoid}/upvote', [Controllers\CommentController::class, 'upvote'])
    ->middleware(['auth'])
    ->name('comment.upvote');

Route::post('/comments/{comment:nanoid}/downvote', [Controllers\CommentController::class, 'downvote'])
    ->middleware(['auth'])
    ->name('comment.downvote');

// images
Route::get('/images/{image:nanoid}', [Controllers\ImageController::class, 'show'])
    ->name('image.show');

Route::put('/images/{image:nanoid}', [Controllers\ImageController::class, 'update'])
    ->middleware(['can:manage,image'])
    ->name('image.update');

Route::delete('/images/{image:nanoid}', [Controllers\ImageController::class, 'destroy'])
    ->middleware(['can:manage,image'])
    ->name('image.destroy');

Route::post('/images/{image:nanoid}/favorite', [Controllers\ImageController::class, 'favorite'])
    ->middleware(['auth'])
    ->name('image.favorite');

Route::get('/images/{image:nanoid}/download', [Controllers\ImageController::class, 'download'])
    ->name('image.download');

Route::get('/img/{image:filename}', [Controllers\ImageController::class, 'url'])
    ->where('filename', '.*')
    ->name('image.url');

<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashbordController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\FollowerController;
use App\Http\Controllers\IdeaController;
use App\Http\Controllers\IdeaLikeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [DashbordController::class, 'index'])->name('dashboard');

// ! prefix will help in instead of giving a prefix to each of the routes, it will give it as a group to all
// ! as will help in prefix for route names in the group
// ! we can have route groups inside routegroups
Route::group(['prefix' => 'ideas', 'as' => 'ideas.', 'middleware' => ['auth']], function () {
    Route::get('/{idea}', [IdeaController::class, 'show'])->name('show')->withoutMiddleware(['auth']);
    Route::post('', [IdeaController::class, 'store'])->name('store');
    // ! this middleware see if the person is logged in or not, if he was not logged in it will send it to log in page: 
    Route::get('/{idea}/edit', [IdeaController::class, 'edit'])->name('edit');
    Route::put('/{idea}', [IdeaController::class, 'update'])->name('update');
    // todo: inside {} is a variable and we can pass value to it
    Route::delete('/{idea}', [IdeaController::class, 'destroy'])->name('destroy');
    Route::post('/{idea}/comments', [CommentController::class, 'store'])->name('comments.store');
});

// ? another way to do all the routes above (index, create, store, show, edit, update, destroy):
// ? if there are some methods that you do not use, you should use except:
// ? we can give middleware as well and not giving middlewarware with only method:
// todo: Route::resource('ideas', IdeaController::class)->except(['index', 'create'])->middleware('auth');
// todo: Route::resource('ideas', IdeaController::class)->only(['show']);
// ? this one is the same as the post comment above:
// todo: Route::resource('ideas.comments', CommentController::class)->only(['store'])->middleware('auth');

Route::resource('users', UserController::class)->only(['edit', 'update', 'destroy'])->middleware('auth');
Route::resource('users', UserController::class)->only(['show']);

Route::get('profile', [UserController::class, 'profile'])->middleware('auth')->name('profile');

Route::post('users/{user}/follow', [FollowerController::class, 'store'])->middleware('auth')->name('users.follow');
Route::post('users/{user}/unfollow', [FollowerController::class, 'distroy'])->middleware('auth')->name('users.unfollow');

Route::post('ideas/{idea}/like', [IdeaLikeController::class, 'store'])->middleware('auth')->name('ideas.like');
Route::post('ideas/{idea}/unlike', [IdeaLikeController::class, 'distroy'])->middleware('auth')->name('ideas.unlike');

// ! because invokbale controller just do one action, we do not need do mention the method name:
Route::get('/feed', FeedController::class)->name('feed')->middleware('auth');

// ! to pass more than one middleware you can put it inside an array:
// ? first way to give role base functions:
// todo: Route::get('/admin', [AdminDashboardController::class, 'index'])->name('admin.dashboard')->middleware(['auth', 'admin']);
// ? second way to give role base functions by using gate in the controller:
// todo: Route::get('/admin', [AdminDashboardController::class, 'index'])->name('admin.dashboard')->middleware('auth');
// ? third way to give role base functions by using can middleware, inside can we must give a gate name:
Route::get('/admin', [AdminDashboardController::class, 'index'])->name('admin.dashboard')->middleware(['auth', 'can:admin']);

// ! here is for different languages:
Route::get('lang/{lang}', function ($lang) {
    app()->setLocale($lang);
    // ! for saving or preserving the locale over multiple request, we need to store it in our sessions:
    // * Cookies are client-side files on a local computer that hold user information. Sessions are server-side files that contain user data. Cookies end on the lifetime set by the user. When the user quits the browser or logs out of the programmed, the session is over.
    session()->put('locale', $lang);

    // todo: dd(app()->getLocale());

    return redirect()->route('dashboard');
})->name('lang');

Route::get('/terms', function () {
    return view('terms');
});

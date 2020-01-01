<?php

use Illuminate\Http\Request;
// use Symfony\Component\Routing\Annotation\Route;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route Authentication
Route::group(['prefix' => 'auth'], function () {
    Route::post('login', 'AuthController@login');
    Route::post('register', 'AuthController@register');
    Route::post('request/reset-password', 'AuthController@requestResetPassword');
    Route::post('accept/reset-password', 'AuthController@acceptResetPassword');
    Route::group(['middleware' => ['jwt']], function () {
        Route::put('change-password', 'AuthController@changePassword');
        Route::post('logout', 'AuthController@logout');
    });
});

//Route user
Route::group(['prefix' => 'user'], function () {
    Route::post('accept/create-user', 'AdminController@acceptCreateUser');
    Route::middleware(['jwt'])->group(function () {
        Route::get('all-activities', 'UserController@getAllActivities');
        Route::get('reactions-activities', 'UserController@getReactionsActivities');
        Route::get('comments-activities', 'UserController@getCommentsActivities');

        Route::get('profile', 'UserController@getProfile');
        Route::post('change-avatar', 'UserController@changeUserAvatar');
        Route::get('search', 'UserController@search');
    });
});

//Route admin -- only Admin can access
Route::group(['prefix' => 'admin'], function () {
    Route::post('accept/create-user', 'AdminController@acceptCreateUser');
    Route::middleware(['jwt', 'admin'])->group(function () {
        Route::post('request/create-user', 'AdminController@requestCreateUser');
        Route::get('all-user', 'AdminController@getAllUser');
        Route::get('statistic', 'AdminController@getStatistic');

        Route::put('/{id}', 'AdminController@editUser');
        Route::delete('/{id}', 'AdminController@deleteUser');
    });
});

//Route ask
Route::group(['prefix' => 'ask'], function () {
    Route::middleware(['jwt'])->group(function () {
        Route::get('all', 'AskController@getAllPost');
        Route::get('news-feed', 'AskController@getNewsFeed');
        Route::post('reaction/{askId}', 'AskController@reactionPost');

        Route::get('/{askId}', 'AskController@getPost');
        Route::post('/{askId}', 'AskController@updatePost');
        Route::post('/', 'AskController@createPost');
        Route::delete('/{askId}', 'AskController@deletePost');
    });
});
//Route comment
Route::group(['prefix' => 'comment'], function () {
    Route::middleware(['jwt'])->group(function () {
        Route::get('ask/{askId}', 'CommentController@getCommentAsk');
        Route::put('/{commentId}', 'CommentController@updateComment');
        Route::post('/', 'CommentController@createComment');
        Route::delete('/{commentId}', 'CommentController@deleteComment');
    });
});
//Route video
Route::group(['prefix' => 'video'], function () {
    Route::middleware(['jwt'])->group(function () {
        Route::get('all', 'VideoController@getAllVideo');
        Route::get('/{videoId}', 'VideoController@getVideo');

        Route::middleware(['admin'])->group(function () {
            Route::post('/{videoId}', 'VideoController@updateVideo');
            Route::post('/', 'VideoController@createVideo');
            Route::delete('/{videoId}', 'VideoController@deleteVideo');
        });
    });
});
//Route Learn
Route::group(['prefix' => 'learn'], function () {
    Route::middleware(['jwt'])->group(function () {
        Route::middleware(['admin'])->group(function () {
            Route::post('/{learnId}', 'LearnController@updatePost');
            Route::post('/', 'LearnController@createPost');
            Route::delete('/{learnId}', 'LearnController@deletePost');
        });
        Route::get('all', 'LearnController@getAllPost');
        Route::get('/{learnId}', 'LearnController@getPost');
    });
});

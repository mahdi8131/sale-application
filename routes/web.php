<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\SessionAuthenticate;
use App\Http\Middleware\TokenVerificationMiddleware;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;




// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [HomeController::class, 'index'])->name('home');

//User all routes
Route::post('/user-registration', [UserController::class, 'UserRegistration'])->name('user.registration');
Route::post('/user-login', [UserController::class, 'UserLogin'])->name('user.login');
Route::post('/send-otp', [UserController::class, 'SendOTPCode'])->name('SendOTPCode');
Route::post('/verify-otp', [UserController::class, 'VerifyOTP'])->name('VerifyOTP');



Route::middleware(SessionAuthenticate::class)->group(function () {
    //reset password
    Route::post('/reset-password', [UserController::class, 'ResetPassword']);

    Route::get('/DashboardPage', [UserController::class, 'DashboardPage']);
    Route::get('/user-logout', [UserController::class, 'UserLogout']);



    //Post all routes
    Route::post('/create-post', [PostController::class, 'CreatePost'])->name('CreatePost');
    Route::get('/list-post', [PostController::class, 'PostList'])->name('PostList');
    Route::post('/post-by-id', [PostController::class, 'PostById'])->name('PostById');
    Route::post('/update-post', [PostController::class, 'PostUpdate'])->name('PostUpdate');
    Route::get('/delete-post/{id}', [PostController::class, 'PostDelete'])->name('PostDelete');
    Route::get('/PostPage', [PostController::class, 'PostPage'])->name('Post.page');
    Route::get('/PostSavePage', [PostController::class, 'PostSavePage'])->name('PostSavePage');
    Route::get('/post-detail/{id}', [PostController::class, 'PostDetail'])->name('PostDetail');


    // Route::get('/posts/{post_id}/comments', [CommentController::class, 'showPostComments']);
    // Route::post('/comments', [CommentController::class, 'store']);
    // Route::put('/comments/{id}', [CommentController::class, 'update']);
    // Route::delete('/comments/{id}', [CommentController::class, 'destroy']);

    Route::post('/comments', [CommentController::class, 'store']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);

    Route::get('/TagPage', [TagController::class, 'TagPage']);
    Route::get('/TagSavePage', [TagController::class, 'TagSavePage']);
    Route::post('/CreateTag', [TagController::class, 'CreateTag']);
    Route::post('/TagUpdate', [TagController::class, 'TagUpdate']);
    Route::get('/TagDelete/{id}', [TagController::class, 'TagDelete']);
    Route::get('/TagList', [TagController::class, 'TagList']);
    Route::post('/TagById', [TagController::class, 'TagById']);



    //Dashboard Summary
    Route::get('/dashboard-summary', [DashboardController::class, 'DashboardSummary'])->name('DashboardSummary');

    //Resetpassword page
    Route::get('/reset-password', [UserController::class, 'ResetPasswordPage']);

    Route::get('/ProfilePage', [UserController::class, 'ProfilePage']);
    Route::get('/user-update', [UserController::class, 'UserUpdate']);
});

// Route::get('/posts', [PostController::class, 'PostPage'])->middleware('auth');

//Pages all routes
Route::get('/login', [UserController::class, 'LoginPage'])->name('login.page');
Route::get('/registration', [UserController::class, 'RegistrationPage'])->name('registration.page');
Route::get('/send-otp', [UserController::class, 'SendOTPPage'])->name('sendotp.page');
Route::get('/verify-otp', [UserController::class, 'VerifyOTPPage'])->name('VerifyOTPPage');
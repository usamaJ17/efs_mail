<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ComposeController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\ForwardEmailController;
use App\Http\Controllers\GeneralSettingController;
use App\Http\Controllers\ImportantEmailController;
use App\Http\Controllers\InboxController;
use App\Http\Controllers\StarredEmailsController;
use App\Http\Controllers\ToggleAsImportantEmailController;
use App\Http\Controllers\SentController;
use App\Http\Controllers\ToggleAsReadEmailController;
use App\Http\Controllers\ToggleAsStarredEmailController;
use App\Http\Controllers\TrashController;
use App\Jobs\ImportUserEmailData;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('test/login/{id}', function (string $id) {
    Auth::loginUsingId($id);
    return redirect()->route('inbox');
});

Route::get('/', function () {
    return Inertia::render('Welcome');
});

Route::middleware('auth')->group(function () {

    Route::get('inbox', InboxController::class)->name('inbox');
    Route::get('sent', SentController::class)->name('sent');
    Route::get('trash', TrashController::class)->name('trash');
    Route::get('starred', StarredEmailsController::class)->name('starred');
    Route::get('important', ImportantEmailController::class)->name('important');
    Route::post('compose', ComposeController::class)->name('compose');
    Route::post('forward', ForwardEmailController::class)->name('forward');
    Route::post('emails/store-reply/{email}', [EmailController::class, 'storeReply'])->name('emails.store-reply');

    Route::get('toggle-as-read/{email}', ToggleAsReadEmailController::class)
        ->name('toggle-as-read');
    Route::get('toggle-as-important/{email}', ToggleAsImportantEmailController::class)
        ->name('toggle-as-important');
    Route::get('toggle-as-starred/{email}', ToggleAsStarredEmailController::class)
        ->name('toggle-as-starred');

    Route::post('emails/mark-read-all', [EmailController::class, 'markReadAll'])->name('emails.mark-read-all');
    Route::post('emails/delete-many', [EmailController::class, 'deleteMany'])->name('emails.delete-many');
    Route::delete('emails/permanent-delete/{id}', [EmailController::class, 'permanentDestroy'])
        ->name('emails.permanent.destroy');
    Route::get('emails/restore/{id}', [EmailController::class, 'restore'])
        ->name('emails.restore');
    Route::get('emails/search', [EmailController::class, 'search'])->name('emails.search');
    Route::resource('emails', EmailController::class)->only(['destroy', 'show']);

    Route::get('admin/dashboard', [AdminController::class, 'getAdminDashboard'])
        ->name('admin.get-admin-dashboard');
    Route::post('admin/toggle-admin-status/{user}', [AdminController::class, 'toggleAdminStatus'])
        ->name('admin.toggle-admin-status');
    Route::post('logo-upload', [GeneralSettingController::class, 'logoUpload'])
        ->name('general-setting.logo-upload');
    Route::post('favicon-upload', [GeneralSettingController::class, 'faviconUpload'])
        ->name('general-setting.favicon-upload');

});

require __DIR__ . '/auth.php';


Route::get('start_jobs', function(){
    // ImportUserEmailData::dispatch(1);
    return redirect()->route('inbox');
});

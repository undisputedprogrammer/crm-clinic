<?php

use App\Http\Controllers\DoctorController;
use App\Http\Controllers\FollowupController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\Remarkcontroller;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TestController;
use App\Models\Followup;
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

Route::middleware('auth')->group(function(){
    Route::get('/logout',[PageController::class,'destroy'])->name('user-logout');
    Route::get('/overview', [PageController::class, 'overview'])->name('overview');
    Route::get('/leads',[PageController::class, 'leadIndex'])->name('fresh-leads');
    Route::post('/remark/store',[Remarkcontroller::class, 'store'])->name('add-remark');
    Route::get('/lead/change/segment',[LeadController::class, 'change'])->name('change-segment');
    Route::get('/lead/change/valid',[LeadController::class, 'changevalid'])->name('change-valid');
    Route::get('/lead/change/genuine',[LeadController::class, 'changeGenuine'])->name('change-genuine');
    Route::get('/followups',[PageController::class, 'followUps'])->name('followups');
    Route::post('/followup/initiate',[FollowupController::class, 'initiate'])->name('initiate-followup');
    Route::post('/followup/store',[FollowupController::class, 'store'])->name('process-followup');
    Route::get('/search',[PageController::class, 'searchIndex'])->name('search-index');
    Route::post('/search',[SearchController::class, 'index'])->name('get-results');
    Route::post('/followup/new',[FollowupController::class,'next'])->name('next-followup');
    Route::post('/import/lead',[ImportController::class, 'importLead'])->name('import-leads');
    Route::get('/questions',[PageController::class, 'questionIndex'])->name('manage-questions');
    Route::post('/questions/store',[QuestionController::class, 'store'])->name('add-question');
    Route::post('/questions/update',[QuestionController::class, 'update'])->name('update-question');
    Route::get('/doctors', [DoctorController::class, 'index'])->name('doctors.index');
    Route::post('/doctors', [DoctorController::class, 'store'])->name('doctors.store');
    Route::post('/doctors/{id}', [DoctorController::class, 'update'])->name('doctors.update');
});

Route::get('/',[PageController::class, 'home']);

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/note', [NoteController::class, 'index'])->name('note.index')->middleware(['auth']);
Route::get('/notes/search', [NoteController::class, 'search'])->name('note.search')->middleware(['auth']);
Route::get('/notes/{id}/content', [NoteController::class, 'getContent'])->name('note.content')->middleware(['auth']);
Route::post('/notes/{id}/update-content', [NoteController::class, 'updateContent'])->name('note.update-content')->middleware(['auth']);
Route::post('/notes/{id}/update-title', [NoteController::class, 'updateTitle'])->name('note.update-title')->middleware(['auth']);
Route::post('/notes', [NoteController::class, 'store'])->name('note.store')->middleware(['auth']);
Route::delete('/notes/{note}', [NoteController::class, 'destroy'])->name('note.destroy')->middleware(['auth']);
Route::put('/notes/{note}', [NoteController::class, 'update'])->name('note.update')->middleware(['auth']);

Route::get('/app-settings', function () {
    $colors = Auth::user()->getColors();
    return view('note/app-settings', compact('colors'));
})->name('app-settings')->middleware(['auth']);

Route::post('/colors', [ColorController::class, 'update'])->name('colors.update')->middleware(['auth']);
Route::post('/colors/reset', [ColorController::class, 'reset'])->name('colors.reset')->middleware(['auth']);

Route::post('/folders', [FolderController::class, 'store'])->name('folder.store')->middleware(['auth']);
Route::delete('/folders/{folder}', [FolderController::class, 'destroy'])->name('folder.destroy')->middleware(['auth']);
Route::post('/move', [FolderController::class, 'move'])->name('folder.move')->middleware(['auth']);
Route::put('/folders/{folder}', [FolderController::class, 'update'])->name('folder.update')->middleware(['auth']);

require __DIR__ . '/auth.php';

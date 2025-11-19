<?php

use App\Livewire\ContactForm;
use App\Livewire\ContactSubmissionList;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/contact', ContactForm::class)->name('contact');
Route::get('/admin/submissions', ContactSubmissionList::class)->name('admin.submissions');

Route::livewire('/dashboard', 'pages::dashboard')->name('dashboard');
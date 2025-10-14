<?php

use App\Livewire\ContactForm;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/contact', ContactForm::class)->name('contact');

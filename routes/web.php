<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

Route::view('/about', 'pages.about')->name('about');
Route::view('/institutions', 'pages.institutions')->name('institutions');
Route::view('/programs', 'pages.programs')->name('programs');
Route::view('/news-events', 'pages.news-events')->name('news-events');
Route::view('/gallery', 'pages.gallery')->name('gallery');
Route::view('/downloads', 'pages.downloads')->name('downloads');
Route::view('/career', 'pages.career')->name('career');
Route::view('/contact', 'pages.contact')->name('contact');

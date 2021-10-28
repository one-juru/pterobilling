<?php

use App\Services\Extensions;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/client/{any?}', function () {
  return view('client');
})
  ->where('any', '^(?!api).*$')
  ->middleware('auth');

Route::get('/admin/{any?}', function () {
  return view('admin');
})
  ->where('any', '^(?!api).*$')
  ->middleware('auth');

Route::get('/{any?}', function () {
  $plugins_scripts = app(Extensions::class)->get_scripts();

  $loading_script = app(Extensions::class)->generate_loader();

  return view('store', [
    'plugin_scripts' => $plugins_scripts,
    'loading_script' => $loading_script
  ]);
})
  ->where('any', '^(?!api).*$');

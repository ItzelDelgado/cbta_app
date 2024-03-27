<?php

use App\Http\Controllers\Admin\HospitalController;
use App\Http\Controllers\Admin\InputController;
use App\Http\Controllers\Admin\MedicineController;
use App\Http\Controllers\Admin\SolicitudController;
use App\Http\Controllers\Admin\UserController;
use App\Models\Solicitud;
use Illuminate\Support\Facades\Route; //Importamos para generar nuestras rutas.

//Debemos avisarle a laravel que hemos creado un nuevo archivo de rutas en providers
Route::get('/', function(){
//     session()->flash('swal', [
//         'icon'=>"error",
//         'title'=>"Oops...",
//         'text'=>"Something went wrong :(!",
//         'footer'=>'<a href="#">Why do I have this issue?</a>'
// ]);
    return view('admin.dashboard');
})->name('dashboard');


Route::resource('/users', UserController::class);

Route::resource('/hospitals', HospitalController::class)
->except(['show','destroy']);

Route::resource('/medicines', MedicineController::class)
->except(['destroy']);

Route::resource('solicitudes', SolicitudController::class)->parameter('solicitudes', 'solicitud')->except(['destroy']);

Route::get('/orden-de-preparacion', [SolicitudController::class, 'ordenPreparacion'])->name('preparacion');

Route::get('/remision', [SolicitudController::class, 'remision'])->name('remision');

Route::get('/envio', [SolicitudController::class, 'envio'])->name('envio');

Route::get('/etiqueta', [SolicitudController::class, 'etiqueta'])->name('etiqueta');

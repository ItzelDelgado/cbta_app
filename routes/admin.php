<?php 
use Illuminate\Support\Facades\Route; //Importamos para generar nuestras rutas.
//Debemos avisarle a laravel que hemos creado un nuevo archivo de rutas en providers
Route::get('/', function(){
    session()->flash('swal', [
        'icon'=>"error",
        'title'=>"Oops...",
        'text'=>"Something went wrong :(!",
        'footer'=>'<a href="#">Why do I have this issue?</a>'
]);
    return view('admin.dashboard');
})->name('admin.dashboard');


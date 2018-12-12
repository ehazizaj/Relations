<?php
use App\User;
use App\Address;
/*
|--------------------------------------------------------------------------
| NOTE: ALL THE ROUTES ARE "GET" BECAUSE THE FUNCTIONS ARE BEING USED DIRECTLY FORM ROUTE FILES
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/







/*
|-----------------------------------------------------------------------------------------------
| NOTE: ALL THE ROUTES ARE "GET" BECAUSE THE FUNCTIONS ARE BEING USED DIRECTLY FORM ROUTE FILES
|-----------------------------------------------------------------------------------------------
*/


Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
|One to one relation actions
|--------------------------------------------------------------------------
*/

Route::get('/insert_address/{id}', function ($id){

    $user = User::findOrFail($id);

    $address = new Address(['name'=>'Big Ben London, UK']);

    $user->address()->save($address);
    return "done";

});
Route::get('/update_address/{id}', function ($id){
    // First Way


//    $address = Address::whereUserId($id)->first();
//
//    $address->name= "4353 5th Avenue, New York";
//
//    $address->save();
//    return "done";


    // Second Way
    $user = User::findOrFail($id);

    $user->address()->update(['name'=>'4353 5th Avenue, New York']);

    return "done";

});


Route::get('/read_address/{id}', function ($id){

    $user = User::findOrFail($id);
    return $user->address->name;


});

//Soft Delete
Route::get('/delete_address/{id}', function ($id){

    $user = User::findOrFail($id);
    $user->address->delete();
    return "done";

});

//Hard  Delete
Route::get('/force_delete_address/{id}', function ($id){

    $user = User::findOrFail($id);
    $user->address->forcedelete();
    return "done";
});

//Restore From Soft Delete
//In oreder to do restore first way, address function at user model should be changed to this:
//return $this->hasOne('App\Address','user_id')->withTrashed();

Route::get('/restore_delete_address/{id}', function ($id){

    //First Way
//    $user = User::findOrFail($id);
//    $user->address->restore();
//    return "done";

    //Second Way
    $address = Address::whereUserId($id)->withTrashed()->first();
    $address->restore();
    return "done";

});

/*
|--------------------------------------------------------------------------
|One to many relation actions
|--------------------------------------------------------------------------
*/
Route::get('/create/post', function (){



});


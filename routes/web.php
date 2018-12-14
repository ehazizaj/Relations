<?php
use App\User;
use App\Address;
use App\Post;
use App\Role;
use App\Staff;
use App\Product;
use App\Photo;
use  App\Article;
use App\Video;
use App\Tag;
use Illuminate\Support\Facades\Route;

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
Route::get('/create/post/{id}', function ($id){

    $user = User::findOrFail($id);

    $post = new Post(['title'=>'First Post', 'body'=>'Post BOdy']);

    $user->posts()->save($post);

    return 'done';

});


Route::get('/posts/{id}', function ($id){
  $user = User::findOrFail($id);

  foreach ($user->posts as $post){

     echo  "Title:". $post->title . "&nbsp" . "&nbsp" , "Body:".  $post->body . "<br>";

        }
});

Route::get('/update/post/{id}/{post}', function ($id, $post){

    $user = User::findOrFail($id);
    $user->posts()->whereId($post)->update(['title'=>'update by update', 'body'=>'update function']);
    return 'done';
});

//NOTE: In order to use Soft Delete we must make a migration to add deleted_at column and include SoftDeletes to model
// like in the Address Model.
//Hard Delete a specific posts
Route::get('/delete/post/{id}/{post}', function ($id, $post){

    $user = User::findOrFail($id);
    $user->posts()->whereId($post)->delete();
    return 'done';
});
//Hard Delete all user posts
Route::get('/delete/post/{id}', function ($id){

    $user = User::findOrFail($id);
    $user->posts()->delete();
    return 'done';
});

//Get User by post id / Inverse One to Many
Route::get('/post/{id}', function ($id){

    $post = Post::findOrFail($id);
   return $post->user->name;

});

/*
|--------------------------------------------------------------------------
|Many to many relation actions
|--------------------------------------------------------------------------
*/
//Create role for user
Route::get('/create/user_role/{id}', function ($id){

    $user = User::findOrFail($id);

//    $role = new Role(['name'=>'Administrator']);
//    $user->roles()->save($role);

    $user->roles()->save(new Role(['name'=>'Operator']));
    return 'done';
});

//Get User Roles
Route::get('user/{id}/role', function ($id){

    $user = User::findOrFail($id);


    foreach ($user->roles as $role)
    {
      echo $role->name ."<br>";
    }
});

//Get Role Users
Route::get('role/{id}/user', function ($id){

    $role = Role::findOrFail($id);

    foreach ($role->users as $user)
    {
        echo $user->name ."<br>";
    }
});

Route::get('delete/{user_id}/{role_id}', function ($user_id, $role_id){

    $user = User::findOrFail($user_id);
    foreach ($user->roles as $role)
    {
        $role->whereId($role_id)->delete();
    }
});

//Attach a role to a user, this writes in "role_users" table
//Attach method add records even if that record already excist.
Route::get('/attach/{id}/{role}', function ($id, $role){

    $user = User::findOrFail($id);

    $user->roles()->attach($role);

});


//Detach a role to a user, this writes in "role_users" table.
//Detach can be also used without the id patameter to detach all the users role.
Route::get('/detach/{id}/{role}', function ($id, $role){

    $user = User::findOrFail($id);
    $user->roles()->detach($role);

});

//Sync method delete every other value that is not n the sync array, it writes "role_users" table.
Route::get('/sync/{id}', function ($id){

    $user = User::findOrFail($id);
    $user->roles()->sync([1,2]);
});

//THE EXAMPLES IN THIS SECTIONS BELOW ARE NOT WITH DYNAMIC ID, I HAVE SET THEM STATIC

/*
|--------------------------------------------------------------------------
| One to Many Polymorphic Relationship
 in this case we save the images of staff and products in the same table
|--------------------------------------------------------------------------
*/


Route::get('insert_polymorphic', function (){

        $staff = Staff::findOrFail(1);
        $staff->photos()->create(['path'=>'photo_2.jpg']);
//        $product = Product::find(1);
//        $product->photos()->create(['path'=>'products.jpg']);
});

Route::get('read_polymorphic', function (){

        $staff = Staff::findOrFail(1);
        $photos= array();
        foreach ($staff->photos as $photo){
            array_push($photos, $photo);
        }
        return $photos;
});

Route::get('update_polymorphic', function (){

    $staff = Staff::findOrFail(1);

    $photo = $staff->photos()->whereId(1)->first();

    $photo->path = "epdate.jpg";

    $photo->save();
});

Route::get('delete_polymorphic', function (){

    $staff = Staff::findOrFail(1);

    $photo = $staff->photos()->whereId(1)->first();

    $photo->delete();

});

//Assign an existing photo to a model
Route::get('assign', function (){

    $staff = Staff::findOrFail(1);
    $photo = Photo::findOrFail(3);
    $staff->photos()->save($photo);

});

//un-assign an existing photo to a model
Route::get('un-assign', function (){

    $staff = Staff::findOrFail(1);
    //$photo = Photo::findOrFail(3);
    $staff->photos()->whereId(2)->update(['imageable_id'=> 0, 'imageable_type'=>'']);

});

/*
|--------------------------------------------------------------------------
| Many to Many Polymorphic Relationship
share the taggs between posts and videos and save it to taggable model
|--------------------------------------------------------------------------
*/

Route::get('/create/poli',function (){

        $article = Article::create(['name'=>'first article']);
        $tag1 = Tag::findOrFail(1);
        $article->tags()->save($tag1);

        $video = Video::create(['name'=>'video.mp4']);
        $tag2 = Tag::findOrFail(2);
        $video->tags()->save($tag2);

});


Route::get('/read/poli',function (){

    $article = Article::findOrFail(1);

    foreach ($article->tags as $tag){

        echo $tag;
    }
});

Route::get('/update/poli',function (){

    $article = Article::findOrFail(1);

    foreach ($article->tags as $tag){

        $tag->whereName('article')->update(['name'=>'article tag updated']);

    }
});

Route::get('/update/2',function (){

    $article = Article::findOrFail(1);

    $tag = Tag::findOrFail(2);
    $article->tags()->save($tag);
    //$article->tags()->attach($tag);
    //$article->tags()->sync([1]);
});


Route::get('/delete/poli',function (){

    $article = Video::findOrFail(1);

    foreach ($article->tags as $tag){

        $tag->whereId(3)->delete();

    }
});


<?php

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
function rq($key = null, $default = null){
	if(!$key) return Request::all();
	return Request::get($key, $default);
}
function user_ins() {
	return new App\User;
}

function question_ins(){
	return new App\Question;
}

function answer_ins(){
	return new App\Answer;
}

function comment_ins(){
	return new App\Comment;
}

Route::get('/', function () {
    return view('welcome');
});

Route::get("api/signup", function() {
	$user = user_ins();
	return $user -> signup();

});

Route::get("api/login", function() {
	$user = user_ins();
	return $user -> login();
});

Route::get("api/logout", function(){
	return user_ins() -> logout();
});

Route::any("api/question/add", function(){
	return question_ins()->add();
});


Route::any("api/question/change", function(){
	return question_ins()->change();
});

Route::any("api/question/read", function(){
	return question_ins()->read();
});

Route::any("api/question/remove", function(){
	return question_ins()->remove();
});

Route::any("api/answer/add", function(){
	return answer_ins()->add();
});

Route::any("api/answer/change", function(){
	return answer_ins()->change();
});

Route::any("api/answer/read", function(){
	return answer_ins()->read();
});

Route::any("api/answer/vote", function(){
	return answer_ins()->vote();
});

Route::any("api/comment/add", function(){
	return comment_ins()->add();
});

Route::any("api/comment/read", function(){
	return comment_ins()->read();
});

Route::any("api/comment/remove", function(){
	return comment_ins()->remove();
});
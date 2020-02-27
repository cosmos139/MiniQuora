<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    public function add() {
    	/*Check if user has logged in */
    	if(!user_ins()->is_logged_in())
    		return ["status" => 0, "login required"];
        /*Check if there exists question_id and content in the parameter */
    	if(!rq("question_id") || !rq("content"))
    		return ["status" => 0, "msg" => "question_id and content are required"];
        /*Check if the question exists */
    	$question = question_ins()->find(rq("question_id"));
    	if(!$question) return ["status" => 0, "msg" => "question not exists"];

        $answered = $this->where(["question_id" => rq("question_id"), "user_id" => session("user_id")])
             ->count();

    	if($answered)
    		return ["status" => 0, "msg" => "duplicate answers"];

    	$this->content = rq("content");
    	$this->question_id = rq("question_id");
    	$this->user_id = session("user_id");

    	return $this->save() ?
    	  ["status" => 1, "id" => $this->id]:
    	  ["status" => 0, "msg" => "db insert failed"];
    }

    public function change(){
         if (!user_ins()->is_logged_in())
         	return ["status" => 0, "msg" => "id is required"];
         if (!rq("id"))
         	return ["status" => 0, "msg" => "id is required"];
         $answer = $this->find(rq("id"));
         if($answer->user_id != session("user_id"))
         	return ["status"=> 0, "msg"=> "permission denied"];

         $answer->content = rq("content");
         return $answer->save() ?
           ["status" => 1]:
           ["status" => 0, "msg" => "db update failed"];
    }

    public function read(){
    	if (!rq("id") && !rq("question_id"))
    		return ["status" => 0, "msg" => "id or question_id is required"];
    	if(rq("id")){
    		$answer = $this->find(rq("id"));
    		if(!$answer)
    			return ["status" => 0, "msg" => "answer not exists"];
    		return ["status" => 1, "data" => $answer];
    	}
    		
        if (!question_ins()->find(rq("question_id")))
        	return ["status" => 0, "msg" => "question not exists"];

        $answers = $this
          ->where("question_id", rq("question_id"));
    }

    public function vote(){
        if(!user_ins() -> is_logged_in())
            return ["status" => 0, "msg" => "login required"];
    }

    public function users(){
    	return $this
    	   ->belongsToMany("App\User")
    	   ->withPivot("vote")
    	   ->withTimestamps();

    }

}

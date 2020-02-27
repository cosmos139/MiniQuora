<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    public function add()
    {
    	if(!user_ins()->is_logged_in())
    	   return ["status" => 0, "msg" => "login required"];

    	if(!rq("title"))
    		return ["status" => 0, "msg" => "required title"];
    	$this->title=rq("title");
    	$this->user_id = session("user_id");
    	if(rq("desc")) 
    		$this->desc = rq("desc");
    	return $this->save() ?
    	  ["status" => 1, "id" => $this->id]:
    	  ["status" => 0, "msg" => "db insert failed"];
    }

    public function change() {
    	if(!user_ins()->is_logged_in())
    		return ["status" => 0, "msg" => "login required"];

    	if(!rq("id"))
    		return ["status" => 0, "msg" => "id is required"];

    	$question = $this->find(rq("id"));

    	if ($question)
    		return ["status" => 0, "msg" => "question not exists"];

    	if(($question->user_id) != session("user_id"))

    		return ["status" => 0, "msg" => "permission denied"];

    	if(rq("title"))
    		$question->title = rq("title");

    	if(rq("desc"))
    		$question->desc = rq("desc");

    	return $question->save()?
    	  ["status" => 1, "id" => $this->id]:
    	  ["status" => 0, "msg" => "db update failed"];

    }

    public function read()
    {
    	/*If there is the "id", return the line with the "id"*/
    	if(rq("id"))
    		return ["status" => 1, "data" => $this->find(rq("id"))];
    	/*limit condition*/

    	$limit = rq("limit") ?: 15;
    	$skip = (rq("page") ? rq("page") - 1 : 0) * $limit;
        
    	/*build query and return the collection of data */
    	$r = $this
    	  ->orderby("created_at")
    	  ->limit($limit)
    	  ->skip($skip)
    	  ->get(["id", "title", "desc", "user_id", "created_at", "updated_at"])
    	  ->keyBy("id");

    	return ["status" => 1, "data" => $r];
    }

    public function remove() {
    	/*Check if user log in */
    	if (!user_ins()-> is_logged_in())
    		return ["status" => 0, "msg" => "login required"];
    	/*Check if the passed paramater contains id*/
    	if (!rq("id"))
    		return ["status" => 0, "msg" => "id is required"];
    	/*Check the corresponding model for the passed id*/
    	$question = $this->find(rq("id"));
    	if (!$question) return ["status" => 0, "question not exists"];
    	/*Check if the question asked belong to the user*/
    	if (session("user_id") != $question->user_id)
    		return ["status" => 0, "msg" => "permission denied"];
    	return $question->delete() ?
    	  ["status" => 1]:
    	  ["status" => 0, "msg" => "db delete failed"];

    }
    
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    //Table name
    protected $table = 'posts'; // if you want to change the table name (by default if model is post table is posts )
    // Primary key
    public $primarykey = 'id'; //if you want to change primary key from the default id
    // Timestamps
    public $timestamps = true ; // if i dont want updated and created time in db I set it to false
    
    public function user(){
        return $this->belongsTO('App\User');
    }
}

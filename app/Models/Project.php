<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $table = 'project';

    protected $fillable = ['name', 'creatorID', 'estimatedCompletionDate'];

    public $timestamps = false;

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EndedSprintRecord extends Model
{
    protected $table = 'endedSprintRecord';

    protected $fillable = ['description','category','status','priority','estimation','estimationUnit','assignedTo','sprintID'];

    public $timestamps = false;
}

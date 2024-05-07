<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SprintBacklog extends Model
{
    protected $table = 'sprintBacklog';

    protected $fillable = ['productBacklogID', 'description','category','status','priority','estimation','estimationUnit','assignedTo','sprintID','sprintInvovled'];

    public $timestamps = false;
}

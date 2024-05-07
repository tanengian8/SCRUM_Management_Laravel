<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectMember extends Model
{
    use HasFactory;

    protected $table = 'projectMember';

    protected $fillable = ['projectID', 'userID', 'isSM', 'isPO', 'isTM', 'isCreator'];

    public $timestamps = false;

}

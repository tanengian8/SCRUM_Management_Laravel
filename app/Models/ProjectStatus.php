<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectStatus extends Model
{
    protected $table = 'projectStatus';

    protected $fillable = ['name', 'projectID'];

    public $timestamps = false;
}

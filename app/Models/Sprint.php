<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sprint extends Model
{
    protected $table = 'sprint';

    protected $fillable = ['id, projectID, description, startDate, endDate, status','estimatedDate','actualEffort'];

    public $timestamps = false;
}

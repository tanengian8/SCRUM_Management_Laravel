<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class productBacklog extends Model
{
    protected $table = 'productBacklog';

    protected $fillable = ['description', 'priority','status','projectID'];

    public $timestamps = false;
}

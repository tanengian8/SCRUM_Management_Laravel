<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanningPokerSequence extends Model
{
    use HasFactory;

    protected $table = 'planningPokerSequence';
    protected $fillable = ['projectID', 'sessionID','sequence'];
    public $timestamps = false;
}

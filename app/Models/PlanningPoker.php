<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanningPoker extends Model
{
    protected $table = 'planningPoker';

    protected $fillable = [
        'userID',
        'sessionID',
        'sprintBacklogID',
        'estimation',
        'sessionStatus',
        'sequenceID',
        'projectID'

    ];
}

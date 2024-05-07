<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\BacklogController;
use App\Http\Controllers\PlanningPokerController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//general routes that doesnt require token
Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);
Route::post('/logout', [UserController::class, 'logout']);

//Verifies if the token is valid
Route::middleware('checkJwtToken')->group(function () {

    //only Creator
    Route::middleware('creator')->group(function () {

        //project controller
        Route::post('/addUserToProject', [ProjectController::class, 'addUserToProject']);
        Route::delete('/deleteProjectMember', [ProjectController::class, 'deleteProjectMember']);
        Route::put('/updateProjectMemberRole', [ProjectController::class, 'updateProjectMemberRole']);
    });

    //only Product Owner
    Route::middleware('po')->group(function () {

        //Backlog Controller
        Route::post('/createProductBacklog', [BacklogController::class, 'createProductBacklog']);
        Route::put('/updateProductBacklog', [BacklogController::class, 'updateProductBacklog']);
        Route::delete('/deleteProductBacklog', [BacklogController::class, 'deleteProductBacklog']);
    });

    //only SCRUM Master
    Route::middleware('sm')->group(function () {

        //Project Controller
        Route::post('/addProjectStatus', [ProjectController::class, 'addProjectStatus']);
        Route::delete('/deleteProjectStatus', [ProjectController::class, 'deleteProjectStatus']);

        //Backlog Controller
        Route::post('/createSprint', [BacklogController::class, 'createSprint']);
        Route::post('/endSprint', [BacklogController::class, 'endSprint']);
        Route::put('updateSprintBacklogAssignedTo', [BacklogController::class, 'updateSprintBacklogAssignedTo']);
    });

    //only SCRUM Master, Team Member
    Route::middleware('smtm')->group(function () {

        //Backlog Controller
        Route::post('/createSprintBacklog', [BacklogController::class, 'createSprintBacklog']);
        Route::put('/updateSprintBacklog', [BacklogController::class, 'updateSprintBacklog']);
        Route::put('/updateSprintBacklogEstimation', [BacklogController::class, 'updateSprintBacklogEstimation']);
        Route::put('/updateSprintBacklogStatus', [BacklogController::class, 'updateSprintBacklogStatus']);
        Route::delete('/deleteSprintBacklog', [BacklogController::class, 'deleteSprintBacklog']);

        //Planning Poker Controller
        Route::post('/createSequenceNumber', [PlanningPokerController::class, 'createSequenceNumber']);
        Route::post('/resetSequenceNumber', [PlanningPokerController::class, 'resetSequenceNumber']);
        Route::post('/getPlanningPokerSession', [PlanningPokerController::class, 'getPlanningPokerSession']);
        Route::post('/revote', [PlanningPokerController::class, 'revote']);
        
    });

    //All Roles can access
    //Project Controller
    Route::post('/createProject', [ProjectController::class, 'createProject']);
    Route::get('/checkUserExistProject', [ProjectController::class, 'checkUserExistProject']);
    Route::get('/getProjectList', [ProjectController::class, 'getProjectList']);
    Route::get('/getUserProjectDetails', [ProjectController::class, 'getUserProjectDetails']);
    Route::get('/getProjectMembers', [ProjectController::class, 'getProjectMembers']);
    Route::get('/getProjectStatus', [ProjectController::class, 'getProjectStatus']);
    Route::get('/getCompletionDate', [ProjectController::class, 'getCompletionDate']);

    //User Controller
    Route::get('/checkUserExist', [UserController::class, 'checkUserExist']);

    //Backlog Controller
    Route::get('/getProductBacklog', [BacklogController::class, 'getProductBacklog']);
    Route::get('/getSprint', [BacklogController::class, 'getSprint']);


    //Planning Poker Controller
    Route::get('/getNotification', [PlanningPokerController::class, 'getNotification']);
    Route::get('getSessionDetails', [PlanningPokerController::class, 'getSessionDetails']);
    Route::get('/getNotes', [PlanningPokerController::class, 'getNotes']);
    Route::post('/addNotes', [PlanningPokerController::class, 'addNotes']);
    Route::post('/updatePlanningPokerEstimation', [PlanningPokerController::class, 'updatePlanningPokerEstimation']);
    Route::get('/getSequenceNumber', [PlanningPokerController::class, 'getSequenceNumber']);
});

<?php

namespace App\Repositories;

use App\Repositories\Contracts\PlanningPokerRepositoryContract;
use Illuminate\Support\Facades\Auth;
use App\Models\PlanningPoker;
use App\Models\SprintBacklog;
use App\Models\ProjectMember;
use App\Models\Note;
use App\Models\User;
use App\Models\ProductBacklog;
use App\Models\Sprint;
use App\Models\Project;
use App\Models\PlanningPokerSequence;


class PlanningPokerRepository implements PlanningPokerRepositoryContract
{
    //get the notification for each planning poker session
    public function getNotification($request)
    {
        // Get user id
        $user = Auth::user();
        $userID = $user->id;
        $projectID = $request->projectID;

        // Find the planning poker entries that the user is in for the given project
        $planningPoker = PlanningPoker::where('projectID', $projectID)->where('userID', $userID)->get();

        // If planning poker entries are found
        if (!$planningPoker->isEmpty()) {
            // Initialize an array to store all sprint backlog items
            $sprintBacklogs = [];

            foreach ($planningPoker as $poker) {
                // Find sprint backlog items related to each planning poker entry
                $sprintBacklogsForPoker = SprintBacklog::where('id', $poker->sprintBacklogID)->get();

                // Push each sprint backlog item to the array
                foreach ($sprintBacklogsForPoker as $sprint) {
                    $sprintBacklogs[] = $sprint;
                }
            }

            // Check if any sprint backlog items were found
            if (!empty($sprintBacklogs)) {
                return response()->json([
                    'notifications' => $planningPoker,
                    'sprintBacklog' => $sprintBacklogs
                ]);
            }
        }
        return response()->json(['message' => 'No planning poker found']);
    }


    //get the requested planning poker session id for the user
    public function getPlanningPokerSession($request)
    {
        // Get user id
        $user = Auth::user();
        $userID = $user->id;
        $projectID = $request->projectID;
        $sprintBacklogID = $request->sprintBacklogID;

        // Find the planning poker entries that the user is in for the given project
        $planningPoker = PlanningPoker::where('projectID', $projectID)
            ->where('userID', $userID)
            ->where('sprintBacklogID', $sprintBacklogID)
            ->first();

        //Case 1: Planning Poker session exist and current user is in the session
        // If planning poker entries are found
        if ($planningPoker) {
            return response()->json(['sessionID' => $planningPoker->sessionID]);
        }

        //if no planning poker entries are found
        //Case 2: User is a new member of the project but session is already created
        //check if user is a new member of the project, if so, create a new planning poker session for the user
        $planningPoker = PlanningPoker::where('projectID', $projectID)->where('sprintBacklogID', $sprintBacklogID)->first();

        // if planning poker session is found but user is not in the session, add the user to the session
        if ($planningPoker) {
            //create a new planning poker for current user with the same session ID
            $newPlanningPoker = new PlanningPoker();
            $newPlanningPoker->userID = $userID;
            $newPlanningPoker->sessionID = $planningPoker->sessionID;
            $newPlanningPoker->sprintBacklogID = $sprintBacklogID;
            $newPlanningPoker->projectID = $projectID;
            $newPlanningPoker->save();

            return response()->json(['sessionID' => $planningPoker->sessionID]);
        }

        //Case 3: Session is not created yet
        //create a new planning poker session
        //get all project members with the project ID
        $projectMembers = ProjectMember::where('projectID', $projectID)->get();

        //get the lagest session ID, then increment by 1
        $latestSessionID = PlanningPoker::max('sessionID');
        $newSessionID = $latestSessionID + 1;

        //create a new planning poker session (invite all the project members to the session)
        foreach ($projectMembers as $member) {
            $planningPoker = new PlanningPoker();
            $planningPoker->userID = $member->userID;
            $planningPoker->sessionID = $newSessionID;
            $planningPoker->sprintBacklogID = $sprintBacklogID;
            $planningPoker->projectID = $projectID;
            $planningPoker->save();
        }

        return response()->json(['sessionID' => $newSessionID]);
    }

    //get the planning poker session details for the user
    public function getSessionDetails($request)
    {
        // Get user id
        $user = Auth::user();
        $userID = $user->id;
        $projectID = $request->projectID;
        $sessionID = $request->sessionID;

        // Find the planning poker entries that the user is in for the given project
        $planningPoker = PlanningPoker::where('projectID', $projectID)->where('userID', $userID)->where('sessionID', $sessionID)->first();

        // If planning poker entries are found
        if ($planningPoker) {
            //get the sprint details as well then return back
            //error as if a planning poker session is tied to a sprint backlog item, it should exist
            $sprintBacklog = SprintBacklog::where('id', $planningPoker->sprintBacklogID)->firstOrFail();
            if ($sprintBacklog) {
                return response()->json(['sessionDetails' => $planningPoker, 'sprintDetails' => $sprintBacklog]);
            }
        }

        return response()->json(['message' => 'No planning poker found']);
    }

    //get all the notes written by all members for the planning poker session
    public function getNotes($request)
    {
        $sessionID = $request->sessionID;

        //find all the notes for the given session ID
        $notes = Note::where('sessionID', $sessionID)->get();

        if ($notes) {
            //find the userID for each note
            foreach ($notes as $note) {
                $user = User::where('id', $note->userID)->first();
                if ($user) {
                    $note->username = $user->username;
                } else {
                    //if null value just set the name "system"
                    $note->username = "System";
                }
            }
            return response()->json(['notes' => $notes], 200);
        }

        return response()->json(['message' => 'No notes found'], 200);
    }

    //add a note to the planning poker session
    public function addNotes($request)
    {
        $sessionID = $request->sessionID;
        $userID = Auth::user()->id;
        $description = $request->description;

        //create a new note
        $note = new Note();
        $note->sessionID = $sessionID;
        $note->userID = $userID;
        $note->description = $description;
        $note->save();

        if ($note) {
            return response()->json(['message' => 'Note added successfully'], 200);
        }

        return response()->json(['message' => 'Note could not be added'], 400);
    }

    //update the planning poker estimation chosen by the user
    public function updatePlanningPokerEstimation($request)
    {
        $sessionID = $request->sessionID;
        $estimation = $request->estimation;
        $projectID = $request->projectID;
        $sprintBacklogID = $request->sprintBacklogID;
        $user = Auth::user();

        //find the planning poker entry for the given session ID, project ID, user ID and sprint backlog ID
        $planningPoker = PlanningPoker::where('sessionID', $sessionID)
            ->where('projectID', $projectID)
            ->where('userID', $user->id)
            ->where('sprintBacklogID', $sprintBacklogID)
            ->first();

        if ($planningPoker) {
            //update the estimation
            $planningPoker->estimation = $estimation;
            $planningPoker->save();
        }

        //look if the estimation for the same session is filled by all the members
        $planningPoker = PlanningPoker::where('sessionID', $sessionID)->where('projectID', $projectID)->get();
        $allEstimationsFilled = true;

        foreach ($planningPoker as $poker) {
            if ($poker->estimation == null) {
                $allEstimationsFilled = false;
                break;
            }
        }

        //if all filled, check if all the estimations are the same
        if ($allEstimationsFilled) {
            $estimation = $planningPoker[0]->estimation;
            $allEstimationsSame = true;

            foreach ($planningPoker as $poker) {
                if ($poker->estimation != $estimation) {
                    $allEstimationsSame = false;
                    break;
                }
            }

            //if all the estimations are the same, update the sprint backlog item with the estimation
            if ($allEstimationsSame) {
                //end the session
                foreach ($planningPoker as $poker) {
                    $poker->sessionStatus = true;
                    $poker->save();
                }

                //append a note to the session saying the session has ended, and the agreed estimation
                $note = new Note();
                $note->sessionID = $sessionID;
                $note->description = "Session has ended, The estimation is " . $estimation;
                $note->save();

                //update the sprint backlog item with the estimation
                // Update the sprint backlog estimation
                $sprintBacklog = SprintBacklog::where('id', $sprintBacklogID)->first();
                $sprintBacklog->estimation = $request->estimation;
                $sprintBacklog->save();

                // Algorithm to calculate the estimated completion date as the estimation has been updated
                $sprints = Sprint::where('projectID', $projectID)
                    ->where('status', 'Completed')
                    ->get();

                //get the total actual effort and total completed estimation
                $totalActualEffort = 0;
                $totalCompletedEstimation = 0;
                $startDate = null;
                $actualEffort = 0;
                $averageEffort = 0;

                foreach ($sprints as $sprint) {
                    //For each sprint, get the total actual effort and total completed estimation
                    $totalActualEffort += $sprint->actualEffort;
                    $totalCompletedEstimation += $sprint->completedEstimation;

                    // If last index, get the latest sprint start date
                    if ($sprint === $sprints->last()) {
                        $startDate = $sprint->startDate;
                        $actualEffort = $sprint->actualEffort;
                    }
                }

                //if total actual effort and total completed estimation is not 0, calculate the average effort
                if ($totalCompletedEstimation !== 0 && $totalActualEffort !== 0) {
                    $averageEffort =  $totalCompletedEstimation / $totalActualEffort;
                }

                //if averageEffort exists (means user has completed at least one sprint), calculate the estimated completion date
                if ($averageEffort !== 0) {
                    // Get the latest sprint start date and add up the actual effort to get the actual end date
                    $actualCompletionDate = date('Y-m-d', strtotime($startDate . ' + ' . $actualEffort . ' days'));

                    // Find the remaining backlog and get the total remaining estimation, all statuses except Completed
                    // Find all product backlog project IDs
                    $productBacklogs = ProductBacklog::where('projectID', $projectID)->get();

                    //check if there is existing sprint backlog item with status not completed and has estimation point 
                    $totalRemainingEstimation = 0;
                    foreach ($productBacklogs as $product) {
                        $sprintBacklogs = SprintBacklog::where('productBacklogID', $product->id)
                            ->where('status', '!=', 'Completed')
                            ->get();
                        foreach ($sprintBacklogs as $sprintBacklog) {
                            if ($sprintBacklog->estimation !== 0) {
                                $totalRemainingEstimation += $sprintBacklog->estimation;
                            }
                        }
                    }

                    // New estimated completion date
                    //round up the estimated effort as days cannot be in decimal
                    if ($totalRemainingEstimation !== 0) {
                        $estimatedEffort = ceil($totalRemainingEstimation / $averageEffort);
                        $estimatedDate = date('Y-m-d', strtotime($actualCompletionDate . ' + ' . $estimatedEffort . ' days'));

                        // Update the project estimated completion date
                        $project = Project::where('id', $projectID)->first();
                        $project->estimatedCompletionDate = $estimatedDate;
                        $project->save();
                    }
                }
                return response()->json(['message' => 'Estimation updated successfully'], 200);
            }

            //if not the same, append a note to session saying estimations are not the same, a revote will be initiated
            $note = new Note();
            $note->sessionID = $sessionID;
            $note->description = "Estimations are not the same, a revote will be initiated";
            $note->save();

            //clear all the estimations in the same session
            foreach ($planningPoker as $poker) {
                $poker->estimation = null;
                $poker->save();
            }

            return response()->json(['message' => 'Estimations are not the same, a revote will be initiated'], 200);
        }

        return response()->json(['message' => 'Estimation updated successfully'], 200);
    }


    //reinitiate the planning poker session
    public function revote($request)
    {
        $sessionID = $request->sessionID;
        $projectID = $request->projectID;

        //find the planning poker entry for the given session ID, project ID
        $planningPoker = PlanningPoker::where('sessionID', $sessionID)->where('projectID', $projectID)->get();

        //for each planning poker entry, set the estimation to null, and set the session status to false
        foreach ($planningPoker as $poker) {
            $poker->estimation = null;
            $poker->sessionStatus = false;
            $poker->save();
        }

        //append a note to the session saying a revote has been initiated
        $note = new Note();
        $note->sessionID = $sessionID;
        $note->description = "A revote has been initiated by " . Auth::user()->username . ".";
        $note->save();

        if ($note) {
            return response()->json(['message' => 'Revoted successfully'], 200);
        }

        return response()->json(['message' => 'Revote could not be initiated'], 400);
    }

    //get the customized sequence number for the planning poker session
    public function getSequenceNumber($request)
    {
        $projectID = $request->projectID;
        $sessionID = $request->sessionID;

        $sequence = PlanningPokerSequence::where('projectID', $projectID)->where('sessionID', $sessionID)->first();

        if ($sequence) {
            return response()->json(['sequenceNumber' => $sequence->sequence], 200);
        }

        return response()->json(['message' => 'No sequence number found'], 200);
    }

    //create a new customized sequence number for the planning poker session
    public function createSequenceNumber($request)
    {
        $projectID = $request->projectID;
        $sessionID = $request->sessionID;

        //find if the sequence number with the same project ID and session ID exists
        $sequence = PlanningPokerSequence::where('projectID', $projectID)->where('sessionID', $sessionID)->first();

        //if exists, update the sequence number as we need to avoid creating a new sequence number ID for the same session
        if ($sequence) {
            $sequence->sequence = $request->sequence;
            $sequence->save();
        } else {

            $sequence = new PlanningPokerSequence();
            $sequence->projectID = $projectID;
            $sequence->sessionID = $sessionID;
            $sequence->sequence = $request->sequence;
            $sequence->save();
        }
        //add a new note saying sequence number has been changed, revote will be initiated
        $note = new Note();
        $note->sessionID = $sessionID;
        $note->description = "Sequence number has been changed, a revote will be initiated.";
        $note->save();

        //find the planning poker entry for the given session ID, project ID
        $planningPoker = PlanningPoker::where('sessionID', $sessionID)->where('projectID', $projectID)->get();

        //for each planning poker entry, set the estimation to null, and set the session status to false
        foreach ($planningPoker as $poker) {
            $poker->estimation = null;
            $poker->sessionStatus = false;
            $poker->save();
        }

        if ($sequence && $note && $planningPoker) {
            return response()->json(['message' => 'Sequence number updated successfully'], 200);
        }

        return response()->json(['message' => 'Sequence number could not be updated'], 400);
    }

    //reset the sequence numbet to derfault for the planning poker session
    //we delete the sequence number as on frontend if no sequence number is found, it will be set to default
    public function resetSequenceNumber($request)
    {
        $projectID = $request->projectID;
        $sessionID = $request->sessionID;

        //find if the sequence number with the same project ID and session ID exists
        $sequence = PlanningPokerSequence::where('projectID', $projectID)->where('sessionID', $sessionID)->first();

        //add a new note saying sequence number has been changed, revote will be initiated
        $note = new Note();
        $note->sessionID = $sessionID;
        $note->description = "Sequence number has been changed, a revote will be initiated.";
        $note->save();

        //find the planning poker entry for the given session ID, project ID
        $planningPoker = PlanningPoker::where('sessionID', $sessionID)->where('projectID', $projectID)->get();

        //for each planning poker entry, set the estimation to null, and set the session status to false
        foreach ($planningPoker as $poker) {
            $poker->estimation = null;
            $poker->sessionStatus = false;
            $poker->save();
        }

        //if no sequence number found, return a success message
        if (!$sequence) {
            return response()->json(['message' => 'No sequence number found'], 200);
        }

        //delete the sequence number
        $sequence->deleteOrFail();


        //if deleted successfully, return a success message
        if ($note && $planningPoker) {
            return response()->json(['message' => 'Sequence number reset successfully'], 200);
        }

        return response()->json(['message' => 'Sequence number could not be reset'], 400);
    }
}

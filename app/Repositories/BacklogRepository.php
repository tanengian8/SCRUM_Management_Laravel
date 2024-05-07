<?php

namespace App\Repositories;

use App\Repositories\Contracts\BacklogRepositoryContract;
use App\Models\ProductBacklog;
use App\Models\SprintBacklog;
use App\Models\Sprint;
use App\Models\EndedSprintRecord;
use App\Models\Project;
use App\Models\PlanningPoker;

class BacklogRepository implements BacklogRepositoryContract
{
    //get product backlog function
    public function getProductBacklog($request)
    {
        $projectID = $request->projectID;

        //find the product backlog
        $productBacklog = ProductBacklog::where('projectID', $projectID)->get();

        //find the sprint backlog associated with the product backlog
        $sprintBacklog = [];

        if ($productBacklog) {
            foreach ($productBacklog as $product) {
                $item = sprintBacklog::where('productBacklogID', $product->id)->get();
                if (!$item->isEmpty()) {
                    array_push($sprintBacklog, $item);
                }
            }

            //if there is sprint backlog, return both product and sprint backlog
            if (count($sprintBacklog) > 0) {
                return response()->json(['productBacklog' => $productBacklog, 'sprintBacklog' => $sprintBacklog], 200);
            }

            //if there is no sprint backlog, return only product backlog
            return response()->json(['productBacklog' => $productBacklog], 200);
        }
        return response()->json(['message' => 'No product backlog found'], 200);
    }

    //create product backlog function
    public function createProductBacklog($request)
    {
        $projectID = $request->projectID;

        //create the product backlog
        $newProductBacklog = new ProductBacklog();
        $newProductBacklog->projectID = $projectID;
        $newProductBacklog->description = $request->description;
        $newProductBacklog->priority = $request->priority;
        $newProductBacklog->save();

        //if product backlog is created successfully, return success message
        if ($newProductBacklog) {
            return response()->json(['message' => 'Product backlog created successfully'], 200);
        }
        return response()->json(['message' => 'Failed to create product backlog'], 400);
    }

    //update product backlog function
    public function updateProductBacklog($request)
    {
        $productBacklogID = $request->productBacklogID;
        $projectID = $request->projectID;

        //update the product backlog
        $productBacklog = ProductBacklog::where('id', $productBacklogID)->where('projectID', $projectID)->first();
        $productBacklog->description = $request->description;
        $productBacklog->priority = $request->priority;
        $productBacklog->status = $request->status;
        $productBacklog->save();

        if ($productBacklog) {
            return response()->json(['message' => 'Product backlog updated successfully'], 200);
        }
        return response()->json(['message' => 'Failed to update product backlog'], 400);
    }

    //delete product backlog function
    public function deleteProductBacklog($request)
    {
        $productBacklogID = $request->productBacklogID;
        $projectID = $request->projectID;

        //delete the sprint backlog associated with the product backlog
        $sprintBacklog = SprintBacklog::where('productBacklogID', $productBacklogID)->get();
        if (!$sprintBacklog->isEmpty()) {
            foreach ($sprintBacklog as $sprint) {
                //delete the planning poker session associated with the sprint backlog
                $planningPoker = PlanningPoker::where('sprintBacklogID', $sprint->id)->get();
                if (!$planningPoker->isEmpty()) {
                    foreach ($planningPoker as $poker) {
                        $poker->deleteOrFail();
                    }
                }
                $sprint->deleteOrFail();
            }
        }

        //delete the product backlog
        $productBacklog = ProductBacklog::where('id', $productBacklogID)->where('projectID', $projectID)->first();
        $productBacklog->deleteOrFail();

        return response()->json(['message' => 'Product backlog deleted successfully'], 200);
    }

    //create sprint backlog function
    public function createSprintBacklog($request)
    {
        $productBacklogID = $request->productBacklogID;

        //create the sprint backlog
        $newSprintBacklog = new SprintBacklog();
        $newSprintBacklog->productBacklogID = $productBacklogID;
        $newSprintBacklog->description = $request->description;
        $newSprintBacklog->priority = $request->priority;
        $newSprintBacklog->category = $request->category;
        $newSprintBacklog->assignedTo = $request->assignedTo;
        $newSprintBacklog->save();

        if ($newSprintBacklog) {
            return response()->json(['message' => 'Sprint backlog created successfully'], 200);
        }

        return response()->json(['message' => 'Failed to create sprint backlog'], 400);
    }

    //update sprint backlog function
    public function updateSprintBacklog($request)
    {
        $sprintBacklogID = $request->sprintBacklogID;
        $productBacklogID = $request->productBacklogID;

        //update the sprint backlog
        $sprintBacklog = SprintBacklog::where('id', $sprintBacklogID)->where('productBacklogID', $productBacklogID)->first();
        $sprintBacklog->description = $request->description;
        $sprintBacklog->priority = $request->priority;
        $sprintBacklog->category = $request->category;
        $sprintBacklog->status = $request->status;
        $sprintBacklog->assignedTo = $request->assignedTo;
        $sprintBacklog->save();

        if ($sprintBacklog) {
            return response()->json(['message' => 'Sprint backlog updated successfully'], 200);
        }

        return response()->json(['message' => 'Failed to update sprint backlog'], 400);
    }

    //delete sprint backlog function
    public function deleteSprintBacklog($request)
    {
        $sprintBacklogID = $request->sprintBacklogID;
        $productBacklogID = $request->productBacklogID;

        //find the planning poker session associated with the sprint backlog
        $planningPoker = PlanningPoker::where('sprintBacklogID', $sprintBacklogID)->get();
        if (!$planningPoker->isEmpty()) {
            foreach ($planningPoker as $poker) {
                $poker->deleteOrFail();
            }
        }
        //delete the sprint backlog
        $sprintBacklog = SprintBacklog::where('id', $sprintBacklogID)->where('productBacklogID', $productBacklogID)->first();
        $sprintBacklog->deleteOrFail();

        return response()->json(['message' => 'Sprint backlog deleted successfully'], 200);
    }

    //update sprint backlog estimation function
    public function updateSprintBacklogEstimation($request)
    {
        $sprintBacklogID = $request->sprintBacklogID;
        $productBacklogID = $request->productBacklogID;
        $projectID = $request->projectID;

        // Update the sprint backlog estimation
        $sprintBacklog = SprintBacklog::where('id', $sprintBacklogID)
            ->where('productBacklogID', $productBacklogID)
            ->first();
        $sprintBacklog->estimation = $request->estimation;
        $sprintBacklog->estimationUnit = $request->estimationUnit;
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

        //return start date, actual effort, average effort and actual completiontime
        return response()->json(['message' => 'Sprint backlog estimation updated successfully'], 200);
    }

    //create sprint function
    public function createSprint($request)
    {
        $projectID = $request->projectID;
        $totalSprint = Sprint::where('projectID', $projectID)->get();
        $totalSprintCount = count($totalSprint);
        //create the sprint
        $newSprint = new Sprint();
        //get the total number of sprint
        $newSprint->projectID = $projectID;
        $newSprint->description = "Sprint " . strval($totalSprintCount + 1);
        $newSprint->startDate = $request->startDate;
        $newSprint->endDate = $request->endDate;
        //get selected item
        $selectedItem = $request->selectedItems;
        $totalNewSprintEffort = 0;

        //if not first sprint, we need to estimate the sprint completion date
        if ($totalSprintCount > 0) {
            $totalTaskCompleted = 0;
            $totalActualEffort = 0;

            foreach ($totalSprint as $sprint) {
                $totalTaskCompleted += $sprint->completedEstimation;
                $totalActualEffort += $sprint->actualEffort;
            }

            foreach ($selectedItem as $item) {
                $totalNewSprintEffort += $item['estimation'];
            }

            if ($totalTaskCompleted > 0) {
                //round up to nearest integer
                $estimatedRatio = $totalTaskCompleted / $totalActualEffort;

                //round up the estimated effort as days cannot be in decimal
                $estimatedEffort = ceil($totalNewSprintEffort / $estimatedRatio);


                //assuming average effort is number of days, add up to the start date and set it as estimatedDate
                $newSprint->estimatedDate = date('Y-m-d', strtotime($request->startDate . ' + ' . $estimatedEffort . ' days'));
            }
        }

        $newSprint->save();

        //loop through selected item to get their id, and update their status with the sprint id
        foreach ($selectedItem as $item) {
            $sprintBacklog = SprintBacklog::where('id', $item['id'])->where('productBacklogID', $item['productBacklogID'])->first();
            $sprintBacklog->sprintID = $newSprint->id;
            $sprintBacklog->save();
        }

        if ($newSprint) {
            return response()->json(['message' => 'Sprint created successfully'], 200);
        }

        return response()->json(['message' => 'Failed to create sprint'], 400);
    }

    //to get all the sprint ifnroamtion
    public function getSprint($request)
    {
        $projectID = $request->projectID;

        //find the sprint
        $sprints = Sprint::where('projectID', $projectID)->get();

        //find the sprint backlog associated with the sprint
        $sprintBacklog = [];

        if (!$sprints->isEmpty()) {
            foreach ($sprints as $sprint) {
                if ($sprint->status == 'Completed') {
                    //if the sprint ended, get the sprint information from the ended sprint record table
                    $item = EndedSprintRecord::where('sprintID', $sprint->id)->get();
                    if (!$item->isEmpty()) {
                        array_push($sprintBacklog, $item);
                    }
                } else {
                    //if sprint is not ended, get the sprint information from the sprint backlog table
                    $item = sprintBacklog::where('sprintID', $sprint->id)->get();
                    if (!$item->isEmpty()) {
                        array_push($sprintBacklog, $item);
                    }
                }
            }

            if (count($sprintBacklog) > 0) {
                return response()->json(['sprint' => $sprints, 'sprintBacklog' => $sprintBacklog], 200);
            }
            return response()->json(['sprint' => $sprint], 200);
        }
        return response()->json(['message' => 'No sprint found'], 200);
    }

    //end sprint function
    public function endSprint($request)
    {
        $sprintID = $request->sprintID;
        $projectID = $request->projectID;
        $actualEffort = $request->actualEffort;

        //update the sprint status
        $sprint = Sprint::where('id', $sprintID)->where('projectID', $projectID)->first();
        $sprint->status = 'Completed';
        $sprint->actualEffort = $actualEffort;
        $sprint->completedEstimation = $request->completedEstimation;
        $sprint->remainingEstimation = $request->remainingEstimation;
        $sprint->nullCompleted = $request->nullCompleted;
        $sprint->nullRemaining = $request->nullRemaining;
        $sprint->averageEffort = $request->averageEffort;
        $estimatedDate = $sprint->estimatedDate;

        //sprint start date
        $startDate = $sprint->startDate;
        $actualCompletionDate = date('Y-m-d', strtotime($startDate . ' + ' . $actualEffort . ' days'));
        //save the finished remaining effort, and completed effort and the average 

        $sprint->save();

        //update the sprint backlog status
        $sprintBacklog = SprintBacklog::where('sprintID', $sprintID)->get();
        if (!$sprintBacklog->isEmpty()) {
            foreach ($sprintBacklog as $sprint) {
                //save the sprint record into the ended sprint record table
                $endedSprintRecord = new EndedSprintRecord();
                $endedSprintRecord->description = $sprint->description;
                $endedSprintRecord->category = $sprint->category;
                $endedSprintRecord->status = $sprint->status;
                $endedSprintRecord->priority = $sprint->priority;
                $endedSprintRecord->estimation = $sprint->estimation;
                $endedSprintRecord->estimationUnit = $sprint->estimationUnit;
                $endedSprintRecord->assignedTo = $sprint->assignedTo;
                $endedSprintRecord->sprintID = $sprintID;
                $endedSprintRecord->save();
                $sprint->sprintID = null;
                if ($sprint->sprintInvovled !== null) {
                    // Append comma and convert sprintID to string
                    $sprint->sprintInvovled .= ',' . strval($sprintID);
                } else {
                    // If null, simply assign $sprintID as string
                    $sprint->sprintInvovled = strval($sprintID);
                }
                $sprint->save();
            }
        }

        //recalculate the estimated completion date
        $sprintBacklogs = SprintBacklog::where('sprintID', $sprintID)->get();
        $totalIncompleteEstimation = 0;

        //if status != completed, += estimation
        foreach ($sprintBacklogs as $sprintBacklog) {
            if ($sprintBacklog->status !== 'Completed') {
                $totalIncompleteEstimation += $sprintBacklog->estimation;
            }
        }

        if ($totalIncompleteEstimation !== 0) {
            $project = Project::where('id', $projectID)->first();
            //replace the estimated copletion date with the estimated date as no more sprint backlog estimation is left
            $project->estimatedCompletionDate = $estimatedDate;
            $project->save();
        } else {

            // Update the overall estimated completion date
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
                $totalActualEffort += $sprint->actualEffort;
                $totalCompletedEstimation += $sprint->completedEstimation;

                // If last index, get the latest sprint start date
                if ($sprint === $sprints->last()) {
                    $startDate = $sprint->startDate;
                    $actualEffort = $sprint->actualEffort;
                }
            }

            if ($totalCompletedEstimation !== 0 && $totalActualEffort !== 0) {
                $averageEffort =  $totalCompletedEstimation / $totalActualEffort;
            }

            if ($averageEffort !== 0) {
                // Get the latest sprint start date and add up the actual effort to get the actual end date
                $actualCompletionDate = date('Y-m-d', strtotime($startDate . ' + ' . $actualEffort . ' days'));

                // Find the remaining backlog and get the total remaining estimation, all statuses except Completed
                // Find all product backlog project IDs
                $productBacklogs = ProductBacklog::where('projectID', $projectID)->get();

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
                $estimatedEffort = ceil($totalRemainingEstimation / $averageEffort);
                $estimatedDate = date('Y-m-d', strtotime($actualCompletionDate . ' + ' . $estimatedEffort . ' days'));

                // Update the project estimated completion date
                $project = Project::where('id', $projectID)->first();
                $project->estimatedCompletionDate = $estimatedDate;
                $project->save();
            }
        }

        return response()->json(['message' => 'Sprint ended successfully'], 200);
    }

    //update sprint backlog status function
    public function updateSprintBacklogStatus($request)
    {
        $sprintBacklogID = $request->sprintBacklogID;
        $status = $request->status;

        //update the sprint backlog status
        $sprintBacklog = SprintBacklog::where('id', $sprintBacklogID)->first();
        $sprintBacklog->status = $status;
        $sprintBacklog->save();

        if ($sprintBacklog) {
            return response()->json(['message' => 'Sprint backlog status updated successfully'], 200);
        }

        return response()->json(['message' => 'Failed to update sprint backlog status'], 400);
    }

    public function updateSprintBacklogAssignedTo($request)
    {
        $sprintBacklogID = $request->sprintBacklogID;
        $assignedTo = $request->assignedTo;

        //update the sprint backlog assigned to
        $sprintBacklog = SprintBacklog::where('id', $sprintBacklogID)->first();
        $sprintBacklog->assignedTo = $assignedTo;
        $sprintBacklog->save();

        if ($sprintBacklog) {
            return response()->json(['message' => 'Sprint backlog assigned to updated successfully'], 200);
        }

        return response()->json(['message' => 'Failed to update sprint backlog assigned to'], 400);
    }
}

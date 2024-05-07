<?php

namespace App\Repositories;

use App\Repositories\Contracts\ProjectRepositoryContract;
use App\Models\Project;
use App\Models\ProjectMember;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\ProjectStatus;
use App\Models\SprintBacklog;




class ProjectRepository implements ProjectRepositoryContract
{
    //create new project function
    public function createProject($request)
    {
        //validate if project name is unique using validate function
        $request->validate([
            'projectName' => 'required'
        ], [
            'projectName.required' => 'Project name is required',
        ]);

        //create new project
        $project = new Project;
        $project->name = $request->projectName;
        $user = Auth::user();
        $userID = $user->id;
        $project->creatorID = $userID;
        $project->save();

        //add the current user to project member table as creator
        $projectMember = new ProjectMember;
        $projectMember->projectID = $project->id;
        $projectMember->userID = $userID;
        $projectMember->isCreator = 1;
        $projectMember->save();

        //add the invited users to the project
        if ($request->invitedUsers) {
            foreach ($request->invitedUsers as $invitedUser) {
                $projectMember = new ProjectMember;
                $projectMember->projectID = $project->id;
                $projectMember->userID = $invitedUser['id'];
                //if invitedUser role == Team Member is TM = 1, ==SCRUM Master, isSM = 1, ==Product Owner, isPO = 1
                if ($invitedUser['role'] == 'Team Member') {
                    $projectMember->isTM = 1;
                } elseif ($invitedUser['role'] == 'SCRUM Master') {
                    $projectMember->isSM = 1;
                } elseif ($invitedUser['role'] == 'Product Owner') {
                    $projectMember->isPO = 1;
                }

                $projectMember->save();
            }
        }

        //if project is created successfully, return success message
        if ($project) {
            return response()->json(['message' => 'Project created successfully'], 201);
        }
        return response()->json(['message' => 'Failed to create project'], 400);
    }

    //check if user already exist in the current project
    public function checkUserExistProject($request)
    {
        $userID = $request->userID;
        $projectID = $request->projectID;

        //check if user is a member of the project
        $projectMember = ProjectMember::where('projectID', $projectID)->where('userID', $userID)->first();

        if ($projectMember) {
            //return 
            return response()->json(['exist' => 'User is a member of the project'], 200);
        }
        return response()->json(['notExist' => 'User is not a member of the project'], 200);
    }

    //add user to project
    public function addUserToProject($request)
    {
        //add the invited users to the project
        if ($request->invitedUsers) {
            foreach ($request->invitedUsers as $invitedUser) {
                $projectMember = new ProjectMember;
                $projectMember->projectID = $request->projectID;
                $projectMember->userID = $invitedUser['id'];
                //if invitedUser role == Team Member is TM = 1, ==SCRUM Master, isSM = 1, ==Product Owner, isPO = 1
                if ($invitedUser['role'] == 'Team Member') {
                    $projectMember->isTM = 1;
                } elseif ($invitedUser['role'] == 'SCRUM Master') {
                    $projectMember->isSM = 1;
                } elseif ($invitedUser['role'] == 'Product Owner') {
                    $projectMember->isPO = 1;
                }

                $projectMember->save();
            }
            return response()->json(['message' => 'User added to project successfully'], 201);
        }

        return response()->json(['message' => 'Failed to add user to project'], 400);
    }

    //get all projects that the user is involved in
    public function getProjectList($request)
    {
        // Get the authenticated user
        $user = Auth::user();
        $userID = $user->id;

        // Get all projects that the user is involved in
        $projectInvolved = ProjectMember::where('userID', $userID)->get();

        $projects = [];

        // Retrieve project details for each project involved
        foreach ($projectInvolved as $projectMember) {
            $project = Project::find($projectMember->projectID);

            // Check if project exists before pushing it to the array
            if ($project) {
                array_push($projects, $project);
            }
        }

        // Return the projects
        if (!empty($projects)) {
            return response()->json(['projects' => $projects], 200);
        }
        return response()->json(['message' => 'No projects found'], 200);
    }

    //after user choose a project, get the project details
    public function getUserProjectDetails($request)
    {
        $user = Auth::user();
        $projectID = $request->projectID;

        //get project details
        $userDetails = ProjectMember::where('userID', $user->id)->where('projectID', $projectID)->first();

        // Define the custom claims to be included in the token payload
        $customClaims = [
            'userID' => $userDetails->userID, 'projectID' => $projectID, 'isSM' => $userDetails->isSM,
            'isPO' => $userDetails->isPO, 'isTM' => $userDetails->isTM, 'isCreator' => $userDetails->isCreator
        ];

        // Generate a JWT token with custom payload
        //get current Auth user

        $token = JWTAuth::claims($customClaims)->fromUser($user);

        if ($userDetails && $token) {
            return response()->json(['user' => $userDetails, 'token' => $token], 200);
        }
        return response()->json(['message' => 'No detail found'], 200);
    }

    //get all project members in the current project
    public function getProjectMembers($request)
    {
        $projectID = $request->projectID;

        //get all project members
        $projectMembers = ProjectMember::where('projectID', $projectID)->get();
        $projectMemberDetails = [];

        if ($projectMembers) {
            foreach ($projectMembers as $projectMember) {
                $user = User::where('id', $projectMember->userID)->first();
                array_push($projectMemberDetails, $user);
            }
            return response()->json(['projectMembers' => $projectMembers, 'projectMemberDetails' => $projectMemberDetails], 200);
        }
        return response()->json(['message' => 'No project members found'], 200);
    }

    //update project member role function
    public function updateProjectMemberRole($request)
    {
        $projectID = $request->projectID;
        $userID = $request->userID;
        $isSM = $request->isSM;
        $isPO = $request->isPO;
        $isTM = $request->isTM;

        //update project member role
        $projectMember = ProjectMember::where('projectID', $projectID)->where('userID', $userID)->first();

        //update all role accordng to the request
        //else statement is required as user may want to remove the role
        if ($isSM) {
            $projectMember->isSM = 1;
        } else {
            $projectMember->isSM = 0;
        }

        if ($isPO) {
            $projectMember->isPO = 1;
        } else {
            $projectMember->isPO = 0;
        }

        if ($isTM) {
            $projectMember->isTM = 1;
        } else {
            $projectMember->isTM = 0;
        }

        $projectMember->save();

        if ($projectMember) {
            return response()->json(['message' => 'Project member role updated successfully'], 200);
        }
        return response()->json(['message' => 'Failed to update project member role'], 400);
    }

    //remove project member function
    public function deleteProjectMember($request)
    {
        $projectID = $request->projectID;
        $userID = $request->userID;

        //delete project member
        $projectMember = ProjectMember::where('projectID', $projectID)->where('userID', $userID)->first();
        $projectMember->deleteOrFail();

        return response()->json(['message' => 'Project member deleted successfully'], 200);
    }

    //get the customize project status for the current project
    public function getProjectStatus($request)
    {
        $projectID = $request->projectID;

        //get project status
        $projectStatus = ProjectStatus::where('projectID', $projectID)->get();

        if ($projectStatus) {
            return response()->json(['projectStatus' => $projectStatus], 200);
        }
        return response()->json(['message' => 'No project status found'], 200);
    }

    public function addProjectStatus($request)
    {
        $projectID = $request->projectID;
        $status = $request->status;

        //add project status
        $projectStatus = new ProjectStatus;
        $projectStatus->projectID = $projectID;
        $projectStatus->name = $status;
        $projectStatus->save();

        if ($projectStatus) {
            return response()->json(['message' => 'Project status added successfully'], 200);
        }
        return response()->json(['message' => 'Failed to add project status'], 400);
    }

    public function deleteProjectStatus($request)
    {
        $projectID = $request->projectID;
        $status = $request->status;

        // Delete project status
        $deleteStatus = ProjectStatus::where('projectID', $projectID)->where('name', $status)->firstOrFail();
        $deleteStatus->deleteOrFail();

        // Find sprintBacklog items that have the status to be deleted and replace it with "To Do"
        $updatedSprintBacklogs = SprintBacklog::where('status', $status)->get();
        foreach ($updatedSprintBacklogs as $sprint) {
            $sprint->status = "To Do";
            $sprint->save();
        }

        return response()->json(['message' => 'Project status deleted successfully'], 200);
    }

    //get the estimated project completion date
    public function getCompletionDate($request)
    {
        $projectID = $request->projectID;

        //get project completion date
        $project = Project::where('id', $projectID)->first();

        if ($project) {
            return response()->json(['completionDate' => $project->estimatedCompletionDate], 200);
        }
        return response()->json(['message' => 'No completion date found'], 200);
    }
}

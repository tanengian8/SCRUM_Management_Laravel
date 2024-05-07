<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProjectService;

class ProjectController extends Controller
{
    private $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }
    
    public function createProject(Request $request)
    {
        return $this->projectService->createProject($request);
    }

    public function checkUserExistProject(Request $request)
    {
        return $this->projectService->checkUserExistProject($request);
    }

    public function addUserToProject(Request $request)
    {
        return $this->projectService->addUserToProject($request);
    }

    public function getProjectList(Request $request)
    {
        return $this->projectService->getProjectList($request);
    }

    public function getUserProjectDetails(Request $request)
    {
        return $this->projectService->getUserProjectDetails($request);
    }

    public function getProjectMembers(Request $request)
    {
        return $this->projectService->getProjectMembers($request);
    }

    public function getProjectStatus(Request $request)
    {
        return $this->projectService->getProjectStatus($request);
    }

    public function deleteProjectMember(Request $request)
    {
        return $this->projectService->deleteProjectMember($request);
    }

    public function updateProjectMemberRole(Request $request)
    {
        return $this->projectService->updateProjectMemberRole($request);
    }

    public function addProjectStatus(Request $request)
    {
        return $this->projectService->addProjectStatus($request);
    }

    public function deleteProjectStatus(Request $request)
    {
        return $this->projectService->deleteProjectStatus($request);
    }

    public function getCompletionDate(Request $request)
    {
        return $this->projectService->getCompletionDate($request);
    }
}

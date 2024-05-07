<?php

namespace App\Services;

use App\Repositories\Contracts\ProjectRepositoryContract;

class ProjectService
{
    
    private $projectRepository;
    
    public function __construct(ProjectRepositoryContract $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function createProject($request)
    {
        return $this->projectRepository->createProject($request);
    }

    public function checkUserExistProject($request)
    {
        return $this->projectRepository->checkUserExistProject($request);
    }
 
    public function addUserToProject($request)
    {
        return $this->projectRepository->addUserToProject($request);
    }

    public function getProjectList($request)
    {
        return $this->projectRepository->getProjectList($request);
    }

    public function getUserProjectDetails($request)
    {
        return $this->projectRepository->getUserProjectDetails($request);
    }

    public function getProjectMembers($request)
    {
        return $this->projectRepository->getProjectMembers($request);
    }

    public function getProjectStatus($request)
    {
        return $this->projectRepository->getProjectStatus($request);
    }

    public function deleteProjectMember($request)
    {
        return $this->projectRepository->deleteProjectMember($request);
    }

    public function updateProjectMemberRole($request)
    {
        return $this->projectRepository->updateProjectMemberRole($request);
    }

    public function addProjectStatus($request)
    {
        return $this->projectRepository->addProjectStatus($request);
    }

    public function deleteProjectStatus($request)
    {
        return $this->projectRepository->deleteProjectStatus($request);
    }

    public function getCompletionDate($request)
    {
        return $this->projectRepository->getCompletionDate($request);
    }

    
}
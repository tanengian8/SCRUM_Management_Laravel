<?php

namespace App\Repositories\Contracts;


interface ProjectRepositoryContract
{
    public function createProject($request);
    public function checkUserExistProject($request);
    public function addUserToProject($request);
    public function getProjectList($request);
    public function getUserProjectDetails($request);
    public function getProjectMembers($request);
    public function getProjectStatus($request);
    public function deleteProjectMember($request);
    public function updateProjectMemberRole($request);
    public function addProjectStatus($request);
    public function deleteProjectStatus($request);
    public function getCompletionDate($request);
}
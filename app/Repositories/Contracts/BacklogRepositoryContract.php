<?php

namespace App\Repositories\Contracts;


interface BacklogRepositoryContract
{
    public function getProductBacklog($request);
    public function createProductBacklog($request);
    public function updateProductBacklog($request);
    public function deleteProductBacklog($request);
    public function createSprintBacklog($request);
    public function updateSprintBacklog($request);
    public function deleteSprintBacklog($request);
    public function updateSprintBacklogEstimation($request);
    public function createSprint($request);
    public function getSprint($request);
    public function endSprint($request);
    public function updateSprintBacklogStatus($request);
    public function updateSprintBacklogAssignedTo($request);
}
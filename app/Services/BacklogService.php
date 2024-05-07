<?php

namespace App\Services;

use App\Repositories\Contracts\BacklogRepositoryContract;

class BacklogService
{
    
    private $backlogRepository;
    
    public function __construct(BacklogRepositoryContract $backlogRepository)
    {
        $this->backlogRepository = $backlogRepository;
    }

    public function getProductBacklog($request)
    {
        return $this->backlogRepository->getProductBacklog($request);
    }

    public function createProductBacklog($request)
    {
        return $this->backlogRepository->createProductBacklog($request);
    }

    public function updateProductBacklog($request)
    {
        return $this->backlogRepository->updateProductBacklog($request);
    }

    public function deleteProductBacklog($request)
    {
        return $this->backlogRepository->deleteProductBacklog($request);
    }

    public function createSprintBacklog($request)
    {
        return $this->backlogRepository->createSprintBacklog($request);
    }

    public function updateSprintBacklog($request)
    {
        return $this->backlogRepository->updateSprintBacklog($request);
    }

    public function deleteSprintBacklog($request)
    {
        return $this->backlogRepository->deleteSprintBacklog($request);
    }

    public function updateSprintBacklogEstimation($request)
    {
        return $this->backlogRepository->updateSprintBacklogEstimation($request);
    }

    public function createSprint($request)
    {
        return $this->backlogRepository->createSprint($request);
    }

    public function getSprint($request)
    {
        return $this->backlogRepository->getSprint($request);
    }

    public function endSprint($request)
    {
        return $this->backlogRepository->endSprint($request);
    }


    public function updateSprintBacklogStatus($request)
    {
        return $this->backlogRepository->updateSprintBacklogStatus($request);
    }

    public function updateSprintBacklogAssignedTo($request)
    {
        return $this->backlogRepository->updateSprintBacklogAssignedTo($request);
    }
}
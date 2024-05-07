<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BacklogService;

class BacklogController extends Controller
{
    private $backlogService;
    
    public function __construct(BacklogService $backlogService)
    {
        $this->backlogService = $backlogService;
    }

    public function getProductBacklog(Request $request)
    {
        return $this->backlogService->getProductBacklog($request);
    }

    public function createProductBacklog(Request $request)
    {
        return $this->backlogService->createProductBacklog($request);
    }

    public function updateProductBacklog(Request $request)
    {
        return $this->backlogService->updateProductBacklog($request);
    }

    public function deleteProductBacklog(Request $request)
    {
        return $this->backlogService->deleteProductBacklog($request);
    }

    public function createSprintBacklog(Request $request)
    {
        return $this->backlogService->createSprintBacklog($request);
    }

    public function updateSprintBacklog(Request $request)
    {
        return $this->backlogService->updateSprintBacklog($request);
    }

    public function deleteSprintBacklog(Request $request)
    {
        return $this->backlogService->deleteSprintBacklog($request);
    }

    public function updateSprintBacklogEstimation(Request $request)
    {
        return $this->backlogService->updateSprintBacklogEstimation($request);
    }

    public function createSprint(Request $request)
    {
        return $this->backlogService->createSprint($request);
    }

    public function getSprint(Request $request)
    {
        return $this->backlogService->getSprint($request);
    }

    public function endSprint(Request $request)
    {
        return $this->backlogService->endSprint($request);
    }


    public function updateSprintBacklogStatus(Request $request)
    {
        return $this->backlogService->updateSprintBacklogStatus($request);
    }

    public function updateSprintBacklogAssignedTo(Request $request)
    {
        return $this->backlogService->updateSprintBacklogAssignedTo($request);
    }
}

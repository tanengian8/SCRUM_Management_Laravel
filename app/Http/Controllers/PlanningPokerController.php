<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PlanningPokerService;

class PlanningPokerController extends Controller
{
   private $planningPokerService;
   
    public function __construct(PlanningPokerService $planningPokerService)
    {
        $this->planningPokerService = $planningPokerService;
    }

    public function getNotification(Request $request)
    {
        return $this->planningPokerService->getNotification($request);
    }

    public function getPlanningPokerSession(Request $request)
    {
        return $this->planningPokerService->getPlanningPokerSession($request);
    }

    public function getSessionDetails(Request $request)
    {
        return $this->planningPokerService->getSessionDetails($request);
    }

    public function getNotes(Request $request)
    {
        return $this->planningPokerService->getNotes($request);
    }

    public function addNotes(Request $request)
    {
        return $this->planningPokerService->addNotes($request);
    }

    public function updatePlanningPokerEstimation(Request $request)
    {
        return $this->planningPokerService->updatePlanningPokerEstimation($request);
    }

    public function revote(Request $request)
    {
        return $this->planningPokerService->revote($request);
    }

    public function getSequenceNumber(Request $request)
    {
        return $this->planningPokerService->getSequenceNumber($request);
    }

    public function createSequenceNumber(Request $request)
    {
        return $this->planningPokerService->createSequenceNumber($request);
    }

    public function resetSequenceNumber(Request $request)
    {
        return $this->planningPokerService->resetSequenceNumber($request);
    }
}

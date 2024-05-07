<?php

namespace App\Services;

use App\Repositories\Contracts\PlanningPokerRepositoryContract;

class PlanningPokerService
{
        
        private $planningPokerRepository;
        
        public function __construct(PlanningPokerRepositoryContract $planningPokerRepository)
        {
            $this->planningPokerRepository = $planningPokerRepository;
        }

        public function getNotification($request)
        {
            return $this->planningPokerRepository->getNotification($request);
        }

        public function getPlanningPokerSession($request)
        {
            return $this->planningPokerRepository->getPlanningPokerSession($request);
        }
    
        public function getSessionDetails($request)
        {
            return $this->planningPokerRepository->getSessionDetails($request);
        }

        public function getNotes($request)
        {
            return $this->planningPokerRepository->getNotes($request);
        }

        public function addNotes($request)
        {
            return $this->planningPokerRepository->addNotes($request);
        }

        public function updatePlanningPokerEstimation($request)
        {
            return $this->planningPokerRepository->updatePlanningPokerEstimation($request);
        }

        public function revote($request)
        {
            return $this->planningPokerRepository->revote($request);
        }

        public function getSequenceNumber($request)
        {
            return $this->planningPokerRepository->getSequenceNumber($request);
        }

        public function createSequenceNumber($request)
        {
            return $this->planningPokerRepository->createSequenceNumber($request);
        }

        public function resetSequenceNumber($request)
        {
            return $this->planningPokerRepository->resetSequenceNumber($request);
        }
}
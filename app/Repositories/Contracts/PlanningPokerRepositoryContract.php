<?php

namespace App\Repositories\Contracts;


interface PlanningPokerRepositoryContract
{
    public function getNotification($request);
    public function getPlanningPokerSession($request);
    public function getSessionDetails($request);
    public function getNotes($request);
    public function addNotes($request);
    public function updatePlanningPokerEstimation($request);
    public function revote($request);
    public function getSequenceNumber($request);
    public function createSequenceNumber($request);
    public function resetSequenceNumber($request);
}
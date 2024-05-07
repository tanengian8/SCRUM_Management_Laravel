<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Token;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\ProjectMember;
use App\Models\Project;

class CheckScrumMaster
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        //get user id from bearer token
        //Decode the token to get the user ID, role and projectID
        $token = $request->bearerToken();
        $jwtToken = new Token($token);
        $userID = JWTAuth::decode($jwtToken)->get('userID');
        $projectID = JWTAuth::decode($jwtToken)->get('projectID');
        $isSM = JWTAuth::decode($jwtToken)->get('isSM');

         //verify if user is a SCRUM Master in the projectMember table with the current projectID
        $projectMember = ProjectMember::where('projectID', $projectID)->where('userID', $userID)->first();
        if ($projectMember && $projectMember->isSM == $isSM) {
            return $next($request);
        }

        //verify if user is project owner if not SCRUM Master
        $projectOwner = Project::where('id', $projectID)->where('creatorID', $userID)->first();
        if ($projectOwner) {
            return $next($request);
        }

        return response()->json(['error' => 'Unauthorized'], 403);
    }
}

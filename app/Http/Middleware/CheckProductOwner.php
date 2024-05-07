<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Token;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\ProjectMember;
use App\Models\Project;


class CheckProductOwner
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
        $isPO = JWTAuth::decode($jwtToken)->get('isPO');

         //verify if user is a Project Owner in the projectMember table with the current projectID
        $projectMember = ProjectMember::where('projectID', $projectID)->where('userID', $userID)->first();
        if ($projectMember && $projectMember->isPO == $isPO) {
            return $next($request);
        }

        //verify if user is project creator if not Product Owner
        $projectCreator= Project::where('id', $projectID)->where('creatorID', $userID)->first();
        if ($projectCreator) {
            return $next($request);
        }

        return response()->json(['error' => 'Unauthorized'], 403);
    }
    
}

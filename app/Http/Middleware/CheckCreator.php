<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Token;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Project;

class CheckCreator
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
        $isCreator = JWTAuth::decode($jwtToken)->get('isCreator');

        //verify if user is a creator inside the project table
        $projectCreator = Project::where('id', $projectID)->where('creatorID', $userID)->first();

        //check if the isCreator is true and the user is the creator of the project
        if ($isCreator == true && $projectCreator) {
            return $next($request);
        }
        
        return response()->json(['error' => 'Unauthorized'], 403);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MemberMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
       
        $token = $request->input('token');

        if (!$token) {
            return response()->json(['message' => 'Unauthorized token missing'], 401);
        }

        $user = User::where('login_tokens', $token)->first();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized user not found'], 401);
        } else if($user->status != "2") {
            return response()->json(['message' => 'Unauthorized member not found'], 401);
        }

        return $next($request);
    }
}


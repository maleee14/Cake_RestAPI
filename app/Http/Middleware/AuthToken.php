<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('Authorization');
        $user = User::where('auth_token', $token)->first();
        $authenticate = true;

        if (!$token) {
            $authenticate = false;
            $message = "unauthorized";
        } else {
            if (!$user) {
                $authenticate = false;
                $message = "invalid token";
            } else {
                // Log in the user if token matches
                Auth::login($user);
            }
        }

        // Proceed or return error response based on authentication
        if ($authenticate) {
            return $next($request);
        } else {
            return response()->json([
                "status" => false,
                "errors" => [
                    "message" => [
                        $message
                    ]
                ]
            ])->setStatusCode(401);
        }
    }
}

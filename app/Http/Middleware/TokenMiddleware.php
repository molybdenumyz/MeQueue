<?php

namespace App\Http\Middleware;

use App\Common\Utils;
use App\Exceptions\Auth\NeedLoginException;
use App\Exceptions\Auth\TokenExpiredException;
use App\Exceptions\Auth\UserNotActivatedException;
use App\Repository\Eloquent\TokenRepository;
use App\Repository\Eloquent\UserRepository;
use Closure;

class TokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    protected $userRepository;
    protected $tokenRepository;

    /**
     * TokenMiddleware constructor.
     * @param UserRepository $ur
     * @param TokenRepository $tr
     */
    public function __construct(UserRepository $ur, TokenRepository $tr)
    {
        $this->userRepository = $ur;
        $this->tokenRepository = $tr;
    }

    public function handle($request, Closure $next)
    {

        $time = Utils::createTimeStamp();

        if(!$request->hasHeader('token'))
            throw new NeedLoginException();

        $tokenStr = $request->header('token');

        $token = $this->tokenRepository->getBy('token',$tokenStr)->first();

        if($token === null)
            throw new NeedLoginException();

        if($token->expires_at < $time)
            throw new TokenExpiredException();
        $user = $this->userRepository->get($token->user_id)->first();

        if (config('user.register_need_check')) {
            if($user->status == 0)
                throw new UserNotActivatedException();
        }

        $request->user = $user;
        return $next($request);
    }
}

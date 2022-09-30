<?php

namespace App\Modules\Auth\Controllers;

use App\Exceptions\VerifyEmailException;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

// use Cookie;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected string $token;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function setToken(string $token): void
    {
        $this->token = $token;
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param Request $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        $user = User::where('email', strtolower($request->input($this->username())))->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return false;
        }

        $this->guard()->setUser($user);

        if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()) {
            return false;
        }

        $this->setToken($user->createToken($request->device_name)->plainTextToken);

        if(method_exists($this->guard(), 'attempt')) {
            $resp = $this->guard()->attempt(
                $this->credentials($request), $request->filled('remember')
            );
        } else {
            $resp = true;
        }

        return $resp;
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param Request $request
     * @return JsonResponse
     */
    protected function sendLoginResponse(Request $request)
    {

        // $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        return response()->json([
            'token' => $this->token,
        ])->header('Authorization', $this->token);
    }

    /**
     * Get the failed login response instance.
     *
     * @param Request $request
     * @return void
     *
     * @throws ValidationException
     * @throws VerifyEmailException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $user = $this->guard()->user();
        if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()) {
            throw VerifyEmailException::forUser($user);
        }

        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }

    /**
     * Log the user out of the application.
     *
     * @param Request $request
     * @return void
     */
    public function logout(Request $request)
    {
        if(method_exists($request->user()->currentAccessToken(), 'delete')) {
            $request->user()->currentAccessToken()->delete();
        } else {

        }

        // auth()->user()->tokens()->delete();
        // \Auth::logout();

        app()->get('auth')->forgetGuards();
        auth('web')->logout();
        auth()->guard('web')->logout();
        // auth()->guard()->logout();

        $delete_cookie = \Cookie::forget('laravel_vue_spa_template_session');
        // Cookie::queue(\Cookie::forget('myCookie'));

        // \Cookie::queue(
        //     \Cookie::forget('laravel_vue_spa_template_session')
        //   );

        // \Cookie::queue(
        //     \Cookie::forget('XSRF-TOKEN')
        //   );

        return response()->noContent()->withCookie($delete_cookie);

    }

    /**
     * Validate the user login request.
     *
     * @param Request $request
     * @return void
     */
    protected function validateLogin(Request $request): void
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
            'device_name' => 'required|string',
        ]);
    }
}

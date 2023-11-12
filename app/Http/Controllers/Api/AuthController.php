<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AuthController extends ApiController
{
    public function login(Request $request)
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendError(1, 'Validate error', $validator->errors());
        }

        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $errors = (object) [
                'email' => 'required',
                'password' => 'required'
            ];

            return $this->sendError(2, 'Wrong email or password', $errors);
        }

        $user = Auth::user();
        $token = $user->createToken('AuthToken')->plainTextToken;

        $responseData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'email_verified_at' => $user->email_verified_at,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
            'is_active' => $user->is_active,
            'users_code' => $user->users_code,
            'access_token' => $token,
        ];

        return $this->sendResponse(0, 'Login Successfully', $responseData);
    }

    public function checkToken(Request $request)
    {
        if (Auth::check()) {
            $header = Auth::user($request->header('Authorization'));
            $header['token'] = $request->bearerToken();

            if ($header) {
                return $this->sendResponse(0, "Valid token", $header);
            } else {
                return $this->sendError(1, "Invalid token");
            }
        } else {
            return $this->sendError(2, "Invalid Token.");
        }
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            $request->user()->tokens->each(function ($token) {
                $token->delete();
            });
            return $this->sendResponse(0, "Logout Successfully");
        } else {
            return $this->sendError(2, "Logout failed.");
        }
    }

    public function register(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
            'password_confirmation' => 'required|same:password',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendError(1, 'Validate erorr', $validator->errors());
        }

        $user = new User([
            'users_code' => generateCode('USERS'),
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->save();

        return $this->sendCreatedResponse(1, 'Register Successfully');
    }

    public function forgot_password(Request $request)
    {
        $rules = [
            'email' => 'required|email|exists:users,email',
        ];

        $validator = $this->validateThis($request, $rules);
        if ($validator->fails()) {
            return $this->sendError(1, 'Email tidak terdaftar');
        }

        $email = $request->email;
        $user = User::where('email', $email)->first();

        if ($user->is_active == 0) {
            return $this->sendError(2, "Akun anda telah di-nonaktifkan. Silahkan contact admin", (object) array());
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        DB::table('password_resets')
            ->updateOrInsert(
                ['email' => $email],
                ['token' => $token, 'created_at' => Carbon::now()]
            );

        $app_url = env('BASE_URL');
        $url = $app_url . '/forgot-password/next?token=' . $token;

        $data = [
            'name' => $user->name,
            'title' => 'Pulihkan Kata Sandi',
            'url' => $url
        ];

        Mail::to($email)->send(new ResetPassword($data));

        return $this->sendResponse(0, 'Link tautan ubah kata sandi telah dikirim melalui email Anda');
    }
}
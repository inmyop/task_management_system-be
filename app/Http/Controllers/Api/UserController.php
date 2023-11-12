<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends ApiController
{
    public function getProfile(Request $request)
    {
        if (Auth::check()) {
            $user_code = Auth::user()->users_code;
            $user = User::where('users_code', $user_code)
                ->select(['users_code', 'name', 'email', 'email_verified_at'])
                ->first();

            return $this->sendResponse(1, 'User profile retrieved successfully', $user);
        } else {
            $errors = [
                'Unauthenticated' => 'You must be logged in to access this resource.',
            ];
            return $this->sendUnauthorized(2, "Unauthenticated", $errors);
        }
    }

    public function updateProfile(Request $request)
    {
        if (Auth::check()) {
            $user_code = Auth::user()->users_code;
            $user = User::where('users_code', $user_code)
                ->first();

            $rules = [
                'name' => 'required|string|max:255',
                'email' => [
                    'required',
                    'string',
                    'email',
                    Rule::unique('users')->ignore($user->id),
                ],
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return $this->sendError(1, 'Validate erorr', $validator->errors());
            }

            $user->name = $request->name;
            $user->email = $request->email;
            $user->save();

            return $this->sendResponse(1, 'Update profile successfully', $user);
        } else {
            $errors = [
                'Unauthenticated' => 'You must be logged in to access this resource.',
            ];
            return $this->sendUnauthorized(2, "Unauthenticated", $errors);
        }
    }
}
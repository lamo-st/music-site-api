<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\User;
use App\Traits\APIResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use APIResponse;

    public function signup(Request $request)
    {
        $rules = [
            'f_name' => 'required',
            'l_name' => 'required',
            'username' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
        ];

        $messages = [
            'f_name.required' => 'First name is required',
            'f_name.required' => 'Last name is required',
            'username.required' => 'Username is required',
            'email.required' => 'Email is required',
            'email.email' => 'Email is incorrect format',
            'email.unique' => 'The email was used before',
            'password.required' => 'Password is required',
            'password.min' => 'Password should be at least 8 chrachters',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return $this->createAPIResponse(false, null, null, $validator->errors());
        }

        try{
            User::create([
                'f_name' => $request->f_name,
                'l_name' => $request->l_name,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'address' => $request->address,
                'is_admin' => 0
            ]);

            return $this->createAPIResponse(true, null, 'Done Successfully');

        } catch(Exception $e)
        {
            return $this->createAPIResponse(false, null, null, $e);
        }
    }

    //------------------------------------------
    public function login(Request $request)
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required',
        ];

        $messages = [
            'email.required' => 'Email is required',
            'email.email' => 'Email is incorrect format',
            'password.required' => 'Password is required',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return $this->createAPIResponse(false, null, null, $validator->errors());
        }

        if (!Auth::attempt($request->only(['email', 'password']))) {
            return $this->createAPIResponse(false, null, 'Email and password do not match');
        }

        $user = User::where('email', $request->email)->first();
        if($user)
            return $this->createAPIResponse(true,
                    [
                        "token" => $user->createToken("API TOKEN")->plainTextToken,
                        "id" => $user->id,
                        "email" => $user->email,
                        "name" => $user->f_name,
                    ], 'Successfully logged in');
        else
            return $this->createAPIResponse(false, null, 'User is nout found!');
    }
}

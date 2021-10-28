<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
  /**
   * User login method
   *
   * @param Request $request
   * @return Response
   */
  public function login(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'email' => 'required|string|email|max:255|exists:users,email',
      'password' => 'required|string|min:6',
      'rememberMe' => 'boolean|nullable'
    ]);

    if ($validator->fails()) {
      return response()->json([
        'message' => 'invalid credentials',
        'errors' => $validator->errors(),
      ])->setStatusCode(400);
    }

    $data = $validator->validated();

    if (Auth::attempt(['email' => $data['email'], 'password' => $data['password']], $data['rememberMe'])) {
      return response()->json([
        'message' => 'Login successful',
        'user' => auth()->user()
      ], 200);
    } else {
      return response()->json(['message' => 'Invalid email or password'], 401);
    }
  }

  /**
   * User register method
   *
   * @param Request $request
   * @return Response
   */
  public function register(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'email' => 'required|string|email|max:255|unique:users,email',
      'password' => 'required|string|min:6|confirmed',
      'agreement' => 'required|accepted'
    ]);

    if ($validator->fails()) {
      return response()->json([
        'message' => 'invalid credentials',
        'errors' => $validator->errors(),
      ])->setStatusCode(400);
    }

    $data = $validator->validated();

    $user = User::create([
      'email' => $data['email'],
      'password' => Hash::make($data['password']),
      'language' => 'en'
    ]);
    $user->save();

    Auth::loginUsingId($user->id);

    return response()->json([
      'user' => auth()->user()
    ]);
  }

  /**
   * Forgot password method
   *
   * @param Request $request
   * @return Response
   */
  public function forgotPassword(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'email' => 'required|string|email|max:255|exists:users,email'
    ]);

    if ($validator->fails()) {
      return response()->json([
        'message' => 'invalid email address',
        'errors' => $validator->errors(),
      ])->setStatusCode(400);
    }

    Password::sendResetLink($request->only('email'));

    return response()->json([]);
  }

  /**
   * Fetch user method
   *
   * @param Request $request
   * @return Response
   */
  public function fetchUser(Request $request)
  {
    return response()->json([
      'user' => $request->user(),
    ]);
  }

  /**
   * Logout method
   *
   * @param Request $request
   * @return Response
   */
  public function logout(Request $request)
  {
    Auth::guard('web')->logout();

    $request->session()->invalidate();

    $request->session()->regenerateToken();

    return response()->json([]);
  }
}

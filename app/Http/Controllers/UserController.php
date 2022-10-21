<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Access\Gate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $users = User::query()->paginate();
        return response()->json($users);
    }

    /**
     * @throws AuthorizationException
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create-user');

        $user = new User();
        $user->fill($request->all());
        $user->save();
        return response()->json($user);
    }

    public function read(Request $request): JsonResponse
    {
        $users = User::query()->paginate();
        return response()->json($users);
    }

    public function login(Request $request)
    {
        $user = User::query()
            ->where('email', 'LIKE', $request->input('email'))
            ->first();

        $password = $request->input('password');

        if (!$user || !$password || !Hash::check($password, $user->password)) {
            return response()->json([], Response::HTTP_UNAUTHORIZED);
        }

        $token = $user->createToken($user->email)->plainTextToken;

        return response()->json($token);
    }
}

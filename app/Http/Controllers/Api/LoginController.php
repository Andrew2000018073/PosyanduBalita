<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Handle user login
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Validate incoming data
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6', // Ensure password has a minimum length
        ]);

        // Return validation errors if validation fails
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 422); // 422 Unprocessable Entity
        }

        // Check if the user exists and validate password
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => ['These credentials do not match our records.'],
            ], 401); // 401 Unauthorized
        }

        // Generate API token after successful login
        $token = $user->createToken('ApiToken')->plainTextToken;

        return response()->json([
            'success' => true,
            'user' => $user,
            'token' => $token,
        ], 200); // 200 OK
    }

    /**
     * Handle user logout
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // Mendapatkan pengguna yang terautentikasi
        $user = $request->user();

        // Periksa apakah pengguna terautentikasi
        if ($user) {
            // Hapus token akses saat ini
            $user->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Successfully logged out.',
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated.',
            ], 401);
        }
    }
}

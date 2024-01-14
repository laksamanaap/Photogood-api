<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function __construct(User $user)
    {
        $this->user = $user;
    }



     /**
 * @OA\Post(
 *     path="/auth/login",
 *     tags={"Authentication"},
 *     summary="User Login API's",
 *     @OA\RequestBody(
 *          description= "- Login to your account",
 *          required=true,
 *          @OA\JsonContent(
 *              type="object",
 *              @OA\Property(property="Username", type="string", example="ogud"),
 *              @OA\Property(property="Password", type="string", example="1234"),
 *          )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successfully Login",
 *      ),
 *     @OA\Response(
 *         response=201,
 *         description="Successfully Login",
 *      ),
 *      @OA\Response(
 *         response=400,
 *         description="Bad Request",
 *      ),
 *    )
 *
 * @return \Illuminate\Http\JsonResponse
 */
     public function loginUsers(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'Username' => 'required|string',
            'Password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([$validator->errors()], 422);
        }

        if (auth()->attempt([
            'Username' => $request->input('Username'),
            'Password' => $request->input('Password')
        ])) {
            $token = auth()->login(auth()->user());
            return response()->json([
                'data' => [
                    'user' => auth()->user(),
                    'access_token' => [
                        'token' => $token,
                        'type' => 'Bearer',
                        'expires_in' => auth()->factory()->getTTL() * 60,
                    ],
                ],
            ]);
        } else {
            return response()->json(['error' => 'Try to check your username or password'], 401);
        }

    }



    /**
 * @OA\Post(
 *     path="/auth/register",
 *     tags={"Authentication"},
 *     summary="User Register API's",
 *     @OA\RequestBody(
 *          description= "- Register to your account",
 *          required=true,
 *          @OA\JsonContent(
 *              type="object",
 *              @OA\Property(property="Username", type="string", example="ogud"),
 *              @OA\Property(property="NamaLengkap", type="string", example="ogud laksamana"),
 *              @OA\Property(property="Password", type="string", example="1234"),
 *              @OA\Property(property="Email", type="string", example="laksamana.arya1412@gmail.com"),
 *              @OA\Property(property="Alamat", type="string", example="Jl sama kamu kapan?"),
 *              @OA\Property(property="Followers", type="integer", example="0"),
 *          )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successfully Register",
 *      ),
 *     @OA\Response(
 *         response=201,
 *         description="Successfully Register",
 *      ),
 *      @OA\Response(
 *         response=400,
 *         description="Bad Request",
 *      ),
 *    )
 *
 * @return \Illuminate\Http\JsonResponse
 */
     public function registerUsers(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'Username' => 'required|string',
            'NamaLengkap' => 'required|string',
            'Password' => 'required|string',
            'Email' => 'required|string',
            'Alamat' => 'required|string',
            'Followers' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([$validator->errors()], 422);
        }

        if (User::where('Username', $request->input('Username'))->exists()) {
            return response()->json(['message' => 'Username has been used by another user!'], 401);
        } else {
             $this->validate($request, [
            'Username' => 'required|string',
            'NamaLengkap' => 'required|string',
            'Password' => 'required|string',
            'Email' => 'required|string',
            'Alamat' => 'required|string',
            'Followers' => 'required|integer'
        ]);

        $user = User::create([
            'Username' => $request->input('Username'),
            'NamaLengkap' => $request->input('NamaLengkap'),
            'Password' => bcrypt($request->input('Password')),
            'Email' => $request->input('Email'),
            'Alamat' => $request->input('Alamat'),
            'Followers' => $request->input('Followers'),
        ]);

        $token = auth()->login($user);

        return response()->json([
            'user' => $user,
            'access_token' => [
                'token' => $token,
                'type' => 'Bearer',
                'expires_in' => auth()->factory()->getTTL() * 60,   
            ],
        ]);

        }
       
    }

    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}

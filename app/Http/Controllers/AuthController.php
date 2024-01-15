<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Member;
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
 * @OA\Get(
 *     path="/get-all-user",
 *     tags={"Authentication"},
 *     summary="Get All User API's",
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
    public function getAllUser(Request $request)
    {

        $user = User::all();

        if (!$user) {
            return response()->json(['error' => "There's no user found!"], 404);
        } else {
            return response()->json(['data' => $user]);
        }

    }

    /**
 * @OA\Get(
 *     path="/get-all-member",
 *     tags={"Authentication"},
 *     summary="Get All Member API's",
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
    public function getAllMember(Request $request)
    {
        $user = Member::all();

        if (!$user) {
            return response()->json(['error' => "There's no member found!"], 404);
        } else {
            return response()->json(['data' => $user]);
        }
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
 *              @OA\Property(property="password", type="string", example="1234"),
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
        'password' => 'required|string'
    ]);

    if ($validator->fails()) {
        return response()->json([$validator->errors()], 422);
    }

    if ($token = auth()->attempt([
        'Username' => $request->input('Username'),
        'password' => $request->input('password')
    ])) {
        $user = auth()->user();
        $loginToken = Hash::make($user->username);
        $user->update(['login_tokens' => $loginToken]);

        return response()->json([
            'user' => $user,
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

        // Status 0 : Not Active / suspended
        // Status 1 : Active
        // Status 2 : Member
        // Status 3 : Admin
        $user = User::create([
            'username' => $request->input('Username'),
            'nama_lengkap' => $request->input('NamaLengkap'),
            'password' => bcrypt($request->input('Password')),
            'email' => $request->input('Email'),
            'alamat' => $request->input('Alamat'),
            'status' => 1, // The Default is 1 or active
            'followers' => $request->input('Followers'),
        ]);

        $token = auth()->login($user);

        return response()->json([
            'user' => $user,
            'token' => $token
        ]);

        }
       
    }

    
}

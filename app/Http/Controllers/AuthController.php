<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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
        'username' => 'required|string',
        'password' => 'required|string'
    ]);

    if ($validator->fails()) {
        return response()->json([$validator->errors()], 422);
    }

    if ($token = auth()->attempt([
        'username' => $request->input('username'),
        'password' => $request->input('password')
    ])) {
        $user = auth()->user();
        
        $loginToken = Hash::make($user->username);
        $user->update(['login_tokens' => $loginToken]);
        $userWithMember = User::with('member')->find($user->user_id);

        return response()->json($userWithMember,200);
    } else {
        return response()->json(['message' => 'Try to check your username or password!'], 401);
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
            'username' => 'required|string',
            'password' => 'required|string',
            'email' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([$validator->errors()], 422);
        }

        if (User::where('username', $request->input('username'))->exists()) {
            return response()->json(['message' => 'username has been used by another user!'], 401);
        } else {
             $this->validate($request, [
            'username' => 'required|string',
            'password' => 'required|string',
            'email' => 'required|string',
        ]
    );

        // Status 0 : Not Active / suspended
        // Status 1 : Active
        // Status 2 : Member
        // Status 3 : Admin
        $user = User::create([
            'username' => $request->input('username'),
            'nama_lengkap' => $request->input('nama_lengkap'),
            'password' => bcrypt($request->input('password')),
            'email' => $request->input('email'),
            'alamat' => $request->input('alamat'),
            'status' => 1, // The Default is 1 or active
        ]);

        $token = auth()->login($user);

        return response()->json([
            'user' => $user,
            'token' => $token
        ]);

        }
       
    }

    public function showUserDetail(Request $request)
    {
        $loginToken = $request->input('token');

        $user = User::where('login_tokens', $loginToken)->with('member')->first();

        if (!$user) {
            return response()->json(['message' => 'User not found!'], 404);
        }

        $appUrl = env('APP_URL');
        if ($user->foto_profil != null) {
            $user->foto_profil = "{$appUrl}/{$user->foto_profil}";
        }

        return response()->json($user, 200);
    }

    public function updateUserDetail(Request $request)
    {

         $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'nama_lengkap' => 'required|string',
            'email' => 'required|string',
            'alamat' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([$validator->errors()],422);
        }

        $loginToken = $request->input('token');
        $userByToken = User::where('login_tokens', $loginToken)->first();
        $userID = $userByToken->user_id;

        $user = User::with('member')->find($userID);

        if (!$user) {
            return response()->json(['message' => "There's no user found"]);
        }

        $user->username = $request->input('username');
        $user->nama_lengkap = $request->input('nama_lengkap');
        $user->email = $request->input('email');
        $user->alamat = $request->input('alamat');
        $user->save();

        return response()->json([
            'message' => "Successfully updated user with id $userID",
            'data' => $user
        ]);

    }

    public function storeUserPhoto(Request $request)
    {
         $validator = Validator::make($request->all(), [
            'images.*' => 'required|image:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([$validator->errors()],422);
        }

        $loginToken = $request->input("token");
        $userByToken = User::where('login_tokens', $loginToken)->first();
        $userID = $userByToken->user_id;

        $uploadFolders = 'foto_user';
        $image = $request->file('images');
        $imagePath = $image->store($uploadFolders, 'public');

        $user = User::with('member')->find($userID);
        $user->foto_profil = "storage/" . $imagePath; 
        $user->save();

        return response()->json([
            'message' => 'Image uploaded successfully',
            'data' => $user
        ]);

    }

    public function updateUserPhoto(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'images' => 'required|image:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([$validator->errors()], 422);
        }

        $loginToken = $request->input("token");
        $user = User::where('login_tokens', $loginToken)->first();

        $uploadFolder = 'foto_user';
        $oldImagePath = $user->foto_profil;

        if ($oldImagePath) {
            Storage::disk('public')->delete($oldImagePath);
        }

        $image = $request->file('images');
        $imagePath = $image->store($uploadFolder, 'public');

        $user->foto_profil = "storage/" . $imagePath;
        $user->save();

        return response()->json([
            'message' => 'Image updated successfully',
            'data' => $user
        ]);

    }

    public function deleteUserPhoto(Request $request)
    {
        
        $loginToken = $request->input("token");
        $user = User::where('login_tokens', $loginToken)->first();

        if (!$user) {
            return response()->json(['error' => 'Invalid token'], 401);
        }

        $oldImagePath = $user->foto_profil;

        if ($oldImagePath) {
            Storage::disk('public')->delete($oldImagePath);
            $user->foto_profil = null; 
            $user->save();

            return response()->json([
                'message' => 'Image deleted successfully',
                'data' => $user
            ]);
        }

        return response()->json([
            'message' => 'No image to delete',
            'data' => $user
        ]);
    }

    
}

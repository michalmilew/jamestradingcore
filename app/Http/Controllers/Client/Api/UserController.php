<?php

namespace App\Http\Controllers\Client\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * @var User
     */
    protected $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = User::find(auth()->id());
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $this->user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            if (isset($request->current_password)) {
                $validatedData = $request->validate([
                    'lang' => 'required|string|in:de,en,es,fr,it,nl,pt',
                ]);
                $this->user->lang = $request->lang;
            } else {
                $validatedData = $request->validate([
                    'lang' => 'required|string|in:de,en,es,fr,it,nl,pt',
                ]);
                $this->user->lang = $request->lang;
            }

            $this->user->save();

            Session::put('locale', $this->user->lang);
            App::setLocale($this->user->lang);

            return response()->json([
                'success' => true,
                'message' => __('User updated successfully.'),
                'data' => $this->user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updatePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'current_password' => 'required|string|max:255',
                'password' => [
                    'required',
                    'confirmed',
                    'min:6',
                ],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'The given data was invalid.',
                    'errors' => $validator->errors()
                ], 422);
            }

            if (!Hash::check($request->current_password, $this->user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'The current password is incorrect.'
                ], 422);
            }

            $this->user->password = Hash::make($request->password);
            $this->user->save();

            return response()->json([
                'success' => true,
                'message' => 'Password updated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

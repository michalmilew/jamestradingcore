<?php

namespace App\Http\Controllers\Client\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TradingApiClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use App\Notifications\CustomResetPasswordNotification;
use Laravel\Sanctum\HasApiTokens;

class AuthController extends Controller
{
    /**
     * Handle user login and return JWT token
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            // Check if user exists first
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                Log::error('Login attempt with non-existent email: ' . $request->email);
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            if (!Auth::attempt($request->only('email', 'password'))) {
                Log::error('Login attempt with incorrect password for user: ' . $request->email);
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }

            $user = Auth::user();
            Log::info('User logged in successfully: ' . $user->email);

            // if ($user->restricted_user === 1) {
            //     Auth::logout();
            //     return response()->json([
            //         'success' => true,
            //         'message' => __('User is restricted')
            //     ], 403);
            // }

            // Create token
            /** @var User $user */
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $user,
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during login'
            ], 500);
        }
    }

    /**
     * Handle user registration
     */
    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'lang' => ['required', 'string', 'in:en,pt'],
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'lang' => $request->lang,
            ]);

            // Create token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $user,
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Registration error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during registration'
            ], 500);
        }
    }

    /**
     * Handle user logout
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Successfully logged out'
            ]);
        } catch (\Exception $e) {
            Log::error('Logout error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during logout'
            ], 500);
        }
    }

    /**
     * Get authenticated user
     */
    public function me(Request $request)
    {
        try {
            $user = $request->user();
            
            // Check and update restricted user status based on current balance
            $userAccounts = $user->userAccounts->pluck('account_id')->toArray();
            
            // Only check restricted status if user has accounts
            if (count($userAccounts) > 0) {
                try {
                    $tradingApiClient = new TradingApiClient;
                    $accounts = $tradingApiClient->getAccounts($userAccounts);
                    $accounts = collect($accounts);
                    
                    $totalBalance = $accounts->sum('balance');
                    $wasRestricted = $user->restricted_user;
                    
                    if ($user->restricted_user && $totalBalance >= 350) {
                        // User has reached the minimum balance requirement - remove restriction
                        $user->restricted_user = false;
                        $user->save();
                        
                        Log::info('User restriction removed due to sufficient balance (auth/me)', [
                            'user_id' => $user->id,
                            'total_balance' => $totalBalance,
                            'was_restricted' => $wasRestricted
                        ]);
                    } else {
                        // If user is not restricted, keep them unrestricted regardless of balance
                        // If user is restricted but balance < 350, keep them restricted
                        Log::info('User restriction status unchanged', [
                            'user_id' => $user->id,
                            'total_balance' => $totalBalance,
                            'was_restricted' => $wasRestricted,
                            'current_restricted' => $user->restricted_user
                        ]);
                    }
                } catch (\Exception $e) {
                    // If there's an error fetching accounts, log it but don't fail the request
                    Log::warning('Could not check balance for restricted user status update', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage()
                    ]);
                }
            } else {
                // User has no accounts - if they're not restricted, keep them unrestricted
                // If they are restricted, they need to add accounts to get unrestricted
                Log::info('User has no accounts, skipping restricted status check', [
                    'user_id' => $user->id,
                    'restricted_user' => $user->restricted_user
                ]);
            }
            
            return response()->json([
                'success' => true,
                'data' => $user->fresh() // Return fresh user data with updated restricted_user status
            ]);
        } catch (\Exception $e) {
            Log::error('Get user error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching user data'
            ], 500);
        }
    }

    public function forgotPassword(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|exists:users,email'
            ]);

            // Find the user by email
            $user = User::where('email', $request->email)->first();

            // Generate a password reset token
            $token = Password::createToken($user);

            // Send the custom reset password notification
            $notification = new CustomResetPasswordNotification($token, $user->lang);
            $notification->sendMail($user);

            return response()->json([
                'success' => true,
                'message' => __('Password reset link sent to your email')
            ]);

        } catch (\Exception $e) {
            Log::error('Forgot Password Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

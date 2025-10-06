<?php

namespace App\Http\Controllers\Client\Api;

use App\Http\Controllers\Controller;
use App\Models\TelegramGroup;
use App\Models\InstructionalVideo;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\UserAccount;
use App\Models\NewTradingApiClient;

class TelegramGroupController extends Controller
{
    protected $telegramService;
    protected $tradingApiClient;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
        $this->tradingApiClient = new NewTradingApiClient;
    }

    public function index()
    {
        try {
            $user = Auth::user();

            // Get user's active MT4 accounts
            $userAccounts = UserAccount::where('user_id', $user->id)
                ->whereNull('deleted_at')
                ->get();

            $totalBalance = 0;
            $hasConnectedAccount = false;

            try {
                $mt4Account = $this->tradingApiClient->getAccount($user->id);
                if ($mt4Account && isset($mt4Account->balance)) {
                    $hasConnectedAccount = true;
                    $totalBalance += $mt4Account->balance;
                }
            } catch (\Exception $e) {
                Log::error('Failed to fetch MT4 account balance: ' . $e->getMessage());
            }

            // Get all telegram groups
            $telegramGroups = TelegramGroup::orderBy('min_balance')->get();

            // If no connected accounts, get forced access groups
            $forcedGroupIds = [];
            if (!$hasConnectedAccount) {
                $forcedGroupIds = DB::table('forced_telegram_group_access')
                    ->where('user_id', $user->id)
                    ->pluck('telegram_group_id')
                    ->toArray();
            }

            // Map groups with access information
            $groups = $telegramGroups->map(function($group) use ($totalBalance, $hasConnectedAccount, $forcedGroupIds) {
                $isEnabled = false;

                // Check if user has access through balance or forced access
                if ($hasConnectedAccount) {
                    $isEnabled = $totalBalance >= $group->min_balance;
                } else {
                    $isEnabled = in_array($group->id, $forcedGroupIds);
                }

                return [
                    'name' => $group->name,
                    'key' => $group->key,
                    'minBalance' => $group->min_balance,
                    'isEnabled' => $isEnabled,
                    'isForcedAccess' => !$hasConnectedAccount && in_array($group->id, $forcedGroupIds)
                ];
            });

            // Get video URL for current language
            $currentLang = app()->getLocale();
            $video = InstructionalVideo::where('language', $currentLang)->first();

            if (!$video) {
                $video = InstructionalVideo::where('language', 'en')->first();
            }

            $availableLanguages = InstructionalVideo::pluck('language')->unique();

            // Get all videos
            $allVideos = InstructionalVideo::all();

            return response()->json([
                'success' => true,
                'data' => [
                    'groups' => $groups,
                    'balance' => $totalBalance,
                    'hasConnectedAccount' => $hasConnectedAccount,
                    'video' => $video,
                    'videos' => $allVideos,
                    'availableLanguages' => $availableLanguages,
                    'currentLang' => $currentLang
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error in TelegramGroupController@index: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('Failed to load Telegram groups')
            ], 500);
        }
    }

    public function getInviteLink(Request $request)
    {
        try {
            $request->validate([
                'group_key' => 'required|string'
            ]);

            $user = Auth::user();
            $group = TelegramGroup::where('key', $request->group_key)->firstOrFail();

            // Get user's active MT4 accounts
            $userAccounts = UserAccount::where('user_id', $user->id)
                ->whereNull('deleted_at')
                ->get();

            $totalBalance = 0;
            $hasConnectedAccount = false;

            // Fetch real-time balance from Trading API
            foreach ($userAccounts as $account) {
                try {
                    $mt4Account = $this->tradingApiClient->getAccount($account->account_id);
                    if ($mt4Account && isset($mt4Account->balance)) {
                        $hasConnectedAccount = true;
                        $totalBalance += $mt4Account->balance;
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to fetch MT4 account balance: ' . $e->getMessage());
                    continue;
                }
            }

            // Check access conditions
            $hasAccess = false;

            if ($hasConnectedAccount) {
                // Normal balance-based access
                $hasAccess = $totalBalance >= $group->min_balance;
            } else {
                // Check for forced access
                $hasAccess = DB::table('forced_telegram_group_access')
                    ->where('user_id', $user->id)
                    ->where('telegram_group_id', $group->id)
                    ->exists();
            }

            if (!$hasAccess) {
                return response()->json([
                    'success' => false,
                    'message' => __('Insufficient permissions to join this group.')
                ], 403);
            }

            // Check for existing invite link
            $existingLink = DB::table('user_telegram_groups')
                ->where('user_id', $user->id)
                ->where('telegram_group_id', $group->id)
                ->whereNotNull('invite_link')
                ->first();

            if ($existingLink) {
                return response()->json([
                    'success' => true,
                    'invite_link' => $existingLink->invite_link
                ]);
            }

            // Generate new invite link
            $inviteLink = $this->generateInviteLink($group->bot_token, $group->chat_id);

            if (!$inviteLink) {
                throw new \Exception('Failed to generate invite link');
            }

            // Save the invite link
            DB::table('user_telegram_groups')->insert([
                'user_id' => $user->id,
                'telegram_group_id' => $group->id,
                'invite_link' => $inviteLink,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'invite_link' => $inviteLink
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to generate Telegram invite link: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('Failed to generate invite link. Please try again later.')
            ], 500);
        }
    }

    private function generateInviteLink($botToken, $chatId)
    {
        // Create invite link using Telegram Bot API
        $url = "https://api.telegram.org/bot{$botToken}/createChatInviteLink";
        $data = [
            'chat_id' => $chatId,
            'expire_date' => time() + 365 * 24 * 60 * 60, // Link expires in 365 days
            'member_limit' => 1 // One-time use link
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);
        return $result['result']['invite_link'] ?? null;
    }

    public function leave(TelegramGroup $group)
    {
        try {
            $user = Auth::user();

            // Get the user's group record directly from the pivot table
            $userGroup = DB::table('user_telegram_groups')
                ->where('user_id', $user->id)
                ->where('telegram_group_id', $group->id)
                ->first();

            if ($userGroup && $userGroup->invite_link) {
                $this->telegramService->revokeInviteLink($group, $userGroup->invite_link);
            }

            // Remove the user from the group
            DB::table('user_telegram_groups')
                ->where('user_id', $user->id)
                ->where('telegram_group_id', $group->id)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => __('messages.group_left')
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to leave Telegram group: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('Failed to leave group. Please try again later.')
            ], 500);
        }
    }

    public function updateVideoLanguage(Request $request)
    {
        try {
            $request->validate([
                'language' => 'required|string|max:2'
            ]);

            $newLang = $request->language;
            $video = InstructionalVideo::where('language', $newLang)->first();

            if (!$video) {
                return response()->json([
                    'success' => false,
                    'message' => 'Video not found for selected language'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'videoUrl' => $video->url
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update video language: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update video language'
            ], 500);
        }
    }
}
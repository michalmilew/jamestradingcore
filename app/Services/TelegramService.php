<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    private $groups = [
    ];

    public function generateInviteLink($groupKey)
    {
        if (!isset($this->groups[$groupKey])) {
            throw new \Exception('Invalid group key');
        }

        $group = $this->groups[$groupKey];
        
        try {
            $response = Http::post("https://api.telegram.org/bot{$group['bot_token']}/createChatInviteLink", [
                'chat_id' => $group['chat_id'],
                'member_limit' => 1,
                'expire_date' => 0
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['result']['invite_link'])) {
                    return $data['result']['invite_link'];
                }
            }
            
            Log::error('Telegram API Error: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('Telegram Service Error: ' . $e->getMessage());
            return null;
        }
    }

    public function revokeInviteLink($groupKey, $inviteLink)
    {
        if (!isset($this->groups[$groupKey])) {
            return false;
        }

        $group = $this->groups[$groupKey];
        
        try {
            $response = Http::post("https://api.telegram.org/bot{$group['bot_token']}/revokeChatInviteLink", [
                'chat_id' => $group['chat_id'],
                'invite_link' => $inviteLink
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Failed to revoke invite link: ' . $e->getMessage());
            return false;
        }
    }
} 

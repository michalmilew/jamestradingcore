<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    private $groups = [
        'forex' => [
            'bot_token' => '1772449814:AAEV90dS7ws3-i0o-B41o2G9pr9EWr1Tx3s',
            'chat_id' => '-1001374090428'
        ],
        'gold' => [
            'bot_token' => '7574196466:AAFVlsLYYuhlMvkMJInDZI_13jiKsJ77Zl8',
            'chat_id' => '-1002255436190'
        ],
        'bitcoin' => [
            'bot_token' => '8044549822:AAFSS2k5HGFm4cDs26UX41jK1ZO-VZnz6bE',
            'chat_id' => '-1002469209311'
        ],
        'forexPlus' => [
            'bot_token' => '8186317731:AAGsMK5WafYIiTs7E0G0u9V7DP_u4J8GEZA',
            'chat_id' => '-1002275712867'
        ]
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
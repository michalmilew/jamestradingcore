<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TelegramGroup;
use App\Models\InstructionalVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramSettingsController extends Controller
{
    public function index()
    {
        $groups = TelegramGroup::all();
        $video = InstructionalVideo::where('language', app()->getLocale())->first();

        return view('admin.telegram-settings.index', compact('groups', 'video'));
    }

    public function storeGroup(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'key' => 'required|string|max:255|unique:telegram_groups',
            'min_balance' => 'required|numeric|min:0',
            'bot_token' => 'required|string|max:255',
            'chat_id' => 'required|string|max:255'
        ]);

        $validated['is_active'] = true;

        TelegramGroup::create($validated);

        return response()->json([
            'success' => true,
            'message' => __('Group created successfully.')
        ]);
    }

    public function getGroup(TelegramGroup $group)
    {
        return response()->json([
            'success' => true,
            'group' => $group
        ]);
    }

    public function updateGroup(Request $request, TelegramGroup $group)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'key' => 'required|string|max:255|unique:telegram_groups,key,' . $group->id,
                'min_balance' => 'required|numeric|min:0',
                'bot_token' => 'required|string|max:255',
                'chat_id' => 'required|string|max:255'
            ]);

            // Convert min_balance to float before updating
            $validated['min_balance'] = (float) $validated['min_balance'];

            $group->update($validated);

            return response()->json([
                'success' => true,
                'message' => __('Group updated successfully.')
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('An error occurred while updating the group. ' . $e->getMessage())
            ], 500);
        }
    }

    public function destroyGroup(TelegramGroup $group)
    {
        $group->delete();

        return response()->json([
            'success' => true,
            'message' => __('Group deleted successfully.')
        ]);
    }

    public function updateVideoLink(Request $request)
    {
        $validated = $request->validate([
            'url' => 'required|url',
            'language' => 'required|string'
        ]);

        InstructionalVideo::updateOrCreate(
            ['language' => $validated['language']],
            ['url' => $validated['url']]
        );

        return response()->json([
            'success' => true,
            'message' => __('Video link updated successfully.')
        ]);
    }

    public function getVideoByLanguage($language)
    {
        try {
            $video = InstructionalVideo::where('language', $language)->first();

            if (!$video) {
                return response()->json([
                    'success' => false,
                    'message' => 'No video found for selected language'
                ]);
            }

            return response()->json([
                'success' => true,
                'video' => [
                    'url' => $video->url,
                    'language' => $video->language
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch video data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch video data'
            ], 500);
        }
    }
} 
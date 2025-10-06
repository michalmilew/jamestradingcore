<div class="mb-4">
    <h3 class="text-lg font-semibold mb-2">{{ __('Telegram Group Permissions') }}</h3>
    <div class="space-y-2">
        @foreach($telegramGroups as $group)
        <label class="inline-flex items-center">
            <input type="checkbox" name="telegram_groups[]" value="{{ $group->id }}"
                   @if(isset($user) && $user->telegramGroups->contains($group->id)) checked @endif
                   class="form-checkbox h-5 w-5 text-blue-600">
            <span class="ml-2">{{ $group->name }}</span>
        </label>
        @endforeach
    </div>
</div> 
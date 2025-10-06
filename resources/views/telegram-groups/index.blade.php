<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl md:text-2xl font-bold text-white">{{ __('Group Access') }}</h1>
    </x-slot>
    <div class="p-5">
        <div class="container mx-auto px-4 py-12 md:py-8">
            <div class="max-w-4xl mx-auto">
                <p class="mb-8 text-gray-300 text-lg">{{ __('telegram_description') }}</p>

                <!-- Balance Information -->
                @if ($hasConnectedAccount)
                    <div
                        class="bg-gradient-to-r from-[#1a2234] to-[#12181F] rounded-xl p-8 mb-8 shadow-lg border border-gray-700">
                        <h2 class="text-xl font-semibold mb-4 text-white">{{ __('Your Trading Account') }}</h2>
                        <div class="flex items-center space-x-4">
                            <span class="text-gray-300 text-lg">{{ __('Current Balance') }}:</span>
                            <span class="text-3xl font-bold {{ $balance > 0 ? 'text-green-400' : 'text-red-400' }}">
                                €{{ number_format($balance, 2) }}
                            </span>
                        </div>
                    </div>
                @endif

                <!-- Groups Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($groups as $group)
                        <div
                            class="bg-gradient-to-b from-[#1a2234] to-[#12181F] rounded-xl p-6 relative {{ $group['isEnabled'] ? 'border-2 border-green-500 shadow-lg shadow-green-500/20' : 'border border-gray-700' }} flex flex-col h-full transform transition-all duration-300 hover:scale-[1.02]">

                            <!-- Group Info -->
                            <div class="flex-grow">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-xl font-bold text-white">{{ $group['name'] }}</h3>
                                    <!-- Group Status Badge -->
                                    @if ($group['isEnabled'])
                                        <span
                                            class="bg-green-500 text-white px-4 py-1.5 rounded-full text-sm font-medium shadow-lg shadow-green-500/30">
                                            {{ __('Available') }}
                                        </span>
                                    @else
                                        <span
                                            class="bg-gray-700 text-gray-300 px-4 py-1.5 rounded-full text-sm font-medium">
                                            {{ __('Locked') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="space-y-4">
                                    <div class="flex justify-between items-center bg-[#1a2234] p-3 rounded-lg">
                                        <span class="text-gray-300">{{ __('Required Balance') }}</span>
                                        <span
                                            class="font-semibold text-white">€{{ number_format($group['minBalance'], 2) }}</span>
                                    </div>

                                    @if (!$group['isEnabled'])
                                        <div class="text-red-400 text-sm bg-red-500/10 p-3 rounded-lg">
                                            €{{ number_format($group['minBalance'] - $balance, 2) }}
                                            {{ __('left_amount') }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Action Button -->
                            <div class="mt-6">
                                @if ($group['isEnabled'])
                                    <button 
                                        onclick="getInviteLink('{{ $group['key'] }}')"
                                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50">
                                        {{ __('Join Group') }}
                                    </button>
                                @else
                                    <button
                                        class="w-full bg-gray-700 text-gray-400 py-3 px-4 rounded-lg cursor-not-allowed"
                                        disabled title="{{ __('Insufficient balance to join this group') }}">
                                        {{ __('Join Group') }}
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Manual Copy Instructions -->
        <div class="container mx-auto px-4 py-12 md:py-8">
            <div class="max-w-4xl mx-auto">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4 md:gap-8 mb-6">
                    <p class="text-gray-300 text-lg">{{ __('telegram_video_description') }}</p>

                    <!-- Language Selector -->
                    <div class="relative">
                        <select id="videoLanguage"
                            class="bg-[#1a2234] border border-gray-700 text-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200"
                            onchange="updateVideoLanguage(this.value)">
                            @foreach ($availableLanguages as $lang)
                                <option value="{{ $lang }}" {{ $lang === $currentLang ? 'selected' : '' }}>
                                    {{ strtoupper($lang) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Video Container -->
                <div
                    class="bg-gradient-to-r from-[#1a2234] to-[#12181F] rounded-xl p-6 shadow-lg border border-gray-700">
                    <div class="max-w-2xl mx-auto">
                        <div class="relative min-h-[400px] md:min-h-[600px]" style="padding-bottom: 56.25%">
                            <iframe id="videoFrame" src="{{ $video->url }}" frameborder="0"
                                allow="autoplay; fullscreen; picture-in-picture; clipboard-write; encrypted-media"
                                class="absolute top-0 left-0 w-full h-full rounded-lg shadow-xl"
                                title="instructional video"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading Overlay -->
        <div class="loading-overlay fixed inset-0 bg-black bg-opacity-75 z-50 hidden items-center justify-center"
            id="loading-overlay">
            <div class="text-center">
                <img src="{{ asset('images/loading.gif') }}" alt="Loading..." class="w-16 h-16 mx-auto mb-4">
                <p class="text-white text-lg">{{ __('Loading...') }}</p>
            </div>
        </div>

        <!-- Modal -->
        <div id="inviteLinkModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-black bg-opacity-75"></div>
                </div>

                <div
                    class="inline-block align-bottom bg-[#1a2234] rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="px-6 pt-5 pb-4 sm:p-6">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-xl font-bold text-white mb-4" id="modalTitle"></h3>
                                <div class="mt-2">
                                    <p class="text-gray-300 text-lg" id="modalMessage"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-[#12181F] flex flex-row-reverse">
                        <button type="button" id="closeModal"
                            class="inline-flex justify-center rounded-lg border border-gray-700 shadow-sm px-6 py-2 bg-[#1a2234] text-base font-medium text-white hover:bg-[#12181F] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                            {{ __('Close') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const modal = document.getElementById('inviteLinkModal');
                    const loadingOverlay = document.getElementById('loading-overlay');
                    const closeButton = document.getElementById('closeModal');
                    const modalTitle = document.getElementById('modalTitle');
                    const modalMessage = document.getElementById('modalMessage');

                    window.showModal = function(title, message) {
                        modalTitle.textContent = title;
                        modalMessage.textContent = message;
                        modal.classList.remove('hidden');
                    };

                    window.showLoading = function() {
                        loadingOverlay.style.display = 'flex';
                    };

                    window.hideLoading = function() {
                        loadingOverlay.style.display = 'none';
                    };

                    function hideModal() {
                        modal.classList.add('hidden');
                    }

                    closeButton.addEventListener('click', hideModal);

                    modal.addEventListener('click', function(event) {
                        if (event.target === modal) {
                            hideModal();
                        }
                    });

                    document.addEventListener('keydown', function(event) {
                        if (event.key === 'Escape') {
                            hideModal();
                        }
                    });
                });

                function openTelegramLink(url) {
                    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
                    const isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
                    
                    if (isIOS && isSafari) {
                        // For iOS Safari, open in same window
                        window.location.href = url;
                    } else {
                        // For all other browsers, open in new tab
                        window.open(url, '_blank', 'noopener,noreferrer');
                    }
                }

                function getInviteLink(groupKey) {
                    // Show loading overlay
                    showLoading();

                    fetch('{{ route(\App\Models\SettingLocal::getLang() . '.client.telegram-groups.get-invite-link') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                group_key: groupKey
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            // Hide loading overlay
                        hideLoading();

                            if (data.success && data.invite_link) {
                            openTelegramLink(data.invite_link);
                            } else {
                                showModal('{{ __('Error') }}', data.error || '{{ __('An error occurred. Please try again later.') }}');
                            }
                        })
                        .catch(error => {
                            // Hide loading overlay
                        hideLoading();
                            showModal('{{ __('Error') }}', '{{ __('An error occurred. Please try again later.') }}');
                        });
                }

                function updateVideoLanguage(language) {
                    showLoading();

                    fetch('{{ route(\App\Models\SettingLocal::getLang() . '.client.telegram-groups.update-video-language') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                language: language
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            hideLoading();
                            if (data.success) {
                                document.getElementById('videoFrame').src = data.videoUrl;
                            } else {
                                alert(data.message || 'Failed to update video language');
                            }
                        })
                        .catch(error => {
                            hideLoading();
                            alert('An error occurred while updating the video language');
                        });
                }
            </script>

            <style>
                .loading-overlay {
                    display: none;
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.7);
                    z-index: 9999;
                    justify-content: center;
                    align-items: center;
                }

                .loading-gif {
                    width: 80px;
                    height: 80px;
                }
            </style>
        @endpush
    </div>
</x-app-layout>
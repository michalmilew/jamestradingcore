@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-100">
                <h2 class="text-2xl font-bold mb-6">{{ __('Telegram Settings') }}</h2>

                <!-- Notification Container -->
                <div id="notification-container" class="fixed top-4 right-4 z-50"></div>

                <!-- Edit Group Modal -->
                <div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
                    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-gray-800">
                        <div class="mt-3">
                            <h3 class="text-lg font-medium text-gray-100 mb-4">Edit Telegram Group</h3>
                            <div id="editGroupForm" class="space-y-4">
                                <input type="hidden" id="editGroupId" name="id">
                                <div>
                                    <label class="block text-sm font-medium text-gray-300">Name</label>
                                    <input type="text" id="editName" name="name" class="mt-1 block w-full rounded-md bg-gray-700 border-gray-600 text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-300">Key</label>
                                    <input type="text" id="editKey" name="key" class="mt-1 block w-full rounded-md bg-gray-700 border-gray-600 text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-300">Min Balance</label>
                                    <input type="number" id="editMinBalance" name="min_balance" class="mt-1 block w-full rounded-md bg-gray-700 border-gray-600 text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-300">Bot Token</label>
                                    <input type="text" id="editBotToken" name="bot_token" class="mt-1 block w-full rounded-md bg-gray-700 border-gray-600 text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-300">Chat ID</label>
                                    <input type="text" id="editChatId" name="chat_id" class="mt-1 block w-full rounded-md bg-gray-700 border-gray-600 text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                </div>
                                <div class="flex justify-end space-x-3 mt-4">
                                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-sm font-medium text-gray-300 hover:text-gray-100">
                                        Cancel
                                    </button>
                                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Save Changes
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add Group Form -->
                <form id="addGroupForm" action="{{ route(app()->getLocale() . '.admin.telegram-settings.store-group') }}" method="POST" class="mb-8">
                        @csrf
                        @method('POST')
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300">Name</label>
                            <input type="text" name="name" class="mt-1 block w-full rounded-md bg-gray-700 border-gray-600 text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300">Key</label>
                            <input type="text" name="key" class="mt-1 block w-full rounded-md bg-gray-700 border-gray-600 text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300">Min Balance</label>
                            <input type="number" name="min_balance" class="mt-1 block w-full rounded-md bg-gray-700 border-gray-600 text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300">Bot Token</label>
                            <input type="text" name="bot_token" class="mt-1 block w-full rounded-md bg-gray-700 border-gray-600 text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300">Chat ID</label>
                            <input type="text" name="chat_id" class="mt-1 block w-full rounded-md bg-gray-700 border-gray-600 text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Add Group
                        </button>
                    </div>
                </form>

                <!-- Video Link Form -->
                <form id="videoForm" class="mb-8">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300">Video URL</label>
                            <input type="url" id="videoUrl" name="url" value="{{ $video->url ?? '' }}" class="mt-1 block w-full rounded-md bg-gray-700 border-gray-600 text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300">Language</label>
                            <select 
                                id="videoLanguage" 
                                name="language" 
                                class="mt-1 block w-full rounded-md bg-gray-700 border-gray-600 text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                required
                                onchange="loadVideoData(this.value)"
                            >
                                <option value="en" {{ ($video->language ?? '') === 'en' ? 'selected' : '' }}>English</option>
                                <option value="es" {{ ($video->language ?? '') === 'es' ? 'selected' : '' }}>Spanish</option>
                                <option value="pt" {{ ($video->language ?? '') === 'pt' ? 'selected' : '' }}>Portuguese</option>
                                <option value="de" {{ ($video->language ?? '') === 'de' ? 'selected' : '' }}>Danish</option>
                                <option value="fr" {{ ($video->language ?? '') === 'fr' ? 'selected' : '' }}>French</option>
                                <option value="it" {{ ($video->language ?? '') === 'it' ? 'selected' : '' }}>Italy</option>
                                <option value="nl" {{ ($video->language ?? '') === 'nl' ? 'selected' : '' }}>Netherlands</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Update Video Link
                        </button>
                    </div>
                </form>

                <!-- Groups Table -->
                <div class="mt-8">
                    <h2 class="text-lg font-medium text-gray-100 mb-4">Telegram Groups</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-700">
                            <thead class="bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Key</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Min Balance</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-gray-800 divide-y divide-gray-700">
                                @foreach($groups as $group)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-100">{{ $group->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-100">{{ $group->key }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-100">{{ $group->min_balance }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <button onclick="editGroup({{ $group->id }})" class="text-indigo-400 hover:text-indigo-300 mr-3">Edit</button>
                                        <button onclick="deleteGroup({{ $group->id }})" class="text-red-400 hover:text-red-300">Delete</button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="loading-overlay" id="loading-overlay">
        <img src="{{ asset('images/loading.gif') }}" alt="Loading..." class="loading-gif">
    </div>
</div>


<script>
// // Add loading overlay elements at the top of the script
const loadingOverlay = document.getElementById('loading-overlay');

// Add showLoading and hideLoading functions
function showLoading() {
    loadingOverlay.style.display = 'flex';
}

function hideLoading() {
    loadingOverlay.style.display = 'none'; // Hide the overlay
}

// Move showNotification to global scope
function showNotification(message, type = 'success') {
    const container = document.getElementById('notification-container');
    const notification = document.createElement('div');
    notification.className = `mb-4 p-4 rounded-lg shadow-lg ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    } text-white transform transition-all duration-300 ease-in-out`;
    
    notification.innerHTML = `
        <div class="flex items-center">
            <div class="flex-shrink-0">
                ${type === 'success' 
                    ? '<svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>'
                    : '<svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>'
                }
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium">${message}</p>
            </div>
        </div>
    `;
    
    container.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.style.opacity = '1';
        notification.style.transform = 'translateX(0)';
    }, 10);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            container.removeChild(notification);
        }, 300);
    }, 3000);
}

document.addEventListener('DOMContentLoaded', function() {
    const language = '{{ app()->getLocale() }}';
    
    // Add Group Form
    document.getElementById('addGroupForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => Promise.reject(err));
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showNotification(data.message);
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                showNotification(data.message || 'An error occurred while adding the group.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred while adding the group.', 'error');
        });
    });

    // Video Form
    document.getElementById('videoForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        showLoading();
        
        fetch(`/${language}/admin/telegram-settings/video`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                showNotification('Video link updated successfully');
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                showNotification(data.message || 'An error occurred while updating the video link.', 'error');
            }
        })
        .catch(error => {
            hideLoading();
            console.error('Error:', error);
            showNotification('An error occurred while updating the video link.', 'error');
        });
    });

    // Edit Group Form
    document.querySelector('#editGroupForm button[type="submit"]').addEventListener('click', function(e) {
        e.preventDefault();
        const formData = new FormData();
        const groupId = document.getElementById('editGroupId').value;
        const language = '{{ app()->getLocale() }}';
        
        // Add form fields to FormData
        formData.append('name', document.getElementById('editName').value);
        formData.append('key', document.getElementById('editKey').value);
        formData.append('min_balance', document.getElementById('editMinBalance').value);
        formData.append('bot_token', document.getElementById('editBotToken').value);
        formData.append('chat_id', document.getElementById('editChatId').value);
        formData.append('_method', 'PUT');
        
        fetch(`/${language}/admin/telegram-settings/groups/${groupId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => Promise.reject(err));
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showNotification('Group updated successfully');
                closeEditModal();
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                showNotification(data.message || 'An error occurred while updating the group.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification(error.message || 'An error occurred while updating the group.', 'error');
        });
    });
});

// Define editGroup in global scope
function editGroup(id) {
    console.log('Edit button clicked for group:', id);
    const language = '{{ app()->getLocale() }}';
    const url = `/${language}/admin/telegram-settings/groups/${id}`;
    console.log('Fetching from URL:', url);
    
    fetch(url, {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Received data:', data);
        if (data.success) {
            const group = data.group;
            document.getElementById('editGroupId').value = group.id;
            document.getElementById('editName').value = group.name;
            document.getElementById('editKey').value = group.key;
            document.getElementById('editMinBalance').value = group.min_balance;
            document.getElementById('editBotToken').value = group.bot_token;
            document.getElementById('editChatId').value = group.chat_id;
            document.getElementById('editModal').classList.remove('hidden');
            console.log('Modal opened with group data');
        } else {
            console.error('Failed to load group data:', data.message);
            showNotification(data.message || 'Failed to load group data.', 'error');
        }
    })
    .catch(error => {
        console.error('Error fetching group data:', error);
        showNotification('Failed to load group data. Please try again.', 'error');
    });
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

function deleteGroup(id) {
    if (confirm('Are you sure you want to delete this group?')) {
        const language = '{{ app()->getLocale() }}';
        fetch(`/${language}/admin/telegram-settings/groups/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showNotification('Group deleted successfully');
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                showNotification(data.message || 'An error occurred while deleting the group.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred while deleting the group.', 'error');
        });
    }
}

function loadVideoData(language) {
    showLoading();
    const currentLang = '{{ app()->getLocale() }}';
    
    fetch(`/${currentLang}/admin/telegram-settings/video/${language}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success && data.video) {
            document.getElementById('videoUrl').value = data.video.url || '';
            document.getElementById('videoLanguage').value = data.video.language;
        } else {
            // Clear the URL field if no video exists for this language
            document.getElementById('videoUrl').value = '';
            showNotification('No video found for selected language', 'error');
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error:', error);
        showNotification('Failed to load video data', 'error');
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
        width: 100px; /* Adjust the size as needed */
        height: 100px; /* Adjust the size as needed */
    }

    .alert {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 10px;
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
        border-radius: 5px;
        display: none;
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border-color: #c3e6cb;
    }
</style>
@endsection
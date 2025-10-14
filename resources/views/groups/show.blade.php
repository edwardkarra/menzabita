<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('availability.calendar', $group) }}" class="mr-4 text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ $group->name }}
                    </h2>
                    @if($group->description)
                        <p class="text-sm text-gray-600 mt-1">{{ $group->description }}</p>
                    @endif
                </div>
            </div>
            
            @if($group->isOwner(auth()->user()))
                <div class="flex gap-2">
                    <a href="{{ route('groups.edit', $group) }}" class="btn-secondary">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </a>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Group Info Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Invite Code Card -->
                <div class="card">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Invite Code</h3>
                        <div class="flex items-center justify-between bg-gray-50 rounded-lg p-3">
                            <code class="text-lg font-mono font-bold text-blue-600">{{ $group->invite_code }}</code>
                            <button onclick="copyInviteCode()" class="btn-secondary text-sm">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                Copy
                            </button>
                        </div>
                        <p class="text-sm text-gray-500 mt-2">Share this code with friends to invite them to the group.</p>
                    </div>
                </div>

                <!-- Members Count Card -->
                <div class="card">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Members</h3>
                        <div class="text-3xl font-bold text-blue-600">{{ $group->getMemberCount() }}</div>
                        <p class="text-sm text-gray-500 mt-2">Active group members</p>
                    </div>
                </div>

                <!-- Owner Card -->
                <div class="card">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Group Owner</h3>
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-medium text-sm">
                                {{ strtoupper(substr($group->owner->name, 0, 1)) }}
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ $group->owner->name }}</p>
                                <p class="text-sm text-gray-500">{{ $group->owner->email }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Members List -->
            <div class="card">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Group Members</h3>
                        <span class="text-sm text-gray-500">{{ $group->members->count() }} members</span>
                    </div>

                    <div class="space-y-4">
                        @foreach($group->members as $member)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-medium">
                                        {{ strtoupper(substr($member->user->name, 0, 1)) }}
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-900">{{ $member->user->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $member->user->email }}</p>
                                        <p class="text-xs text-gray-400">Joined {{ $member->joined_at->format('M j, Y') }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3">
                                    @if($member->role === 'admin' || $member->user_id === $group->owner_id)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $member->user_id === $group->owner_id ? 'Owner' : 'Admin' }}
                                        </span>
                                    @endif

                                    @if($group->isOwner(auth()->user()) && $member->user_id !== $group->owner_id)
                                        <form action="{{ route('groups.remove-member', [$group, $member]) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-red-600 hover:text-red-800 text-sm"
                                                    onclick="return confirm('Are you sure you want to remove this member?')">
                                                Remove
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <a href="{{ route('availability.create', $group) }}" class="btn-primary text-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add Availability
                        </a>
                        <a href="{{ route('availability.calendar', $group) }}" class="btn-secondary text-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            View Calendar
                        </a>
                        @if(!$group->isOwner(auth()->user()))
                            <form action="{{ route('groups.leave', $group) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" 
                                        class="btn-danger w-full"
                                        onclick="return confirm('Are you sure you want to leave this group?')">>
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                    Leave Group
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyInviteCode() {
            const code = '{{ $group->invite_code }}';
            navigator.clipboard.writeText(code).then(function() {
                // Show success message
                const button = event.target.closest('button');
                const originalText = button.innerHTML;
                button.innerHTML = '<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Copied!';
                button.classList.add('bg-green-100', 'text-green-800');
                
                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.classList.remove('bg-green-100', 'text-green-800');
                }, 2000);
            });
        }
    </script>
</x-app-layout>
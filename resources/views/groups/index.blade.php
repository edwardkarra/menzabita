<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Groups') }}
            </h2>
            <a href="{{ route('groups.create') }}" class="btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Create Group
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Join Group Section -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Join a Group</h3>
                    <form action="{{ route('groups.join') }}" method="POST" class="flex flex-col sm:flex-row gap-3">
                        @csrf
                        <div class="flex-1">
                            <input type="text" 
                                   name="invite_code" 
                                   placeholder="Enter invite code" 
                                   class="input-field w-full"
                                   maxlength="8"
                                   required>
                            @error('invite_code')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" class="btn-secondary whitespace-nowrap">
                            Join Group
                        </button>
                    </form>
                </div>
            </div>

            <!-- Groups Grid -->
            @if($groups->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($groups as $group)
                        <div class="card group-card">
                            <div class="p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900 truncate">
                                        {{ $group->name }}
                                    </h3>
                                    @if($group->isOwner(auth()->user()))
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Owner
                                        </span>
                                    @endif
                                </div>

                                @if($group->description)
                                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                        {{ $group->description }}
                                    </p>
                                @endif

                                <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                                    <span>{{ $group->getMemberCount() }} members</span>
                                    <span>Code: <code class="bg-gray-100 px-2 py-1 rounded">{{ $group->invite_code }}</code></span>
                                </div>

                                <div class="flex flex-col sm:flex-row gap-2">
                                    <a href="{{ route('groups.show', $group) }}" class="btn-primary flex-1 text-center">
                                        View Group
                                    </a>
                                    
                                    @if($group->isOwner(auth()->user()))
                                        <div class="flex gap-2">
                                            <a href="{{ route('groups.edit', $group) }}" class="btn-secondary flex-1 text-center">
                                                Edit
                                            </a>
                                            <form action="{{ route('groups.destroy', $group) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn-danger"
                                                        onclick="return confirm('Are you sure you want to delete this group?')">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <form action="{{ route('groups.leave', $group) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="btn-danger w-full"
                                                    onclick="return confirm('Are you sure you want to leave this group?')">
                                                Leave Group
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No groups yet</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating a new group or joining an existing one.</p>
                    <div class="mt-6 flex flex-col sm:flex-row gap-3 justify-center">
                        <a href="{{ route('groups.create') }}" class="btn-primary">
                            Create your first group
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
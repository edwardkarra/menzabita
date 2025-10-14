<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Message -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">
                        Welcome back, {{ auth()->user()->name }}!
                    </h3>
                    <p class="text-gray-600">
                        Coordinate schedules with your friends and find the perfect time to meet up.
                    </p>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- My Groups -->
                <div class="card">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900">My Groups</h3>
                                <p class="text-sm text-gray-500">Manage your friend groups</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('groups.index') }}" class="btn-primary">
                            View Groups
                        </a>
                        </div>
                    </div>
                </div>

                <!-- Create Group -->
                <div class="card">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900">Create Group</h3>
                                <p class="text-sm text-gray-500">Start a new friend group</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('groups.create') }}" class="btn-primary">
                            Create New Group
                        </a>
                        </div>
                    </div>
                </div>

                <!-- Join Group -->
                <div class="card">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900">Join Group</h3>
                                <p class="text-sm text-gray-500">Use an invite code</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <form action="{{ route('groups.join') }}" method="POST" class="space-y-3">
                                @csrf
                                <input type="text" 
                                       name="invite_code" 
                                       placeholder="Enter invite code" 
                                       class="input-field w-full text-sm"
                                       maxlength="8"
                                       required>
                                <button type="submit" class="btn-primary">
                                Join Group
                            </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

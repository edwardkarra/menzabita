<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Group Calendar') }} - {{ $group->name }}
            </h2>
            <div class="mt-4 sm:mt-0 flex flex-col sm:flex-row gap-2">
                <a href="{{ route('groups.show', $group) }}" 
                   class="inline-flex items-center px-4 py-2 bg-green-600 border border-green-700 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Group Information
                </a>
                <a href="{{ route('availability.create', $group) }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-blue-700 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Availability
                </a>
                <a href="{{ route('availability.index', $group) }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 focus:bg-gray-200 active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                    </svg>
                    My Availability
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Back Button -->
            <div class="mb-6">
                <a href="{{ route('groups.index') }}" 
                   class="inline-flex items-center px-3 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 focus:bg-gray-200 active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to My Groups
                </a>
            </div>

            <!-- Calendar Info -->
            <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">
                            Calendar View (Next 30 Days)
                        </h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <p>View when all group members are available. Overlapping times show the best opportunities for group activities.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($availabilities->count() > 0)
                        <div class="space-y-6">
                            @php
                                $currentDate = $startDate->copy();
                            @endphp
                            
                            @while($currentDate->lte($endDate))
                                @php
                                    $dateString = $currentDate->format('Y-m-d');
                                    $dayAvailabilities = $availabilities->get($dateString, collect());
                                @endphp
                                
                                @if($dayAvailabilities->count() > 0)
                                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                                        <!-- Date Header -->
                                        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                                            <div class="flex justify-between items-center">
                                                <h3 class="text-lg font-semibold text-gray-800">
                                                    {{ $currentDate->format('l, F j, Y') }}
                                                </h3>
                                                <span class="text-sm text-gray-600">
                                                    {{ $dayAvailabilities->count() }} {{ Str::plural('person', $dayAvailabilities->count()) }} available
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Availability Timeline -->
                                        <div class="p-4">
                                            <div class="space-y-3">
                                                @foreach($dayAvailabilities->groupBy('user.name') as $userName => $userAvailabilities)
                                                    <div class="flex items-start space-x-3">
                                                        <div class="flex-shrink-0 w-24">
                                                            <span class="text-sm font-medium text-gray-700">{{ $userName }}</span>
                                                        </div>
                                                        <div class="flex-1">
                                                            <div class="flex flex-wrap gap-2">
                                                                @foreach($userAvailabilities as $availability)
                                                                    <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium 
                                                                                @if($availability->user_id === auth()->id()) 
                                                                                    bg-blue-100 text-blue-800 border border-blue-300
                                                                                @else 
                                                                                    bg-green-100 text-green-800 border border-green-300
                                                                                @endif">
                                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                                                        </svg>
                                                                        {{ \Carbon\Carbon::parse($availability->start_time)->format('g:i A') }} - 
                                                                        {{ \Carbon\Carbon::parse($availability->end_time)->format('g:i A') }}
                                                                        @if($availability->note)
                                                                            <span class="ml-1 text-xs opacity-75">({{ Str::limit($availability->note, 20) }})</span>
                                                                        @endif
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                            <!-- Overlap Detection -->
                                            @php
                                                $overlaps = [];
                                                $allTimes = $dayAvailabilities->map(function($availability) {
                                                    return [
                                                        'start' => \Carbon\Carbon::parse($availability->start_time),
                                                        'end' => \Carbon\Carbon::parse($availability->end_time),
                                                        'user' => $availability->user->name
                                                    ];
                                                })->sortBy('start');
                                                
                                                // Find overlapping time periods
                                                foreach($allTimes as $i => $time1) {
                                                    $overlapUsers = [$time1['user']];
                                                    $overlapStart = $time1['start'];
                                                    $overlapEnd = $time1['end'];
                                                    
                                                    foreach($allTimes as $j => $time2) {
                                                        if($i !== $j && 
                                                           $time1['start']->lt($time2['end']) && 
                                                           $time1['end']->gt($time2['start'])) {
                                                            if(!in_array($time2['user'], $overlapUsers)) {
                                                                $overlapUsers[] = $time2['user'];
                                                            }
                                                            $overlapStart = $overlapStart->gt($time2['start']) ? $overlapStart : $time2['start'];
                                                            $overlapEnd = $overlapEnd->lt($time2['end']) ? $overlapEnd : $time2['end'];
                                                        }
                                                    }
                                                    
                                                    if(count($overlapUsers) > 1) {
                                                        $overlapKey = $overlapStart->format('H:i') . '-' . $overlapEnd->format('H:i');
                                                        if(!isset($overlaps[$overlapKey])) {
                                                            $overlaps[$overlapKey] = [
                                                                'start' => $overlapStart,
                                                                'end' => $overlapEnd,
                                                                'users' => array_unique($overlapUsers)
                                                            ];
                                                        }
                                                    }
                                                }
                                            @endphp

                                            @if(count($overlaps) > 0)
                                                <div class="mt-4 pt-4 border-t border-gray-200">
                                                    <h4 class="text-sm font-medium text-gray-800 mb-2">
                                                        <svg class="w-4 h-4 inline mr-1 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        Best Meeting Times
                                                    </h4>
                                                    <div class="flex flex-wrap gap-2">
                                                        @foreach($overlaps as $overlap)
                                                            <div class="bg-green-50 border border-green-200 rounded-lg px-3 py-2">
                                                                <div class="text-sm font-medium text-green-800">
                                                                    {{ $overlap['start']->format('g:i A') }} - {{ $overlap['end']->format('g:i A') }}
                                                                </div>
                                                                <div class="text-xs text-green-600">
                                                                    {{ count($overlap['users']) }} {{ Str::plural('person', count($overlap['users'])) }}: {{ implode(', ', $overlap['users']) }}
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                                
                                @php
                                    $currentDate->addDay();
                                @endphp
                            @endwhile
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No availability found</h3>
                            <p class="mt-1 text-sm text-gray-500">No group members have set their availability for the next 30 days.</p>
                            <div class="mt-6">
                                <a href="{{ route('availability.create', $group) }}" 
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-blue-700 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Be the First to Add Availability
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Legend -->
            <div class="mt-6 bg-gray-50 border border-gray-200 rounded-lg p-4">
                <h3 class="text-sm font-medium text-gray-800 mb-3">Legend</h3>
                <div class="flex flex-wrap gap-4 text-xs">
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-blue-100 border border-blue-300 rounded mr-2"></div>
                        <span class="text-gray-600">Your availability</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-green-100 border border-green-300 rounded mr-2"></div>
                        <span class="text-gray-600">Others' availability</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-green-50 border border-green-200 rounded mr-2"></div>
                        <span class="text-gray-600">Overlapping times (best for meetings)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
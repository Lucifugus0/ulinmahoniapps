<!-- Depok Tab -->
<div class="area-tab-content hidden" data-tab="depok">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($propertyAreas['depok'] ?? [] as $property)
        <a href="{{ route('houses.show', ['id' => $property['id']]) }}" class="block">
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                <div class="relative h-48">
                    @if($property['thumbnail'])
                        <img src="{{ env('ADMIN_URL') }}/storage/{{ $property['thumbnail'] }}"
                             alt="{{ $property['name'] }}"
                             class="w-full h-full object-cover"
                             onerror="this.onerror=null; this.src='{{ asset('images/assets/placeholder.jpg') }}';">
                    @else
                        <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                            <i class="fas fa-building text-4xl text-gray-400"></i>
                        </div>
                    @endif
                    <div class="absolute inset-0 bg-black bg-opacity-40"></div>
                    <div class="absolute bottom-4 left-4 text-white">
                        <h3 class="text-xl font-medium">{{ $property['name'] }}</h3>
                        <h4 class="text-lg font-medium">{{ $property['city'] }}</h4>
                        <p class="text-sm">
                            @if($property['room_count'] > 0)
                                {{ $property['room_count'] }} {{ __('homepage.areas.rooms_available') }}
                            @else
                                {{ __('homepage.areas.view_details') }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </a>
        @empty
        <div class="col-span-full text-center py-8">
            <div class="text-gray-400">
                <i class="fas fa-building text-4xl mb-2"></i>
                <p class="text-gray-500">{{ __('homepage.messages.no_properties_in_area') }}</p>
            </div>
        </div>
        @endforelse
    </div>
</div>

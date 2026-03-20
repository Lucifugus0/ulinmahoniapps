<!-- Bogor Tab -->
<div class="area-tab-content hidden" data-tab="bogor">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($propertyAreas['bogor'] ?? [] as $property)
        <a href="{{ route('houses.show', ['id' => $property['id']]) }}" class="block">
            <!-- Glass area card — frosted overlay on image with depth hover -->
            <div class="overflow-hidden transition-all duration-400" style="background: var(--glass-bg, rgba(255, 255, 255, 0.15)); backdrop-filter: blur(24px); -webkit-backdrop-filter: blur(24px); border-radius: 1.75rem; border: 1px solid var(--glass-border, rgba(255, 255, 255, 0.18)); box-shadow: 0 8px 32px rgba(0, 0, 0, 0.10);">
                <div class="relative h-48">
                    @if($property['thumbnail'])
                        <img src="{{ env('ADMIN_URL') }}/storage/{{ $property['thumbnail'] }}"
                             alt="{{ $property['name'] }}"
                             class="w-full h-full object-cover transition-transform duration-500"
                             style="border-radius: 1.75rem 1.75rem 0 0;"
                             onerror="this.onerror=null; this.src='{{ asset('images/assets/placeholder.jpg') }}';">
                    @else
                        <div class="w-full h-full flex items-center justify-center" style="background: var(--glass-bg, rgba(255,255,255,0.3));">
                            <i class="fas fa-building text-4xl" style="color: var(--text-tertiary, #8888a4);"></i>
                        </div>
                    @endif
                    <!-- Glass overlay — frosted gradient bottom panel -->
                    <div class="absolute inset-x-0 bottom-0" style="background: linear-gradient(to top, rgba(0,0,0,0.6), transparent); height: 70%;"></div>
                    <div class="absolute bottom-4 left-4 text-white">
                        <h3 class="text-xl font-medium">{{ $property['name'] }}</h3>
                        <h4 class="text-lg font-light opacity-80">{{ $property['city'] }}</h4>
                        <!-- <p class="text-sm">
                            @if($property['room_count'] > 0)
                                {{ $property['room_count'] }} {{ __('homepage.areas.rooms_available') }}
                            @else
                                {{ __('homepage.areas.view_details') }}
                            @endif
                        </p> -->
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

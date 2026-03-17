<!-- Hero Component -->
<div class="hero-section">
    @if($heroMedia['type'] == 'video')
    <div class="hero-content">
        <video id="heroVideo" class="hero-media" autoplay loop muted playsinline>
            <source src="{{ asset($heroMedia['sources']['video']) }}" type="video/mp4">
            Your browser does not support the video tag.  
        </video>
        <div class="absolute bottom-4 right-4 p-4">
            <button id="playPauseBtn" class="bg-black/50 hover:bg-black/70 text-white p-3 rounded-full">
                <i id="playPauseIcon" class="fas fa-pause"></i>
            </button>
        </div>
    </div> 
    @else
    <div class="hero-content">
        <img id="heroImage" 
            src="{{ asset('images/assets/pics/WhatsApp Image 2025-02-20 at 14.30.45.jpeg') }}" 
            alt="Hero Image"
            class="hero-media">
    </div>
    @endif

    <!-- Overlay with text -->
    <div class="absolute inset-0 gradient-overlay flex flex-col justify-center md:justify-end p-8 md:p-12 lg:p-16 text-white">
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-light mb-3 max-w-4xl">A safe and harmonious environment</h1>
        <p class="text-xl md:text-2xl font-light mb-24">#UlinMahoni</p>
    </div>

    <!-- Search Section -->
    @include('components.homepage.search')
</div> 
// Property tabs functionality with location filtering
const propertyTriggers = document.querySelectorAll('.property-tab-trigger');
const locationTriggers = document.querySelectorAll('.location-tab-trigger');
const propertyContents = document.querySelectorAll('.property-tab-content');

// Track current selections — "all" shows every property type combined
let currentPropertyType = 'all'; // Default to all property types
let currentLocation = 'all'; // Default to all locations

// Function to update content visibility based on current selections
function updatePropertyContent() {
    propertyContents.forEach(content => {
        const contentTab = content.getAttribute('data-tab');
        const contentLocation = content.getAttribute('data-location');

        // Show content only if it matches BOTH the selected property type AND location
        if (contentTab === currentPropertyType && contentLocation === currentLocation) {
            content.classList.remove('hidden');
            content.classList.add('active');
        } else {
            content.classList.add('hidden');
            content.classList.remove('active');
        }
    });
}

// Property type tab switching
propertyTriggers.forEach(trigger => {
    trigger.addEventListener('click', () => {
        const tabName = trigger.getAttribute('data-tab');
        currentPropertyType = tabName;

        // Update trigger styles
        propertyTriggers.forEach(t => {
            t.classList.remove('active');
        });
        trigger.classList.add('active');

        // Update content visibility
        updatePropertyContent();
    });
});

// Location tab switching
locationTriggers.forEach(trigger => {
    trigger.addEventListener('click', () => {
        const locationName = trigger.getAttribute('data-location');
        currentLocation = locationName;

        // Update trigger styles
        locationTriggers.forEach(t => {
            t.classList.remove('active');
        });
        trigger.classList.add('active');

        // Update content visibility
        updatePropertyContent();
    });
});

// Initialize with default selections
const activePropertyTab = document.querySelector('.property-tab-trigger.active');
if (activePropertyTab) {
    currentPropertyType = activePropertyTab.getAttribute('data-tab');
}

const activeLocationTab = document.querySelector('.location-tab-trigger.active');
if (activeLocationTab) {
    currentLocation = activeLocationTab.getAttribute('data-location');
}

// Set initial content visibility
updatePropertyContent();

// Nearby properties — geolocation + Haversine distance sorting
// Uses browser GPS to sort property cards by distance and show distance badges
(function() {
    const grid = document.getElementById('nearby-properties-grid');
    const statusEl = document.getElementById('nearby-location-status');
    const statusText = document.getElementById('nearby-location-text');
    if (!grid) return;

    /**
     * Haversine formula — calculates distance in km between two lat/lng points
     * @param {number} lat1 - Latitude of point 1
     * @param {number} lon1 - Longitude of point 1
     * @param {number} lat2 - Latitude of point 2
     * @param {number} lon2 - Longitude of point 2
     * @returns {number} Distance in kilometers
     */
    function haversineKm(lat1, lon1, lat2, lon2) {
        const R = 6371;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat / 2) ** 2 +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLon / 2) ** 2;
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    }

    /**
     * Sort property cards in the DOM by distance from user location
     * and display distance badges on each card
     */
    function sortAndBadge(userLat, userLng) {
        const cards = Array.from(grid.querySelectorAll('.nearby-property-card'));

        // Calculate distance for each card and store it
        cards.forEach(card => {
            const lat = parseFloat(card.dataset.lat);
            const lng = parseFloat(card.dataset.lng);
            if (!isNaN(lat) && !isNaN(lng)) {
                card._distance = haversineKm(userLat, userLng, lat, lng);
            } else {
                // Cards without coordinates go to the end
                card._distance = Infinity;
            }
        });

        // Sort cards by distance (nearest first)
        cards.sort((a, b) => a._distance - b._distance);

        // Re-append cards in sorted order (moves DOM nodes)
        cards.forEach(card => {
            grid.appendChild(card);
            // Show distance badge on each card
            const badge = card.querySelector('.nearby-distance');
            if (badge && card._distance !== Infinity) {
                const dist = card._distance;
                badge.textContent = dist < 1
                    ? Math.round(dist * 1000) + ' m'
                    : dist.toFixed(1) + ' km';
                badge.classList.remove('hidden');
            }
        });
    }

    // Request browser geolocation
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            // Success — sort cards by distance and show badges
            function(position) {
                sortAndBadge(position.coords.latitude, position.coords.longitude);
            },
            // Error / denied — show fallback hint, keep default order
            function() {
                if (statusEl && statusText) {
                    statusText.textContent = '{{ __("homepage.subtitles.enable_location") }}';
                    statusEl.classList.remove('hidden');
                }
            },
            { enableHighAccuracy: false, timeout: 8000, maximumAge: 300000 }
        );
    }
})();

// Video functionality
const video = document.getElementById('heroVideo');
const playPauseBtn = document.getElementById('playPauseBtn');
const playPauseIcon = document.getElementById('playPauseIcon');

if (video && playPauseBtn && playPauseIcon) {
    let isPlaying = true;

    video.play().catch(error => {
        console.error("Video autoplay failed:", error);
        isPlaying = false;
        playPauseIcon.classList.remove('fa-pause');
        playPauseIcon.classList.add('fa-play');
    });

    playPauseBtn.addEventListener('click', function() {
        if (isPlaying) {
            video.pause();
            playPauseIcon.classList.remove('fa-pause');
            playPauseIcon.classList.add('fa-play');
        } else {
            video.play();
            playPauseIcon.classList.remove('fa-play');
            playPauseIcon.classList.add('fa-pause');
        }
        isPlaying = !isPlaying;
    });
} 
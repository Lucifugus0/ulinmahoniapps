// Property tabs functionality with location filtering
const propertyTriggers = document.querySelectorAll('.property-tab-trigger');
const locationTriggers = document.querySelectorAll('.location-tab-trigger');
const propertyContents = document.querySelectorAll('.property-tab-content');

// Track current selections
let currentPropertyType = 'kos'; // Default to kos
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

// Area tabs functionality
const areaTriggers = document.querySelectorAll('.area-tab-trigger');
const areaContents = document.querySelectorAll('.area-tab-content');

areaTriggers.forEach(trigger => {
    trigger.addEventListener('click', () => {
        const tabName = trigger.getAttribute('data-tab');
        
        // Update trigger styles
        areaTriggers.forEach(t => {
            t.classList.remove('text-teal-600', 'border-b-2', 'border-teal-600');
            t.classList.add('text-gray-500');
        });
        trigger.classList.remove('text-gray-500');
        trigger.classList.add('text-teal-600', 'border-b-2', 'border-teal-600');
        
        // Update content visibility
        areaContents.forEach(content => {
            if (content.getAttribute('data-tab') === tabName) {
                content.classList.remove('hidden');
                content.classList.add('active');
            } else {
                content.classList.add('hidden');
                content.classList.remove('active');
            }
        });
    });
});

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
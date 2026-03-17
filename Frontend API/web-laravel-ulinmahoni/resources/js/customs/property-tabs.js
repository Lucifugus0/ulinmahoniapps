// Property Tabs Functionality with Location Filtering
document.addEventListener('DOMContentLoaded', function() {
    const propertyTabTriggers = document.querySelectorAll('.property-tab-trigger');
    const locationTabTriggers = document.querySelectorAll('.location-tab-trigger');
    const tabContents = document.querySelectorAll('.property-tab-content');

    // Track current selections
    let currentPropertyType = 'kos'; // Default to kos
    let currentLocation = 'jakarta'; // Default to jakarta

    // Function to update content visibility based on current selections
    function updateContent() {
        tabContents.forEach(content => {
            const contentTab = content.dataset.tab;
            const contentLocation = content.dataset.location;

            // Show content only if it matches BOTH the selected property type AND location
            if (contentTab === currentPropertyType && contentLocation === currentLocation) {
                content.classList.add('active');
                content.classList.remove('hidden');
            } else {
                content.classList.remove('active');
                content.classList.add('hidden');
            }
        });
    }

    // Function to switch property type tab
    function switchPropertyTab(tabId) {
        currentPropertyType = tabId;

        // Update property tab triggers styling
        propertyTabTriggers.forEach(trigger => {
            if (trigger.dataset.tab === tabId) {
                trigger.classList.add('active');
            } else {
                trigger.classList.remove('active');
            }
        });

        // Update content visibility
        updateContent();
    }

    // Function to switch location tab
    function switchLocationTab(locationId) {
        currentLocation = locationId;

        // Update location tab triggers styling
        locationTabTriggers.forEach(trigger => {
            if (trigger.dataset.location === locationId) {
                trigger.classList.add('active');
            } else {
                trigger.classList.remove('active');
            }
        });

        // Update content visibility
        updateContent();
    }

    // Add click event listeners to property tab triggers
    propertyTabTriggers.forEach(trigger => {
        trigger.addEventListener('click', function() {
            const tabId = this.dataset.tab;
            switchPropertyTab(tabId);
        });
    });

    // Add click event listeners to location tab triggers
    locationTabTriggers.forEach(trigger => {
        trigger.addEventListener('click', function() {
            const locationId = this.dataset.location;
            switchLocationTab(locationId);
        });
    });

    // Initialize with default selections
    // Check which property tab is initially active
    const activePropertyTab = document.querySelector('.property-tab-trigger.active');
    if (activePropertyTab) {
        currentPropertyType = activePropertyTab.dataset.tab;
    }

    // Check which location tab is initially active
    const activeLocationTab = document.querySelector('.location-tab-trigger.active');
    if (activeLocationTab) {
        currentLocation = activeLocationTab.dataset.location;
    }

    // Set initial content visibility
    updateContent();
});

/**
 * Date Filter Persistence Utility
 * Stores and retrieves date filter values using localStorage
 */

const DateFilterPersistence = {
    /**
     * Get the storage key for the current page
     * @param {string} pageIdentifier - Unique identifier for the page
     * @returns {string}
     */
    getStorageKey: function(pageIdentifier) {
        return `date_filter_${pageIdentifier}`;
    },

    /**
     * Save date filter to localStorage
     * @param {string} pageIdentifier - Unique identifier for the page
     * @param {string} startDate - Start date in YYYY-MM-DD format
     * @param {string} endDate - End date in YYYY-MM-DD format
     */
    save: function(pageIdentifier, startDate, endDate) {
        const data = {
            start_date: startDate,
            end_date: endDate,
            timestamp: new Date().getTime()
        };
        localStorage.setItem(this.getStorageKey(pageIdentifier), JSON.stringify(data));
    },

    /**
     * Load date filter from localStorage
     * @param {string} pageIdentifier - Unique identifier for the page
     * @returns {object|null} Object with start_date and end_date, or null if not found
     */
    load: function(pageIdentifier) {
        const stored = localStorage.getItem(this.getStorageKey(pageIdentifier));
        if (stored) {
            try {
                return JSON.parse(stored);
            } catch (e) {
                console.error('Error parsing stored date filter:', e);
                return null;
            }
        }
        return null;
    },

    /**
     * Clear date filter from localStorage
     * @param {string} pageIdentifier - Unique identifier for the page
     */
    clear: function(pageIdentifier) {
        localStorage.removeItem(this.getStorageKey(pageIdentifier));
    },

    /**
     * Clear all stored date filters for booking pages
     */
    clearAllBookingFilters: function() {
        const bookingPages = ['allbookings', 'pending', 'newreservations', 'checkin', 'checkout', 'completed', 'changerooms'];
        bookingPages.forEach(page => {
            this.clear(page);
        });
    },

    /**
     * Initialize flatpickr with persistence
     * @param {string} pageIdentifier - Unique identifier for the page
     * @param {object} options - Flatpickr options (includes disablePersistence to ignore stored dates)
     * @returns {object} Flatpickr instance
     */
    initFlatpickr: function(pageIdentifier, options = {}) {
        const self = this;

        // If disablePersistence is true, clear ALL booking stored dates
        if (options.disablePersistence) {
            this.clearAllBookingFilters();
        }

        const stored = options.disablePersistence ? null : this.load(pageIdentifier);

        // Determine initial dates - only use stored dates if persistence is enabled
        let initialDates = [];

        if (stored && stored.start_date && stored.end_date) {
            // Use stored dates if available
            initialDates = [new Date(stored.start_date), new Date(stored.end_date)];

            // Set hidden input values from stored dates
            const startDateEl = document.getElementById('start_date');
            if (startDateEl) {
                startDateEl.value = stored.start_date;
            }
            const endDateEl = document.getElementById('end_date');
            if (endDateEl) {
                endDateEl.value = stored.end_date;
            }
        }
        // No default dates - show all data by default

        // Merge default options with user options
        const flatpickrOptions = {
            mode: "range",
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "j M Y",
            allowInput: true,
            static: true,
            monthSelectorType: 'static',
            defaultDate: initialDates.length > 0 ? initialDates : null,
            minDate: options.minDate || null, // No min date restriction
            maxDate: options.maxDate || null, // No max date restriction
            locale: {
                rangeSeparator: ' to '
            },
            onOpen: function(selectedDates, dateStr, instance) {
                if (options.onOpen) {
                    options.onOpen(selectedDates, dateStr, instance);
                }
            },
            onChange: function(selectedDates, dateStr, instance) {
                if (selectedDates.length > 0) {
                    const startDate = selectedDates[0];
                    const endDate = selectedDates[1] || selectedDates[0];

                    // Format and save dates
                    const formattedStart = self.formatDate(startDate);
                    const formattedEnd = self.formatDate(endDate);

                    const startDateEl = document.getElementById('start_date');
                    const endDateEl = document.getElementById('end_date');
                    if (startDateEl) startDateEl.value = formattedStart;
                    if (endDateEl) endDateEl.value = formattedEnd;

                    // Save to localStorage (skip if persistence is disabled)
                    if (!options.disablePersistence) {
                        self.save(pageIdentifier, formattedStart, formattedEnd);
                    }

                    // Call custom onChange if provided
                    if (options.onChange) {
                        options.onChange(selectedDates, dateStr, instance);
                    }
                }
            },
            onClose: function(selectedDates, dateStr, instance) {
                if (selectedDates.length === 0) {
                    const startDateEl = document.getElementById('start_date');
                    const endDateEl = document.getElementById('end_date');
                    if (startDateEl) startDateEl.value = '';
                    if (endDateEl) endDateEl.value = '';
                    if (!options.disablePersistence) {
                        self.clear(pageIdentifier);
                    }
                }

                // Call custom onClose if provided
                if (options.onClose) {
                    options.onClose(selectedDates, dateStr, instance);
                }
            }
        };

        return flatpickr("#date_picker", flatpickrOptions);
    },

    /**
     * Format date to YYYY-MM-DD
     * @param {Date} date
     * @returns {string}
     */
    formatDate: function(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }
};

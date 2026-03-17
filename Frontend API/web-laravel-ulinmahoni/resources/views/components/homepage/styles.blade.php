<style>


/* Base styles */
    :root {
      --teal-600: #0d9488;
      --teal-700: #0f766e;
      --red-500: #ef4444;
      --red-700: #b91c1c;
    }
    
    body {
      background: linear-gradient(to bottom, #f8f7f4 0%, #f8f7f4 40%, #efe9dc 100%);
      min-height: 100vh;
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
    }

    /* Hero styles */
    .hero-section {
      position: relative;
      width: 100%;
      height: 600px;
      background-color: #111827;
    }

    @media (min-width: 768px) {
      .hero-section {
        height: 400px;
      }
    }

    @media (min-width: 1024px) {
      .hero-section {
        height: 600px;
      }
    }

    .hero-content {
      position: relative;
      width: 100%;
      height: 100%;
    }

    .hero-media {
      position: absolute;
      inset: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    /* Search bar overlapping bottom of hero — moved higher so it sits mostly inside the hero */
    .search-section {
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      transform: translateY(-20%);
      z-index: 30;
    }

    .search-container {
      max-width: 80rem;
      margin: 0 auto;
      padding: 0 1rem;
    }

    /* Search box: 50% transparent with backdrop blur */
    .search-box {
      background-color: rgba(255, 255, 255, 0.5);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border-radius: 1rem;
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
      padding: 1.5rem;
    }

    /* Minimal margin below hero since search bar is inside hero */
    main > section:first-of-type {
      margin-top: 0;
    }

    @media (max-width: 767px) {
      main > section:first-of-type {
        margin-top: 0;
      }
    }

/* Global styles */
.bg-teal-600 { background-color: var(--teal-600); }
.bg-teal-700 { background-color: var(--teal-700); }
.text-teal-600 { color: var(--teal-600); }
.hover\:bg-teal-700:hover { background-color: var(--teal-700); }
.bg-red-500 { background-color: var(--red-500); }
.bg-red-700 { background-color: var(--red-700); }

/* Component styles */
.property-card {
    transition: transform 0.3s ease;
}

.property-card:hover .card-image {
    transform: scale(1.05);
}

.card-image {
    transition: transform 0.5s ease;
}

.tab-trigger {
    position: relative;
}

.tab-trigger.active {
    border-bottom: 2px solid var(--teal-600);
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.gradient-overlay {
    background: linear-gradient(to top, rgba(0,0,0,0.6), transparent);
}

.feature-icon {
    width: 40px;
    height: 40px;
    background-color: #f3f4f6;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 0.25rem;
}

/* Section styles */
/* Reduced top padding to bring content closer to search bar */
.section-light {
    background-color: transparent;
    padding: 1.5rem 0;
}

.section-dark {
    background-color: transparent;
    padding: 4rem 0;
}

.section-container {
    max-width: 80rem;
    margin: 0 auto;
    padding: 0 1rem;  /* Changed from 1.5rem to match search bar */
}

.section-title {
    text-align: center;
    margin-bottom: 3rem;
}

.section-title h2 {
    font-size: 2.25rem;
    font-weight: 300;
    color: #1a1a1a;
    margin-bottom: 0.5rem;
}

.section-title .divider {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
}

.section-title .divider-line {
    width: 3rem;
    height: 1px;
    background-color: #d1d5db;
}

.section-title .divider-text {
    color: #0d9488;
    font-style: italic;
}

/* ========================================
   Dark Mode Overrides (class-based)
   ======================================== */
html.dark body {
    background: linear-gradient(to bottom, #111827 0%, #111827 40%, #1f2937 100%) !important;
    color: #e5e7eb;
}

/* Header: 50% transparent with backdrop blur, positioned over page content */
.site-header {
    background-color: rgba(255, 255, 255, 0.5) !important;
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
}

html.dark .site-header {
    background-color: rgba(17, 24, 39, 0.5) !important;
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.3);
}

/* Spacer for pages without a full-height hero — pushes content below fixed header */
.header-spacer {
    height: 4.5rem; /* matches header height */
}

/* Search box container: 50% transparent with backdrop blur */
html.dark .search-box {
    background-color: rgba(31, 41, 55, 0.5) !important;
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    color: #e5e7eb;
}

html.dark .search-box input,
html.dark .search-box select {
    background-color: #374151 !important;
    color: #e5e7eb !important;
    border-color: #4b5563 !important;
}

/* Fix placeholder text color in dark mode for check-in/check-out date inputs */
html.dark .search-box input::placeholder {
    color: #e5e7eb !important;
    opacity: 1 !important;
}

html.dark .search-box input::-webkit-input-placeholder {
    color: #e5e7eb !important;
    opacity: 1 !important;
}

html.dark .section-title h2 {
    color: #f3f4f6;
}

html.dark .section-title .divider-line {
    background-color: #4b5563;
}

html.dark .property-card,
html.dark .bg-white {
    background-color: #1f2937 !important;
    color: #e5e7eb;
}

/* Body background override for pages with inline styles */
html.dark body {
    background-color: #111827 !important;
    color: #e5e7eb !important;
}

html.dark .text-gray-600 { color: #d1d5db !important; }
html.dark .text-gray-700 { color: #e5e7eb !important; }
html.dark .text-gray-800 { color: #f3f4f6 !important; }
html.dark .text-gray-900 { color: #f9fafb !important; }
html.dark .text-gray-500 { color: #9ca3af !important; }
html.dark .text-gray-400 { color: #9ca3af !important; }

html.dark .bg-gray-50 { background-color: #1f2937 !important; }
html.dark .bg-gray-100 { background-color: #1f2937 !important; }
html.dark .bg-gray-200 { background-color: #374151 !important; }
html.dark .bg-blue-50 { background-color: #1e3a5f !important; }

html.dark .border-gray-100 { border-color: #374151 !important; }
html.dark .border-gray-200 { border-color: #4b5563 !important; }
html.dark .border-gray-300 { border-color: #4b5563 !important; }

/* Form inputs in dark mode — for pages using CDN Tailwind */
html.dark input,
html.dark select,
html.dark textarea {
    background-color: #374151 !important;
    color: #e5e7eb !important;
    border-color: #4b5563 !important;
}
html.dark input::placeholder,
html.dark select::placeholder,
html.dark textarea::placeholder {
    color: #9ca3af !important;
}
html.dark .border-gray-300 { border-color: #4b5563 !important; }

html.dark .hover\:bg-gray-50:hover { background-color: #374151 !important; }
html.dark .hover\:bg-gray-100:hover { background-color: #374151 !important; }

html.dark .shadow-lg { box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3), 0 4px 6px -2px rgba(0, 0, 0, 0.2) !important; }
html.dark .shadow-md { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3), 0 2px 4px -1px rgba(0, 0, 0, 0.2) !important; }

html.dark .ring-black { --tw-ring-color: rgba(75, 85, 99, 1) !important; }

html.dark .feature-icon {
    background-color: #374151;
}

html.dark .tab-trigger {
    color: #d1d5db;
}

html.dark .tab-trigger:hover {
    color: #f3f4f6;
}

/* Info pages with video background: make containers 50% transparent to match header */
.video-page .bg-white {
    background-color: rgba(255, 255, 255, 0.5) !important;
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
}
.video-page .bg-gray-50 {
    background-color: rgba(249, 250, 251, 0.5) !important;
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
}
html.dark .video-page .bg-white {
    background-color: rgba(17, 24, 39, 0.5) !important;
}
html.dark .video-page .bg-gray-50 {
    background-color: rgba(31, 41, 55, 0.5) !important;
}

/* Auth pages (login/register): push login box below fixed header, 50% transparent */
.login-container {
    padding-top: 5rem !important; /* clear fixed header */
}
.login-box {
    background: rgba(255, 255, 255, 0.5) !important;
    backdrop-filter: blur(12px) !important;
    -webkit-backdrop-filter: blur(12px) !important;
}
html.dark .login-box {
    background: rgba(31, 41, 55, 0.5) !important;
    color: #e5e7eb;
}
html.dark .login-box input {
    background-color: #374151 !important;
    color: #e5e7eb !important;
    border-color: #4b5563 !important;
}
html.dark .login-box input::placeholder {
    color: #9ca3af !important;
}
html.dark .login-box label {
    color: #d1d5db !important;
}
html.dark .login-box h1,
html.dark .login-box h2,
html.dark .login-box h3,
html.dark .login-box p,
html.dark .login-box span {
    color: #e5e7eb !important;
}
html.dark .login-box a {
    color: #5eead4 !important;
}

/* Property Cards - Dark Mode */
html.dark .property-card {
    background: #1f2937 !important;
    border-color: #374151 !important;
}

html.dark .property-card:hover {
    box-shadow: 0 0.25rem 0.375rem rgba(0, 0, 0, 0.3) !important;
}

html.dark .property-card-title {
    color: #f3f4f6 !important;
}

html.dark .property-card:hover .property-card-title {
    color: #2dd4bf !important;
}

html.dark .property-card-gender {
    color: #e5e7eb !important;
    border-color: #4b5563 !important;
}

html.dark .property-card-location {
    color: #9ca3af !important;
}

html.dark .property-card-location i {
    color: #6b7280 !important;
}

html.dark .property-card-price-value {
    color: #f3f4f6 !important;
}

html.dark .property-card-price-period {
    color: #9ca3af !important;
}

html.dark .property-card-view-btn {
    color: #f3f4f6 !important;
}

html.dark .property-card-view-btn:hover {
    color: #2dd4bf !important;
}

html.dark .property-card-price-section {
    border-top-color: #374151 !important;
}

html.dark .property-card-image {
    background-color: #374151 !important;
}

html.dark .property-card-no-image {
    color: #6b7280 !important;
}

html.dark .property-cards-empty-content {
    background: #1f2937 !important;
    border-color: #374151 !important;
}

html.dark .property-cards-empty-content p {
    color: #9ca3af !important;
}

html.dark .property-cards-empty-icon {
    color: #4b5563 !important;
}

/* Tabs - Dark Mode */
html.dark .property-tabs-wrapper {
    border-bottom-color: #374151 !important;
}

html.dark .property-tabs-wrapper .property-tab-trigger {
    color: #9ca3af !important;
}

html.dark .property-tabs-wrapper .property-tab-trigger:hover {
    color: #e5e7eb !important;
}

html.dark .property-tabs-wrapper .property-tab-trigger.active,
html.dark .property-tabs-wrapper .property-tab-trigger.text-teal-600 {
    color: #2dd4bf !important;
}

html.dark .location-tabs-wrapper {
    border-bottom-color: #374151 !important;
}

html.dark .location-tabs-wrapper .location-tab-trigger {
    color: #9ca3af !important;
}

html.dark .location-tabs-wrapper .location-tab-trigger:hover {
    color: #e5e7eb !important;
}

html.dark .location-tabs-wrapper .location-tab-trigger.active {
    color: #2dd4bf !important;
}

/* Promo/Area Cards - Dark Mode */
html.dark .promo-card,
html.dark .area-card {
    background: #1f2937 !important;
    border-color: #374151 !important;
}

html.dark .promo-card h3,
html.dark .area-card h3 {
    color: #f3f4f6 !important;
}

html.dark .promo-card p,
html.dark .area-card p {
    color: #9ca3af !important;
}

/* Footer - Dark Mode */
html.dark footer,
html.dark .site-footer {
    background-color: #111827 !important;
    color: #d1d5db !important;
}

/* Area Tabs - Dark Mode */
html.dark .area-tab-trigger {
    color: #9ca3af !important;
}

html.dark .area-tab-trigger:hover {
    color: #e5e7eb !important;
}

html.dark .area-tab-trigger.text-teal-600,
html.dark .area-tab-trigger[class*="border-teal"] {
    color: #2dd4bf !important;
}

/* Promo Section - Dark Mode */
html.dark .promo-image-container {
    background-color: #374151 !important;
}

html.dark .promo-no-image {
    background-color: #374151 !important;
    color: #6b7280 !important;
}

html.dark .promo-empty {
    color: #9ca3af !important;
}

/* Browse All Button - Dark Mode */
html.dark .browse-all-btn {
    background-color: #0d9488 !important;
}

html.dark .browse-all-btn:hover {
    background-color: #0f766e !important;
}
</style>
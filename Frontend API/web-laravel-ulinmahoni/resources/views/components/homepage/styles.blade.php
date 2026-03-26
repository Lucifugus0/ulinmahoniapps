<!-- Liquid Glass Design System — Apple-inspired glassmorphism styles
     Replaces the previous flat/material design with translucent, layered glass panels,
     soft gradients, and fluid animations. -->
<style>
/* ========================================
   CSS Custom Properties — Glass Design Tokens
   ======================================== */
:root {
  /* Glass surface colors — high transparency for prominent frosted glass effect */
  --glass-bg: rgba(255, 255, 255, 0.18);
  --glass-bg-hover: rgba(255, 255, 255, 0.28);
  --glass-bg-strong: rgba(255, 255, 255, 0.30);
  --glass-border: rgba(255, 255, 255, 0.35);
  --glass-border-subtle: rgba(255, 255, 255, 0.18);
  --glass-shadow: 0 8px 32px rgba(0, 0, 0, 0.10);
  --glass-shadow-hover: 0 16px 48px rgba(0, 0, 0, 0.15);
  --glass-blur: blur(24px);
  --glass-blur-strong: blur(48px);

  /* Brand accent — brighter teal in light mode for visibility on light glass */
  --accent: #2dd4bf;
  --accent-hover: #14b8a6;
  --accent-glass: rgba(45, 212, 191, 0.15);
  --accent-glass-border: rgba(45, 212, 191, 0.3);

  /* Typography — darker values for readability on translucent glass backgrounds */
  --text-primary: #0f0f1a;
  --text-secondary: #2d2d42;
  --text-tertiary: #555570;

  /* Spacing */
  --section-padding: 4rem 0;
  --container-max: 80rem;
  --radius-sm: 0.75rem;
  --radius-md: 1.25rem;
  --radius-lg: 1.75rem;
  --radius-xl: 2rem;
}

/* ========================================
   Base Styles — Fluid Background
   ======================================== */
/* Scoped to .liquid-glass-page so non-homepage pages keep their own body styles */
body.liquid-glass-page {
  /* Soft gradient that shifts subtly like a liquid surface */
  background: linear-gradient(135deg, #f0f4f8 0%, #e8eef5 25%, #f5f0eb 50%, #eef2f7 75%, #f0f4f8 100%);
  background-size: 400% 400%;
  animation: liquidShift 20s ease infinite;
  min-height: 100vh;
  font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'SF Pro Text', 'Helvetica Neue', 'Segoe UI', Roboto, sans-serif;
  color: var(--text-primary);
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

/* Subtle background animation — mimics light refraction on glass */
@keyframes liquidShift {
  0%   { background-position: 0% 50%; }
  50%  { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}

/* ========================================
   Hero Section — Full-bleed with Depth
   ======================================== */
.hero-section {
  position: relative;
  width: 100%;
  height: 650px;
  background-color: #0f172a;
  overflow: hidden;
}

@media (min-width: 768px) {
  .hero-section { height: 450px; }
}

@media (min-width: 1024px) {
  .hero-section { height: 650px; }
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
  /* Slight scale for parallax-like depth */
  transform: scale(1.02);
}

/* Gradient overlay — deeper, more cinematic for glass contrast */
.gradient-overlay {
  background: linear-gradient(
    to top,
    rgba(0, 0, 0, 0.7) 0%,
    rgba(0, 0, 0, 0.3) 40%,
    transparent 70%
  );
}

/* ========================================
   Search Bar — Floating Glass Panel
   ======================================== */
.search-section {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  transform: translateY(-15%);
  z-index: 30;
}

.search-container {
  max-width: var(--container-max);
  margin: 0 auto;
  padding: 0 1rem;
}

/* Glass search box — the primary glass surface */
.search-box {
  background: var(--glass-bg-strong);
  backdrop-filter: var(--glass-blur-strong);
  -webkit-backdrop-filter: var(--glass-blur-strong);
  border: 1px solid var(--glass-border);
  border-radius: var(--radius-xl);
  box-shadow:
    var(--glass-shadow),
    inset 0 1px 0 rgba(255, 255, 255, 0.6);
  padding: 1.5rem 2rem;
  transition: all 0.4s cubic-bezier(0.22, 1, 0.36, 1);
}

.search-box:hover,
.search-box:focus-within {
  background: var(--glass-bg-hover);
  box-shadow:
    var(--glass-shadow-hover),
    inset 0 1px 0 rgba(255, 255, 255, 0.7);
  transform: translateY(-2px);
}

/* Glass form inputs — more see-through */
.search-box select,
.search-box input {
  background: rgba(255, 255, 255, 0.25) !important;
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.25) !important;
  border-radius: var(--radius-md) !important;
  color: var(--text-primary) !important;
  font-weight: 400;
  transition: all 0.3s ease;
}

.search-box select:focus,
.search-box input:focus {
  background: rgba(255, 255, 255, 0.35) !important;
  border-color: var(--accent) !important;
  box-shadow: 0 0 0 3px var(--accent-glass) !important;
  outline: none;
}

/* Search button — glass accent */
.search-box button[type="submit"] {
  background: linear-gradient(135deg, var(--accent), var(--accent-hover)) !important;
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: var(--radius-md) !important;
  box-shadow: 0 4px 15px rgba(14, 165, 160, 0.3);
  font-weight: 500;
  letter-spacing: 0.02em;
  transition: all 0.3s cubic-bezier(0.22, 1, 0.36, 1);
}

.search-box button[type="submit"]:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 20px rgba(14, 165, 160, 0.4);
}

/* Brighten search bar icons and placeholder text for glass readability */
.search-box .text-gray-400 {
  color: #555570 !important;
}
.search-box select,
.search-box input {
  color: #0f0f1a !important;
}
.search-box select option {
  color: #0f0f1a;
  background: white;
}
html.dark .search-box .text-gray-400 {
  color: #a0a0b8 !important;
}
html.dark .search-box select option {
  color: #f0f0f5;
  background: #1f2937;
}

/* ========================================
   Section Layout
   ======================================== */
main > section:first-of-type {
  margin-top: 0;
}

@media (max-width: 767px) {
  main > section:first-of-type { margin-top: 0; }
}

/* Teal utility classes */
.bg-teal-600 { background-color: var(--accent); }
.bg-teal-700 { background-color: var(--accent-hover); }
.text-teal-600 { color: var(--accent); }
.hover\:bg-teal-700:hover { background-color: var(--accent-hover); }

/* ========================================
   Property Cards — Glass Cards
   ======================================== */
.property-card {
  background: var(--glass-bg);
  backdrop-filter: var(--glass-blur);
  -webkit-backdrop-filter: var(--glass-blur);
  border: 1px solid var(--glass-border);
  border-radius: var(--radius-lg);
  box-shadow: var(--glass-shadow);
  transition: all 0.4s cubic-bezier(0.22, 1, 0.36, 1);
  overflow: hidden;
}

.property-card:hover {
  transform: translateY(-6px);
  box-shadow: var(--glass-shadow-hover);
  background: var(--glass-bg-hover);
}

.property-card:hover .card-image {
  transform: scale(1.05);
}

.card-image {
  transition: transform 0.6s cubic-bezier(0.22, 1, 0.36, 1);
}

/* ========================================
   Tabs — Glass Pill Tabs
   ======================================== */
.tab-trigger {
  position: relative;
}

.tab-trigger.active {
  border-bottom: 2px solid var(--accent);
}

.tab-content {
  display: none;
}

.tab-content.active {
  display: block;
}

/* ========================================
   Section Styles — Glass Containers
   ======================================== */
.section-light {
  background-color: transparent;
  padding: 2rem 0;
}

.section-dark {
  background-color: transparent;
  padding: 4rem 0;
}

.section-container {
  max-width: var(--container-max);
  margin: 0 auto;
  padding: 0 1rem;
}

.section-title {
  text-align: center;
  margin-bottom: 3rem;
}

.section-title h2,
.section-title h3 {
  font-size: 2.25rem;
  font-weight: 300;
  letter-spacing: -0.02em;
  color: var(--text-primary);
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
  background: linear-gradient(to right, transparent, #d1d5db, transparent);
}

.section-title .divider-text {
  color: var(--accent);
  font-style: italic;
  font-weight: 300;
}

.feature-icon {
  width: 40px;
  height: 40px;
  background: var(--glass-bg);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  border: 1px solid var(--glass-border-subtle);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 0.25rem;
}

/* ========================================
   Header — Floating Glass Bar
   ======================================== */
.site-header {
  position: fixed !important;
  top: 0 !important;
  left: 0 !important;
  right: 0 !important;
  z-index: 9999 !important;
  background: var(--glass-bg) !important;
  backdrop-filter: var(--glass-blur-strong) !important;
  -webkit-backdrop-filter: var(--glass-blur-strong) !important;
  border-bottom: 1px solid var(--glass-border-subtle);
  box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05) !important;
  transition: background 0.3s ease, box-shadow 0.3s ease;
  /* Force own compositing layer so fixed positioning is never broken by parent filters/transforms */
  will-change: transform;
  isolation: isolate;
}
/* Light mode header text — dark for readability on light glass */
.site-header a,
.site-header span,
.site-header button {
  color: #1f2937 !important;
}
.site-header a:hover,
.site-header button:hover {
  color: #000000 !important;
}

/* Spacer for non-hero pages */
.header-spacer {
  height: 4.5rem;
}

/* ========================================
   Footer — Glass-tinted Dark
   ======================================== */
footer, .site-footer {
  background: linear-gradient(180deg, #1a1a2e 0%, #16162a 100%) !important;
  position: relative;
}

footer::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 1px;
  background: linear-gradient(to right, transparent, rgba(255, 255, 255, 0.15), transparent);
}

/* ========================================
   Auth / Login Pages — Glass Panels
   ======================================== */
.login-container {
  padding-top: 5rem !important;
}

.login-box {
  background: var(--glass-bg-strong) !important;
  backdrop-filter: var(--glass-blur-strong) !important;
  -webkit-backdrop-filter: var(--glass-blur-strong) !important;
  border: 1px solid var(--glass-border);
  border-radius: var(--radius-xl);
}

/* Auth glass panels over dark video backgrounds — white text for contrast */
/* Placeholder and icon text boosted for readability on glass */
.login-container .login-box input::placeholder {
  color: rgba(255, 255, 255, 0.85) !important;
  opacity: 1 !important;
}
.login-container .login-box .text-gray-400,
.login-container .login-box .text-gray-500,
.login-container .login-box .text-gray-600 {
  color: rgba(255, 255, 255, 0.85) !important;
}
.login-container .login-box .text-gray-900,
.login-container .login-box .text-gray-800,
.login-container .login-box .text-gray-700 {
  color: #ffffff !important;
}
.login-container .login-box label {
  color: rgba(255, 255, 255, 0.85) !important;
}
/* Input fields: stronger glass background so text is readable */
.login-container .login-box input,
.login-container .login-box select {
  color: #ffffff !important;
  border-color: rgba(255, 255, 255, 0.35) !important;
  background: rgba(255, 255, 255, 0.18) !important;
}
.login-container .login-box input:focus,
.login-container .login-box select:focus {
  border-color: rgba(255, 255, 255, 0.6) !important;
  background: rgba(255, 255, 255, 0.25) !important;
}
.login-container .login-box h1,
.login-container .login-box h2,
.login-container .login-box h3 {
  color: #ffffff !important;
}
.login-container .login-box p {
  color: rgba(255, 255, 255, 0.8) !important;
}
.login-container .login-box a:not(.bg-teal-600) {
  color: #5eead4 !important;
}
.login-container .login-box input[type="checkbox"] {
  background: rgba(255, 255, 255, 0.15) !important;
  border-color: rgba(255, 255, 255, 0.4) !important;
}
.login-container .login-box .text-sm.text-gray-600 {
  color: rgba(255, 255, 255, 0.7) !important;
}

/* ========================================
   Video/Info Pages — Transparent Glass Panels
   ======================================== */
/* Video pages: disable body animation/gradient that breaks position:fixed */
body.video-page {
  background: transparent !important;
  animation: none !important;
}
html.dark body.video-page {
  background: transparent !important;
  animation: none !important;
}

/* Glass effect only for card-level elements, not full-width layout containers */
.video-page .bg-white:not(body):not(main):not(section):not(.min-h-screen):not(.flex-col) {
  background: var(--glass-bg) !important;
  backdrop-filter: var(--glass-blur);
  -webkit-backdrop-filter: var(--glass-blur);
  border: 1px solid var(--glass-border-subtle);
}

.video-page .bg-gray-50 {
  background: rgba(249, 250, 251, 0.4) !important;
  backdrop-filter: var(--glass-blur);
  -webkit-backdrop-filter: var(--glass-blur);
}

/* Light mode text brightening — scoped to homepage glass sections only,
   so property detail and other pages keep their default Tailwind text colors */
.hero-section ~ section .text-gray-400 { color: #555570; }
.hero-section ~ section .text-gray-500 { color: #4a4a68; }

/* ========================================
   DARK MODE — Inverted Glass
   ======================================== */
html.dark {
  --glass-bg: rgba(255, 255, 255, 0.06);
  --glass-bg-hover: rgba(255, 255, 255, 0.12);
  --glass-bg-strong: rgba(255, 255, 255, 0.10);
  --glass-border: rgba(255, 255, 255, 0.12);
  --glass-border-subtle: rgba(255, 255, 255, 0.08);
  --glass-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
  --glass-shadow-hover: 0 16px 48px rgba(0, 0, 0, 0.5);
  /* Brand accent — more subdued teal in dark mode to avoid being overly bright */
  --accent: #0ea5a0;
  --accent-hover: #0d9488;
  --accent-glass: rgba(14, 165, 160, 0.15);
  --accent-glass-border: rgba(14, 165, 160, 0.3);
  /* Typography — brighter values for readability on dark glass backgrounds */
  --text-primary: #f5f5fa;
  --text-secondary: #d0d0e0;
  --text-tertiary: #a0a0b8;
}

html.dark body.liquid-glass-page {
  background: linear-gradient(135deg, #0f0f1a 0%, #141428 25%, #1a1a30 50%, #141428 75%, #0f0f1a 100%) !important;
  background-size: 400% 400%;
  animation: liquidShift 20s ease infinite;
  color: var(--text-primary) !important;
}

/* Golden leaves background — decorative overlay on main content area only.
   Scoped to <main> so it does NOT cover the hero section or header.
   Uses absolute positioning within main, not fixed on the whole viewport. */
main {
  position: relative;
}
main::after {
  content: '';
  position: absolute;
  inset: 0;
  background-image: url('/images/assets/backgrounds/golden-leaves-bg.jpg');
  /* Single image stretched to fill full width and height of content area */
  background-size: 100% 100%;
  background-position: center;
  background-repeat: no-repeat;
  opacity: 0.06;
  z-index: 1;
  pointer-events: none;
}
/* Dark mode — slightly stronger for visibility against dark backgrounds */
html.dark main::after {
  opacity: 0.12;
}
/* Ensure main content sits above the leaf overlay */
main > * {
  position: relative;
  z-index: 2;
}

/* Dark header glass — very transparent */
html.dark .site-header {
  background: rgba(10, 10, 25, 0.35) !important;
  backdrop-filter: var(--glass-blur-strong) !important;
  -webkit-backdrop-filter: var(--glass-blur-strong) !important;
  border-bottom: 1px solid rgba(255, 255, 255, 0.06);
  box-shadow: 0 4px 30px rgba(0, 0, 0, 0.2) !important;
}
/* Dark header text — brighter for readability against glass */
html.dark .site-header a,
html.dark .site-header span,
html.dark .site-header button {
  color: #f3f4f6 !important;
}
html.dark .site-header a:hover,
html.dark .site-header button:hover {
  color: #ffffff !important;
}

/* Header dropdown — frosted glass with high opacity for readability */
.header-dropdown {
  background: rgba(240, 240, 245, 0.85) !important;
  backdrop-filter: var(--glass-blur-strong) !important;
  -webkit-backdrop-filter: var(--glass-blur-strong) !important;
  border: 1px solid rgba(255, 255, 255, 0.40) !important;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12) !important;
}
html.dark .header-dropdown {
  background: rgba(17, 24, 39, 0.85) !important;
  backdrop-filter: var(--glass-blur-strong) !important;
  -webkit-backdrop-filter: var(--glass-blur-strong) !important;
  border: 1px solid rgba(255, 255, 255, 0.08) !important;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4) !important;
}
/* Dropdown text colors */
.header-dropdown a,
.header-dropdown p,
.header-dropdown span {
  color: #374151;
}
.header-dropdown .text-gray-500 { color: #6b7280; }
.header-dropdown .text-red-600 { color: #dc2626; }
html.dark .header-dropdown a,
html.dark .header-dropdown p {
  color: #f3f4f6 !important;
}
html.dark .header-dropdown span { color: #d1d5db !important; }
html.dark .header-dropdown .text-gray-400,
html.dark .header-dropdown .text-gray-500 { color: #9ca3af !important; }
html.dark .header-dropdown .text-red-600,
html.dark .header-dropdown .text-red-400,
html.dark .header-dropdown button.text-red-600 { color: #f87171 !important; }

/* Dark search box glass */
html.dark .search-box {
  background: var(--glass-bg-strong) !important;
  backdrop-filter: var(--glass-blur-strong);
  -webkit-backdrop-filter: var(--glass-blur-strong);
  border-color: var(--glass-border);
  box-shadow: var(--glass-shadow), inset 0 1px 0 rgba(255, 255, 255, 0.05);
}

html.dark .search-box:hover,
html.dark .search-box:focus-within {
  background: var(--glass-bg-hover) !important;
}

html.dark .search-box input,
html.dark .search-box select {
  background: rgba(255, 255, 255, 0.05) !important;
  border-color: rgba(255, 255, 255, 0.08) !important;
  color: var(--text-primary) !important;
}

html.dark .search-box input:focus,
html.dark .search-box select:focus {
  background: rgba(255, 255, 255, 0.08) !important;
  border-color: var(--accent) !important;
  box-shadow: 0 0 0 3px var(--accent-glass) !important;
}

html.dark .search-box input::placeholder {
  color: var(--text-tertiary) !important;
  opacity: 1 !important;
}

/* Dark section titles */
html.dark .section-title h2,
html.dark .section-title h3 {
  color: var(--text-primary);
}

html.dark .section-title .divider-line {
  background: linear-gradient(to right, transparent, #4b5563, transparent);
}

/* Dark property cards — scoped to card-level elements only, not body/main/section containers
   to avoid adding glass borders and transparent backgrounds to full-page layouts */
html.dark .property-card {
  background: var(--glass-bg) !important;
  backdrop-filter: var(--glass-blur);
  -webkit-backdrop-filter: var(--glass-blur);
  border: 1px solid var(--glass-border);
  color: var(--text-primary);
}

html.dark .property-card:hover {
  background: var(--glass-bg-hover) !important;
  box-shadow: var(--glass-shadow-hover);
}

/* Dark text overrides */
html.dark .text-gray-600 { color: var(--text-secondary) !important; }
html.dark .text-gray-700 { color: var(--text-primary) !important; }
html.dark .text-gray-800 { color: var(--text-primary) !important; }
html.dark .text-gray-900 { color: var(--text-primary) !important; }
html.dark .text-gray-500 { color: var(--text-tertiary) !important; }
html.dark .text-gray-400 { color: var(--text-tertiary) !important; }

/* Dark background overrides */
html.dark .bg-gray-50 { background: var(--glass-bg) !important; }
html.dark .bg-gray-100 { background: var(--glass-bg) !important; }
html.dark .bg-gray-200 { background: rgba(255, 255, 255, 0.05) !important; }
html.dark .bg-blue-50 { background: rgba(30, 58, 95, 0.5) !important; }

/* Dark border overrides */
html.dark .border-gray-100 { border-color: var(--glass-border) !important; }
html.dark .border-gray-200 { border-color: var(--glass-border) !important; }
html.dark .border-gray-300 { border-color: var(--glass-border) !important; }

/* Dark form inputs */
html.dark input,
html.dark select,
html.dark textarea {
  background: rgba(255, 255, 255, 0.05) !important;
  color: var(--text-primary) !important;
  border-color: var(--glass-border) !important;
}

html.dark input::placeholder,
html.dark select::placeholder,
html.dark textarea::placeholder {
  color: var(--text-tertiary) !important;
}

/* Dark hover backgrounds */
html.dark .hover\:bg-gray-50:hover { background: rgba(255, 255, 255, 0.05) !important; }
html.dark .hover\:bg-gray-100:hover { background: rgba(255, 255, 255, 0.05) !important; }

/* Dark shadows — deeper for glass depth effect */
html.dark .shadow-lg { box-shadow: var(--glass-shadow) !important; }
html.dark .shadow-md { box-shadow: 0 4px 16px rgba(0, 0, 0, 0.25) !important; }
html.dark .ring-black { --tw-ring-color: rgba(255, 255, 255, 0.1) !important; }

html.dark .feature-icon {
  background: rgba(255, 255, 255, 0.05);
  border-color: var(--glass-border);
}

html.dark .tab-trigger { color: var(--text-secondary); }
html.dark .tab-trigger:hover { color: var(--text-primary); }

/* Dark video pages */
html.dark .video-page .bg-white:not(body):not(main):not(section):not(.min-h-screen):not(.flex-col) {
  background: var(--glass-bg) !important;
  border-color: var(--glass-border);
}
html.dark .video-page .bg-gray-50 {
  background: var(--glass-bg) !important;
}

/* Dark login */
html.dark .login-box {
  background: var(--glass-bg-strong) !important;
  border-color: var(--glass-border);
  color: var(--text-primary);
}
html.dark .login-box input {
  background: rgba(255, 255, 255, 0.05) !important;
  color: var(--text-primary) !important;
  border-color: var(--glass-border) !important;
}
html.dark .login-box input::placeholder { color: var(--text-tertiary) !important; }
html.dark .login-box label { color: var(--text-secondary) !important; }
html.dark .login-box h1,
html.dark .login-box h2,
html.dark .login-box h3,
html.dark .login-box p,
html.dark .login-box span { color: var(--text-primary) !important; }
html.dark .login-box a { color: var(--accent) !important; }

/* Dark property card details */
html.dark .property-card-title { color: var(--text-primary) !important; }
html.dark .property-card:hover .property-card-title { color: var(--accent) !important; }
html.dark .property-card-gender { color: var(--text-primary) !important; border-color: var(--glass-border) !important; }
html.dark .property-card-location { color: var(--text-tertiary) !important; }
html.dark .property-card-location i { color: var(--text-tertiary) !important; }
html.dark .property-card-price-value { color: var(--text-primary) !important; }
html.dark .property-card-price-period { color: var(--text-tertiary) !important; }
html.dark .property-card-view-btn { color: var(--text-primary) !important; }
html.dark .property-card-view-btn:hover { color: var(--accent) !important; }
html.dark .property-card-price-section { border-top-color: var(--glass-border) !important; }
html.dark .property-card-image { background: rgba(255, 255, 255, 0.03) !important; }
html.dark .property-card-no-image { color: var(--text-tertiary) !important; }

html.dark .property-cards-empty-content {
  background: var(--glass-bg) !important;
  border-color: var(--glass-border) !important;
}
html.dark .property-cards-empty-content p { color: var(--text-tertiary) !important; }
html.dark .property-cards-empty-icon { color: var(--text-tertiary) !important; }

/* Dark tabs — targets the combined filter container rows */
html.dark .property-tabs-row .property-tab-trigger { color: var(--text-tertiary) !important; }
html.dark .property-tabs-row .property-tab-trigger:hover { color: var(--text-primary) !important; }
html.dark .property-tabs-row .property-tab-trigger.active,
html.dark .property-tabs-row .property-tab-trigger.text-teal-600 { color: var(--accent) !important; }

html.dark .location-tabs-row .location-tab-trigger { color: var(--text-tertiary) !important; }
html.dark .location-tabs-row .location-tab-trigger:hover { color: var(--text-primary) !important; }
html.dark .location-tabs-row .location-tab-trigger.active { color: var(--accent) !important; }

/* Dark promo/area cards */
html.dark .promo-card,
html.dark .area-card {
  background: var(--glass-bg) !important;
  backdrop-filter: var(--glass-blur);
  -webkit-backdrop-filter: var(--glass-blur);
  border: 1px solid var(--glass-border);
}
html.dark .promo-card h3,
html.dark .area-card h3 { color: var(--text-primary) !important; }
html.dark .promo-card p,
html.dark .area-card p { color: var(--text-tertiary) !important; }

/* Dark footer — deeper glass */
html.dark footer,
html.dark .site-footer {
  background: linear-gradient(180deg, #0a0a18 0%, #08081a 100%) !important;
  color: var(--text-secondary) !important;
}

/* Dark area tabs */
html.dark .area-tab-trigger { color: var(--text-tertiary) !important; }
html.dark .area-tab-trigger:hover { color: var(--text-primary) !important; }
html.dark .area-tab-trigger.text-teal-600,
html.dark .area-tab-trigger[class*="border-teal"] { color: var(--accent) !important; }

/* Dark promo section details */
html.dark .promo-image-container { background: rgba(255, 255, 255, 0.03) !important; }
html.dark .promo-no-image { background: rgba(255, 255, 255, 0.03) !important; color: var(--text-tertiary) !important; }
html.dark .promo-empty { color: var(--text-tertiary) !important; }

/* Dark browse all button */
html.dark .browse-all-btn { background-color: var(--accent) !important; }
html.dark .browse-all-btn:hover { background-color: var(--accent-hover) !important; }
</style>

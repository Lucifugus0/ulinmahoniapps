<style>
        :root {
            --teal-600: #0d9488;
            --teal-700: #0f766e;
            --red-500: #ef4444;
            --red-700: #b91c1c;
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
        .section-light {
            background-color: #f8f7f4;
            padding: 4rem 0;
        }

        .section-dark {
            background-color: #f5f2ea;
            padding: 4rem 0;
        }

        .section-container {
            max-width: 80rem;
            margin: 0 auto;
            padding: 0 1.5rem;
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
            color: var(--teal-600);
            font-style: italic;
        }

        /* Additional styles for property details page */
        .property-image {
            position: relative;
            height: 500px;
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .property-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .property-badge {
            position: absolute;
            top: 1rem;
            left: 1rem;
            background-color: var(--teal-600);
            color: white;
            padding: 0.375rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
        }

        .price-tag {
            background-color: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 1.5rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        .amenity-icon {
            display: inline-flex;
            align-items: center;
            color: #4b5563;
        }

        .amenity-icon svg {
            width: 1.25rem;
            height: 1.25rem;
            margin-right: 0.75rem;
        }

        .action-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        .action-button.primary {
            background-color: var(--teal-600);
            color: white;
        }

        .action-button.primary:hover {
            background-color: var(--teal-700);
        }

        .action-button.secondary {
            border: 1px solid #d1d5db;
            color: #374151;
        }

        .action-button.secondary:hover {
            background-color: #f9fafb;
        }
    </style>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('auth.forgot_password') }} - Ulin Mahoni</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    @include('components.homepage.styles')
</head>
<body class="bg-gray-50">
    @include('components.homepage.header')

    <main class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-lg shadow-lg">
            <div>
                <h2 class="text-4xl font-light text-center text-gray-900 mb-2">{{ __('auth.forgot_password') }}</h2>
                <p class="text-center text-gray-600 text-lg">{{ __('auth.forgot_password_description') }}</p>
            </div>

            <!-- Success Message -->
            <div id="successMessage" class="hidden bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ __('auth.reset_link_sent') }}</span>
            </div>

            <!-- Error Message -->
            <div id="errorMessage" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline" id="errorText"></span>
            </div>

            <form id="forgotPasswordForm" class="mt-8 space-y-6">
                @csrf
                <div class="rounded-md shadow-sm space-y-4">
                    <div>
                        <label for="email" class="sr-only">{{ __('auth.your_email') }}</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400"></i>
                            </div>
                            <input id="email" name="email" type="email" required class="appearance-none rounded-lg relative block w-full px-3 py-3 pl-10 border border-gray-200 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent" placeholder="{{ __('auth.email_address') }}" />
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-center">
                    <button type="submit" id="submitButton" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                        <span id="buttonText">{{ __('auth.send_reset_link') }}</span>
                        <span id="loadingSpinner" class="hidden ml-2">
                            <i class="fas fa-spinner fa-spin"></i>
                        </span>
                    </button>
                </div>

                <div class="text-center">
                    <a href="/login" class="text-sm text-teal-600 hover:text-teal-500">
                        <i class="fas fa-arrow-left mr-1"></i> Back to login
                    </a>
                </div>
            </form>
        </div>
    </main>

    <script>
        document.getElementById('forgotPasswordForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const submitButton = document.getElementById('submitButton');
            const buttonText = document.getElementById('buttonText');
            const loadingSpinner = document.getElementById('loadingSpinner');
            const successMessage = document.getElementById('successMessage');
            const errorMessage = document.getElementById('errorMessage');
            const errorText = document.getElementById('errorText');
            const emailInput = document.getElementById('email');

            // Translation strings from Laravel
            const translations = {
                sending: '{{ __('auth.sending') }}',
                sendResetLink: '{{ __('auth.send_reset_link') }}',
                errorOccurred: '{{ __('auth.error_occurred') }}',
                networkError: '{{ __('auth.network_error') }}',
                emailNotFound: '{{ __('auth.email_not_found') }}'
            };

            // Disable button and show loading
            submitButton.disabled = true;
            buttonText.textContent = translations.sending;
            loadingSpinner.classList.remove('hidden');
            successMessage.classList.add('hidden');
            errorMessage.classList.add('hidden');

            const formData = {
                email: emailInput.value
            };

            try {
                const response = await fetch('/api/v1/forgot-password', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (response.ok && data.status === 'success') {
                    successMessage.classList.remove('hidden');
                    errorMessage.classList.add('hidden');
                    emailInput.value = '';

                    // Re-enable button after showing success
                    submitButton.disabled = false;
                    buttonText.textContent = translations.sendResetLink;
                    loadingSpinner.classList.add('hidden');
                } else {
                    // Show error message
                    if (response.status === 404) {
                        errorText.textContent = translations.emailNotFound;
                    } else {
                        errorText.textContent = data.message || translations.errorOccurred;
                    }

                    if (data.errors) {
                        const errorList = Object.values(data.errors).flat().join(', ');
                        errorText.textContent = errorList;
                    }

                    errorMessage.classList.remove('hidden');
                    successMessage.classList.add('hidden');

                    // Re-enable button
                    submitButton.disabled = false;
                    buttonText.textContent = translations.sendResetLink;
                    loadingSpinner.classList.add('hidden');
                }
            } catch (error) {
                errorText.textContent = translations.networkError;
                errorMessage.classList.remove('hidden');
                successMessage.classList.add('hidden');

                // Re-enable button
                submitButton.disabled = false;
                buttonText.textContent = translations.sendResetLink;
                loadingSpinner.classList.add('hidden');
            }
        });
    </script>
</body>
</html>

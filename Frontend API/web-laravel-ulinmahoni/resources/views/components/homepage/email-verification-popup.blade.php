{{-- Email verification popup — shown once per session for authenticated users
     with unverified email. Supports ID/EN/ZH via Laravel translation. --}}
@auth
    @if(!Auth::user()->hasVerifiedEmail())
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                /** Only show once per browser session to avoid nagging on every page load */
                if (sessionStorage.getItem('email_verify_popup_shown')) return;
                sessionStorage.setItem('email_verify_popup_shown', '1');

                const userEmail = @json(Auth::user()->email);
                const csrfToken = '{{ csrf_token() }}';
                const resendUrl = '{{ url("/api/v1/resend-verification") }}';

                /** Translations for 3 languages */
                const t = {
                    title: @json(__('verification.popup_title')),
                    message: @json(__('verification.popup_message')),
                    emailLabel: @json(__('verification.popup_email_label')),
                    resendBtn: @json(__('verification.popup_resend')),
                    changeEmailBtn: @json(__('verification.popup_change_email')),
                    laterBtn: @json(__('verification.popup_later')),
                    successTitle: @json(__('verification.popup_sent_title')),
                    successMessage: @json(__('verification.popup_sent_message')),
                    errorTitle: @json(__('verification.popup_error_title')),
                    changeEmailTitle: @json(__('verification.popup_change_email_title')),
                    changeEmailPlaceholder: @json(__('verification.popup_change_email_placeholder')),
                    changeEmailConfirm: @json(__('verification.popup_change_email_confirm')),
                    changeEmailCancel: @json(__('verification.popup_cancel')),
                };

                const isDark = document.documentElement.classList.contains('dark');
                const bgColor = isDark ? '#1f2937' : '#ffffff';
                const textColor = isDark ? '#e5e7eb' : '#1f2937';

                Swal.fire({
                    icon: 'warning',
                    title: t.title,
                    html: `
                        <p style="color: ${isDark ? '#9ca3af' : '#6b7280'}; margin-bottom: 16px;">${t.message}</p>
                        <div style="background: ${isDark ? '#374151' : '#f3f4f6'}; border-radius: 8px; padding: 10px 16px; display: inline-flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                            <i class="fas fa-envelope" style="color: ${isDark ? '#9ca3af' : '#6b7280'};"></i>
                            <span style="font-weight: 600; color: ${textColor};">${userEmail}</span>
                        </div>
                    `,
                    background: bgColor,
                    color: textColor,
                    showCancelButton: true,
                    showDenyButton: true,
                    confirmButtonText: '<i class="fas fa-paper-plane mr-1"></i> ' + t.resendBtn,
                    denyButtonText: '<i class="fas fa-edit mr-1"></i> ' + t.changeEmailBtn,
                    cancelButtonText: t.laterBtn,
                    confirmButtonColor: '#0d9488',
                    denyButtonColor: '#6366f1',
                    customClass: {
                        popup: 'rounded-xl',
                    },
                    allowOutsideClick: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        /** Resend verification email */
                        resendVerification(userEmail);
                    } else if (result.isDenied) {
                        /** Change email and resend */
                        showChangeEmailDialog(userEmail);
                    }
                });

                /** Send resend-verification API call */
                async function resendVerification(email) {
                    try {
                        Swal.fire({ title: '...', allowOutsideClick: false, background: bgColor, color: textColor, didOpen: () => Swal.showLoading() });
                        const res = await fetch(resendUrl, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                            body: JSON.stringify({ email: email })
                        });
                        const data = await res.json();
                        if (res.ok && data.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: t.successTitle,
                                html: `<p style="color: ${isDark ? '#9ca3af' : '#6b7280'};">${t.successMessage}</p>
                                       <div style="background: ${isDark ? '#374151' : '#f3f4f6'}; border-radius: 8px; padding: 10px 16px; display: inline-flex; align-items: center; gap: 8px; margin-top: 12px;">
                                           <i class="fas fa-envelope" style="color: ${isDark ? '#9ca3af' : '#6b7280'};"></i>
                                           <span style="font-weight: 600; color: ${textColor};">${email}</span>
                                       </div>`,
                                background: bgColor,
                                color: textColor,
                                confirmButtonColor: '#0d9488',
                            });
                        } else {
                            Swal.fire({ icon: 'error', title: t.errorTitle, text: data.message || 'Error', background: bgColor, color: textColor, confirmButtonColor: '#0d9488' });
                        }
                    } catch (e) {
                        Swal.fire({ icon: 'error', title: t.errorTitle, text: e.message, background: bgColor, color: textColor, confirmButtonColor: '#0d9488' });
                    }
                }

                /** Show change email dialog, then resend to new email */
                function showChangeEmailDialog(currentEmail) {
                    Swal.fire({
                        title: t.changeEmailTitle,
                        input: 'email',
                        inputValue: currentEmail,
                        inputPlaceholder: t.changeEmailPlaceholder,
                        showCancelButton: true,
                        confirmButtonText: t.changeEmailConfirm,
                        cancelButtonText: t.changeEmailCancel,
                        confirmButtonColor: '#0d9488',
                        background: bgColor,
                        color: textColor,
                        inputValidator: (value) => {
                            if (!value) return t.changeEmailPlaceholder;
                            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) return 'Invalid email';
                        }
                    }).then((result) => {
                        if (result.isConfirmed && result.value) {
                            const newEmail = result.value;
                            /** Update user email via profile endpoint, then resend */
                            updateEmailAndResend(newEmail);
                        }
                    });
                }

                /** Update user's email and resend verification */
                async function updateEmailAndResend(newEmail) {
                    try {
                        Swal.fire({ title: '...', allowOutsideClick: false, background: bgColor, color: textColor, didOpen: () => Swal.showLoading() });
                        /** Update email via user profile update */
                        const updateRes = await fetch('{{ url("/user/profile-information") }}', {
                            method: 'PUT',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                            body: JSON.stringify({
                                _method: 'PUT',
                                first_name: @json(Auth::user()->first_name ?? ''),
                                last_name: @json(Auth::user()->last_name ?? ''),
                                email: newEmail,
                            })
                        });

                        if (updateRes.ok || updateRes.status === 302) {
                            /** Now resend verification to the new email */
                            await resendVerification(newEmail);
                        } else {
                            const errData = await updateRes.json().catch(() => ({}));
                            Swal.fire({ icon: 'error', title: t.errorTitle, text: errData.message || errData.errors?.email?.[0] || 'Failed to update email', background: bgColor, color: textColor, confirmButtonColor: '#0d9488' });
                        }
                    } catch (e) {
                        Swal.fire({ icon: 'error', title: t.errorTitle, text: e.message, background: bgColor, color: textColor, confirmButtonColor: '#0d9488' });
                    }
                }
            });
        </script>
    @endif
@endauth

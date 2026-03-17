<x-jet-form-section submit="updateProfileInformation">
    <x-slot name="title">
        {{ __('Informasi Profil') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Perbarui informasi profil dan alamat email akun Anda.') }}
    </x-slot>

    <x-slot name="form">
        <!-- Profile Photo -->
        <div x-data="{photoName: null, photoPreview: null}" class="col-span-6 sm:col-span-4">
                <!-- Profile Photo File Input -->
                <input type="file" class="hidden"
                            wire:model="photo"
                            x-ref="photo"
                            x-on:change="
                                    photoName = $refs.photo.files[0].name;
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        photoPreview = e.target.result;
                                    };
                                    reader.readAsDataURL($refs.photo.files[0]);
                            " />

                <x-jet-label for="photo" value="{{ __('KTP | Passport | KITAS') }}" />

                <!-- Current Profile Photo -->
                <div class="mt-2" x-show="! photoPreview">
                    <div class="bg-gray-100 p-4 rounded-lg w-full max-w-md">
                        <img src="{{ $this->user->profile_photo_url }}" alt="{{ $this->user->name }}" class="rounded-lg w-full h-48 object-cover">
                    </div>
                </div>

                <!-- New Profile Photo Preview -->
                <div class="mt-2" x-show="photoPreview" style="display: none;">
                    <div class="bg-gray-100 p-4 rounded-lg w-full max-w-md">
                        <div class="w-full h-48 bg-cover bg-no-repeat bg-center rounded-lg"
                             x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                        </div>
                    </div>
                </div>

                <x-jet-secondary-button class="mt-2 mr-2" type="button" x-on:click.prevent="$refs.photo.click()">
                    {{ __('Pilih Foto Baru') }}
                </x-jet-secondary-button>

                
                    <x-jet-secondary-button type="button" class="mt-2" wire:click="deleteProfilePhoto">
                        {{ __('Hapus Foto') }}
                    </x-jet-secondary-button>
                

                <x-jet-input-error for="photo" class="mt-2" />
            </div>

        <!-- Name -->
        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="name" value="{{ __('Nama Lengkap') }}" />
            <x-jet-input id="name" type="text" class="mt-1 block w-full" wire:model.defer="state.name" autocomplete="name" />
            <x-jet-input-error for="name" class="mt-2" />
        </div>
        <!-- First Name -->
        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="first_name" value="{{ __('Nama Depan') }}" />
            <x-jet-input id="first_name" type="text" class="mt-1 block w-full" wire:model.defer="state.first_name" autocomplete="first_name" />
            <x-jet-input-error for="first_name" class="mt-2" />
        </div>
        <!-- Last Name -->
        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="last_name" value="{{ __('Nama Belakang') }}" />
            <x-jet-input id="last_name" type="text" class="mt-1 block w-full" wire:model.defer="state.last_name" autocomplete="last_name" />
            <x-jet-input-error for="last_name" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="email" value="{{ __('Alamat Email') }}" />
            <x-jet-input id="email" type="email" class="mt-1 block w-full" wire:model.defer="state.email" />
            <x-jet-input-error for="email" class="mt-2" />
        </div>

        <!-- Verified Status -->
        <div class="col-span-6 sm:col-span-4" x-data="{
            sending: false,
            message: '',
            messageType: '',
            async sendVerification() {
                this.sending = true;
                this.message = '';
                try {
                    const response = await fetch('/api/v1/auth/resend-verification', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            email: '{{ $this->user->email }}'
                        })
                    });
                    const data = await response.json();
                    if (response.ok) {
                        this.messageType = 'success';
                        this.message = data.message || 'Email verifikasi telah dikirim!';
                    } else {
                        this.messageType = 'error';
                        this.message = data.message || 'Gagal mengirim email verifikasi.';
                    }
                } catch (error) {
                    this.messageType = 'error';
                    this.message = 'Terjadi kesalahan saat mengirim email verifikasi.';
                } finally {
                    this.sending = false;
                }
            }
        }">
            <x-jet-label for="email_verified_at" value="{{ __('Status Verifikasi') }}" />
            <div class="flex items-center gap-3">
                @if ($this->user->email_verified_at)
                    <span class="text-green-600 font-medium">{{ __('Terverifikasi') }}</span>
                @else
                    <span class="text-red-600 font-medium">{{ __('Belum Diverifikasi') }}</span>
                @endif
                <x-jet-checkbox id="email_verified_at" type="checkbox" class="mt-1 block" wire:model="state.email_verified_at" :disabled="true" {{ $this->user->email_verified_at ? 'checked' : '' }}/>
            </div>

            @if (!$this->user->email_verified_at)
                <div class="mt-3">
                    <button
                        type="button"
                        @click="sendVerification()"
                        :disabled="sending"
                        class="inline-flex items-center px-4 py-2 bg-teal-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-teal-700 active:bg-teal-800 focus:outline-none focus:border-teal-900 focus:ring focus:ring-teal-200 disabled:opacity-50 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <span x-show="!sending">{{ __('Kirim Email Verifikasi') }}</span>
                        <span x-show="sending" style="display: none;">{{ __('Mengirim...') }}</span>
                    </button>

                    <!-- Success/Error Message -->
                    <div x-show="message" style="display: none;" class="mt-2">
                        <p
                            :class="{
                                'text-green-600': messageType === 'success',
                                'text-red-600': messageType === 'error'
                            }"
                            class="text-sm font-medium"
                            x-text="message">
                        </p>
                    </div>
                </div>
            @endif

            <x-jet-input-error for="email_verified_at" class="mt-2" />
        </div>

        <!-- Phone Number -->
        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="phone_number" value="{{ __('Nomor Telepon (+62)') }}" />
            <x-jet-input id="phone_number" type="text" class="mt-1 block w-full" wire:model.defer="state.phone_number" autocomplete="phone_number" />
            <x-jet-input-error for="phone_number" class="mt-2" />
        </div>

    </x-slot>

    <x-slot name="actions">
        <div class="flex flex-col space-y-4">
            <div class="text-sm text-gray-600">
                {{ __('Dengan menyimpan profil, Anda menyetujui ')}}
                <a href="{{ route('privacy-policy') }}" class="text-teal-600 hover:text-teal-800 hover:underline" target="_blank">
                    {{ __('Kebijakan Privasi') }}
                </a>
                {{ __(' kami.') }}
            </div>
            
            <div class="flex items-center">
                <x-jet-action-message class="mr-3" on="saved">
                    {{ __('Tersimpan.') }}
                </x-jet-action-message>

                <button type="submit" wire:loading.attr="disabled" wire:target="photo" class="inline-flex items-center px-4 py-2 bg-teal-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-teal-700 active:bg-teal-800 focus:outline-none focus:border-teal-900 focus:ring focus:ring-teal-200 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    {{ __('Simpan Profil') }}
                    <div wire:loading wire:target="photo" class="ml-2">
                        <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </button>
            </div>
        </div>
    </x-slot>
</x-jet-form-section>

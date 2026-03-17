<x-jet-action-section>
    <x-slot name="title">
        {{ __('Hapus Akun') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Hapus akun Anda secara permanen.') }}
    </x-slot>

    <x-slot name="content">
        <div class="max-w-xl text-sm text-gray-600">
            {{ __('Setelah akun dihapus, semua data dan sumber daya yang terkait akan dihapus secara permanen. Sebelum menghapus akun, harap unduh semua data atau informasi yang ingin Anda simpan.') }}
        </div>

        <!-- <div class="mt-5">
            <x-jet-danger-button wire:click="confirmUserDeletion" wire:loading.attr="disabled">
                {{ __('Hapus Akun') }}
            </x-jet-danger-button>
        </div> -->
         <div class="mt-5">
            <x-jet-danger-button wire:click="$emit('openModal', 'contact-us-modal')" wire:loading.attr="disabled">
                {{ __('Hubungi kami untuk menghapus akun Anda') }}
            </x-jet-danger-button>
        </div>

        <!-- Delete User Confirmation Modal -->
        <x-jet-dialog-modal wire:model="confirmingUserDeletion">
            <x-slot name="title">
                {{ __('Hapus Akun') }}
            </x-slot>

            <x-slot name="content">
                {{ __('Apakah Anda yakin ingin menghapus akun Anda? Setelah akun dihapus, semua data dan sumber daya yang terkait akan dihapus secara permanen. Masukkan kata sandi Anda untuk mengonfirmasi bahwa Anda ingin menghapus akun secara permanen.') }}

                <div class="mt-4" x-data="{}" x-on:confirming-delete-user.window="setTimeout(() => $refs.password.focus(), 250)">
                    <x-jet-input type="password" class="mt-1 block w-3/4"
                                placeholder="{{ __('Kata Sandi') }}"
                                x-ref="password"
                                wire:model.defer="password"
                                wire:keydown.enter="deleteUser" />

                    <x-jet-input-error for="password" class="mt-2" />
                </div>
            </x-slot>

            <x-slot name="footer">
                <x-jet-secondary-button wire:click="$toggle('confirmingUserDeletion')" wire:loading.attr="disabled">
                    {{ __('Batal') }}
                </x-jet-secondary-button>

                <x-jet-danger-button class="ml-3" wire:click="deleteUser" wire:loading.attr="disabled">
                    {{ __('Hapus Akun') }}
                </x-jet-danger-button>

                <!-- <x-jet-danger-button class="ml-3" wire:click="deleteUser" wire:loading.attr="disabled">
                    {{ __('Hapus Akun') }}
                </x-jet-danger-button> -->
            </x-slot>
        </x-jet-dialog-modal>
    </x-slot>
</x-jet-action-section>

<div class="max-w-2xl">
    <h2 class="text-lg font-semibold text-white mb-6">Settings</h2>

    {{-- Profile --}}
    <div class="p-5 rounded-xl border mb-6" style="background-color: #0f0f18; border-color: #1e1e2e;">
        <h3 class="text-sm font-semibold text-white mb-4">Profile Information</h3>
        @if($profileMessage)
            <p class="text-xs mb-3 text-green-500">{{ $profileMessage }}</p>
        @endif
        <div class="space-y-3">
            <div>
                <label class="block text-xs font-medium mb-1" style="color: #6b6b8a;">Name</label>
                <input wire:model="name" type="text" class="w-full rounded-lg text-sm text-white border px-3 py-2 focus:outline-none" style="background-color: #0a0a0f; border-color: #1e1e2e;">
                @error('name') <p class="text-xs mt-1" style="color: #e24b4a;">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-medium mb-1" style="color: #6b6b8a;">Email</label>
                <input wire:model="email" type="email" class="w-full rounded-lg text-sm text-white border px-3 py-2 focus:outline-none" style="background-color: #0a0a0f; border-color: #1e1e2e;">
                @error('email') <p class="text-xs mt-1" style="color: #e24b4a;">{{ $message }}</p> @enderror
            </div>
        </div>
        <button wire:click="updateProfile" class="mt-4 px-4 py-2 rounded-lg text-sm font-medium text-white" style="background-color: #ff6b2b;">Save</button>
    </div>

    {{-- Password --}}
    <div class="p-5 rounded-xl border mb-6" style="background-color: #0f0f18; border-color: #1e1e2e;">
        <h3 class="text-sm font-semibold text-white mb-4">Change Password</h3>
        @if($passwordMessage)
            <p class="text-xs mb-3 text-green-500">{{ $passwordMessage }}</p>
        @endif
        <div class="space-y-3">
            <div>
                <label class="block text-xs font-medium mb-1" style="color: #6b6b8a;">Current Password</label>
                <input wire:model="currentPassword" type="password" class="w-full rounded-lg text-sm text-white border px-3 py-2 focus:outline-none" style="background-color: #0a0a0f; border-color: #1e1e2e;">
                @error('currentPassword') <p class="text-xs mt-1" style="color: #e24b4a;">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-medium mb-1" style="color: #6b6b8a;">New Password</label>
                <input wire:model="newPassword" type="password" class="w-full rounded-lg text-sm text-white border px-3 py-2 focus:outline-none" style="background-color: #0a0a0f; border-color: #1e1e2e;">
                @error('newPassword') <p class="text-xs mt-1" style="color: #e24b4a;">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-medium mb-1" style="color: #6b6b8a;">Confirm New Password</label>
                <input wire:model="newPasswordConfirmation" type="password" class="w-full rounded-lg text-sm text-white border px-3 py-2 focus:outline-none" style="background-color: #0a0a0f; border-color: #1e1e2e;">
            </div>
        </div>
        <button wire:click="updatePassword" class="mt-4 px-4 py-2 rounded-lg text-sm font-medium text-white" style="background-color: #ff6b2b;">Update Password</button>
    </div>

    {{-- Billing placeholder --}}
    <div class="p-5 rounded-xl border mb-6" style="background-color: #0f0f18; border-color: #1e1e2e;">
        <h3 class="text-sm font-semibold text-white mb-1">Plan & Billing</h3>
        <p class="text-sm mb-3" style="color: #6b6b8a;">
            Current plan: <span class="font-medium text-white">{{ ucfirst(auth()->user()->plan) }}</span>
        </p>
        <button class="px-4 py-2 rounded-lg text-sm font-medium text-white opacity-50 cursor-not-allowed" style="background-color: #ff6b2b;" disabled>
            Upgrade Plan
        </button>
        <p class="text-xs mt-2" style="color: #6b6b8a;">Billing coming soon.</p>
    </div>

    {{-- Delete account --}}
    <div class="p-5 rounded-xl border" style="background-color: #0f0f18; border-color: #e24b4a;">
        <h3 class="text-sm font-semibold mb-2" style="color: #e24b4a;">Delete Account</h3>
        <p class="text-sm mb-3" style="color: #6b6b8a;">This will permanently delete your account and all data. Type DELETE to confirm.</p>
        <input wire:model="deleteConfirmation" type="text" placeholder="DELETE"
               class="w-full rounded-lg text-sm text-white border px-3 py-2 mb-3 focus:outline-none"
               style="background-color: #0a0a0f; border-color: #1e1e2e;">
        @error('deleteConfirmation') <p class="text-xs mb-2" style="color: #e24b4a;">{{ $message }}</p> @enderror
        <button wire:click="deleteAccount" class="px-4 py-2 rounded-lg text-sm font-medium text-white" style="background-color: #e24b4a;">
            Delete Account
        </button>
    </div>
</div>

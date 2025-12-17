<div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-10">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-3">Join Doortag</h2>
            <p class="text-gray-500 text-lg">Start shipping smarter with the best rates</p>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <form wire:submit.prevent="submit" class="divide-y divide-gray-100">

                <!-- Company Information Section -->
                <div class="p-6 sm:p-8">
                    <h3 class="text-xs font-semibold text-red-600 uppercase tracking-wider mb-6">Company Information</h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- Company Name -->
                        <div class="sm:col-span-2">
                            <label for="company_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Company Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="company_name" wire:model="company_name"
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 placeholder-gray-400"
                                placeholder="Enter company name">
                            @error('company_name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tax ID -->
                        <div>
                            <label for="tax_id" class="block text-sm font-medium text-gray-700 mb-2">Tax ID</label>
                            <input type="text" id="tax_id" wire:model="tax_id"
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 placeholder-gray-400"
                                placeholder="Enter tax ID">
                            @error('tax_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Years in Business -->
                        <div>
                            <label for="years_in_business" class="block text-sm font-medium text-gray-700 mb-2">Years in Business</label>
                            <input type="number" id="years_in_business" wire:model="years_in_business"
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 placeholder-gray-400"
                                placeholder="0">
                            @error('years_in_business')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Business Type -->
                        <div class="sm:col-span-2">
                            <label for="business_type" class="block text-sm font-medium text-gray-700 mb-2">
                                Business Type <span class="text-red-500">*</span>
                            </label>
                            <select id="business_type" wire:model="business_type"
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 bg-white appearance-none cursor-pointer">
                                <option value="retail">Retail</option>
                                <option value="wholesale">Wholesale</option>
                            </select>
                            @error('business_type')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Contact Information Section -->
                <div class="p-6 sm:p-8">
                    <h3 class="text-xs font-semibold text-red-600 uppercase tracking-wider mb-6">Contact Information</h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- Full Name -->
                        <div class="sm:col-span-2">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="name" wire:model="name"
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 placeholder-gray-400"
                                placeholder="Enter your full name">
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" id="email" wire:model="email"
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 placeholder-gray-400"
                                placeholder="you@company.com">
                            @error('email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Phone Number <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="phone" wire:model="phone"
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 placeholder-gray-400"
                                placeholder="(555) 000-0000">
                            @error('phone')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Address Information Section -->
                <div class="p-6 sm:p-8">
                    <h3 class="text-xs font-semibold text-red-600 uppercase tracking-wider mb-6">Address Information</h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Address -->
                        <div class="sm:col-span-2 lg:col-span-3">
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                Address <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="address" wire:model="address"
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 placeholder-gray-400"
                                placeholder="Enter a location">
                            @error('address')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Address Line 2 -->
                        <div class="sm:col-span-2 lg:col-span-3">
                            <label for="address2" class="block text-sm font-medium text-gray-700 mb-2">Address Line 2</label>
                            <input type="text" id="address2" wire:model="address2"
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 placeholder-gray-400"
                                placeholder="Apartment, suite, etc. (optional)">
                            @error('address2')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- City -->
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                                City <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="city" wire:model="city"
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 placeholder-gray-400"
                                placeholder="City">
                            @error('city')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- State -->
                        <div>
                            <label for="state" class="block text-sm font-medium text-gray-700 mb-2">
                                State <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="state" wire:model="state"
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 placeholder-gray-400"
                                placeholder="State">
                            @error('state')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Zip Code -->
                        <div>
                            <label for="zipcode" class="block text-sm font-medium text-gray-700 mb-2">
                                Zip Code <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="zipcode" wire:model="zipcode"
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 placeholder-gray-400"
                                placeholder="00000">
                            @error('zipcode')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Account Security Section -->
                <div class="p-6 sm:p-8">
                    <h3 class="text-xs font-semibold text-red-600 uppercase tracking-wider mb-6">Account Security</h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" id="password" wire:model="password"
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 placeholder-gray-400"
                                placeholder="Min 8 characters">
                            @error('password')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                Confirm Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" id="password_confirmation" wire:model="password_confirmation"
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 placeholder-gray-400"
                                placeholder="Re-enter password">
                            @error('password_confirmation')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Additional Information Section -->
                <div class="p-6 sm:p-8">
                    <h3 class="text-xs font-semibold text-red-600 uppercase tracking-wider mb-6">Additional Information</h3>
                    
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                        <textarea id="notes" wire:model="notes" rows="4"
                            class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 placeholder-gray-400 resize-none"
                            placeholder="Any additional information..."></textarea>
                        @error('notes')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Submit Section -->
                <div class="p-6 sm:p-8 bg-gray-50">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <p class="text-gray-500 text-sm order-2 sm:order-1">
                            Already have an account? 
                            <a href="{{ route('login') }}" class="text-red-600 font-semibold hover:text-red-700 transition-colors">
                                Login here
                            </a>
                        </p>
                        <button type="submit" 
                            class="order-1 sm:order-2 w-full sm:w-auto px-8 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 focus:ring-4 focus:ring-red-500/50 transition-all duration-200 transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove class="flex items-center justify-center gap-2">
                                Create Account
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                            </span>
                            <span wire:loading class="flex items-center justify-center gap-2">
                                <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Processing...
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Trust Badges -->
        <div class="mt-8 text-center">
            <p class="text-gray-400 text-sm mb-4">Trusted by businesses nationwide</p>
            <div class="flex justify-center items-center gap-8 opacity-50">
                <span class="text-gray-400 text-sm font-medium">FedEx Partner</span>
                <span class="text-gray-300">•</span>
                <span class="text-gray-400 text-sm font-medium">UPS Partner</span>
                <span class="text-gray-300">•</span>
                <span class="text-gray-400 text-sm font-medium">DHL Partner</span>
            </div>
        </div>
    </div>
</div>

<div x-data="shippingForm()">
    <div>
        @if (!$hasResponse)
            <form wire:submit="getQuote" @submit="if(window.showGlobalLoader) window.showGlobalLoader()"
                class="space-y-6 sm:space-y-8">
                <x-card wire:show="!hasResponse">
                    <x-slot:header wire:show="!hasResponse">
                        <h3 class="text-lg md:text-2xl font-semibold">
                            Create a Shipping Label
                        </h3>
                    </x-slot:header>

                    <!-- Ship To Paste Address -->
                    <section class="mb-[1.489em] bg-gray-50 dark:bg-gray-800 rounded-lg p-4 sm:p-6">

                        {{-- Ship To Section --}}
                        <section>
                            <h2 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4 text-gray-800 dark:text-gray-200">
                                Ship To
                                {{-- <span x-show="!showPasteAddress" @click="showPasteAddress = true"
                                class="text-[#00a9ff] text-[.824em] cursor-pointer">
                                Paste Address
                            </span> --}}
                            </h2>

                            <div x-show="showPasteAddress" x-transition class="relative w-full mb-[1.2em]">
                                <textarea placeholder="Paste the full or partial address here (and edit it if needed) to auto-fill the fields below"
                                    rows="5"
                                    class="peer border-2 border-gray-300 rounded w-full p-[6px_12px] text-[1em] font-[500] focus:outline-none text-[#000] focus:border-[#00a9ff] resize-none"></textarea>
                            </div>

                            <div class="bg-white dark:bg-gray-700 rounded-lg p-3 sm:p-4 mb-3 sm:mb-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                                    {{-- EMail --}}
                                    <x-input label="Email *" required wire:model="receiver.email" type="email"
                                        class="" />
                                    {{-- Phone --}}
                                    <x-input label="Phone (optional)" wire:model="receiver.phone" id="receiverPhone"
                                        class="" />
                                    {{-- Name --}}
                                    <x-input label="Name *" wire:model="receiver.name" class="" required />
                                    {{-- Company --}}
                                    <x-input label="Company (optional)" wire:model="receiver.company" class="" />
                                    {{-- Address --}}
                                    <x-input label="Address *" wire:model="receiver.address" class="" required />
                                    {{-- Apt / Unit / Suite / etc. --}}
                                    <x-input label="Apt / Unit / Suite / etc. (optional)" wire:model="receiver.apt"
                                        class="" />

                                    <div class="col-span-full md:col-span-1 ">
                                        {{-- Street Address --}}
                                        {{-- City, State, Zipcode --}}
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 sm:gap-4">
                                            {{-- City --}}
                                            <x-input label="City *" wire:model="receiver.city" class=""
                                                required />
                                            {{-- State --}}
                                            <x-input label="State *" wire:model="receiver.state" class=""
                                                required />
                                            {{-- Zipcode --}}
                                            <x-input label="Zipcode *" wire:model="receiver.zip" class=""
                                                required />
                                        </div>
                                    </div>
                                    {{-- Country --}}
                                    <x-select.styled label="Country *" searchable wire:model="receiver.country"
                                        :options="$this->countries" placeholder="Select country" required />
                                </div>

                            </div>

                        </section>

                        {{-- Rubber Stamps --}}
                        <section class="my-[34px]">
                            <label class="flex flex-col md:flex-row space-x-[5px]">
                                <x-checkbox label="Rubber Stamps" wire:model.live='has_rubber_stamps'
                                    x-model="rubberStamps" />
                                <p class="text-sm text-[#999]">Print extra information on the label</p>
                            </label>

                            <div x-show="rubberStamps" x-transition
                                class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-3 sm:gap-4">
                                <x-input label="Rubber Stamp / Custom Reference 1" wire:model="rubber_stamp_1"
                                    class="" />
                                <x-input label="Rubber Stamp / Custom Reference 2" wire:model="rubber_stamp_2"
                                    class="" />
                            </div>
                        </section>

                        <!-- Ship From Section -->
                        <section>
                            <h2
                                class="text-base sm:text-lg font-semibold mb-3 sm:mb-2 text-gray-800 dark:text-gray-200">
                                Ship
                                From
                            </h2>
                            {{-- <div class="bg-white dark:bg-gray-700 rounded-lg p-3 sm:p-4 mb-3 sm:mb-4">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <h5 class="font-medium text-sm sm:text-base text-gray-800 dark:text-gray-200">
                                        Sender
                                        Information</h5>
                                    <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 hidden sm:block">
                                        Origin
                                        address details</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                                <x-select.styled label="Country" searchable wire:model="sender.country"
                                    :options="$this->countries" placeholder="Select country" required />

                                <x-input label="ZIP Code" wire:model="sender.zip" placeholder="e.g., 84117" required />
                            </div>
                        </div> --}}
                            <div class="bg-white dark:bg-gray-700 rounded-lg p-3 sm:p-4 mb-3 sm:mb-4">
                                <div class="flex items-center justify-between mb-3">
                                    <div>
                                        <h5 class="font-medium text-sm sm:text-base text-gray-800 dark:text-gray-200">
                                            Sender Information</h5>
                                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 hidden sm:block">
                                            Origin address details</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                                    @if (auth()->user()->email)
                                        <div>
                                            <label
                                                class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email
                                                *</label>
                                            <p class="text-sm sm:text-base text-gray-900 dark:text-white">
                                                {{ auth()->user()->email }}</p>
                                        </div>
                                    @endif

                                    @if (auth()->user()->phone)
                                        <div>
                                            <label
                                                class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone</label>
                                            <p class="text-sm sm:text-base text-gray-900 dark:text-white">
                                                {{ auth()->user()->phone }}</p>
                                        </div>
                                    @endif

                                    @if (auth()->user()->address)
                                        <div>
                                            <label
                                                class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Address
                                                *</label>
                                            <p class="text-sm sm:text-base text-gray-900 dark:text-white">
                                                {{ auth()->user()->address }}</p>
                                        </div>
                                    @endif

                                    @if (auth()->user()->address2)
                                        <div>
                                            <label
                                                class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Address
                                                2</label>
                                            <p class="text-sm sm:text-base text-gray-900 dark:text-white">
                                                {{ auth()->user()->address2 }}</p>
                                        </div>
                                    @endif

                                    @if (auth()->user()->city)
                                        <div>
                                            <label
                                                class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">City
                                                *</label>
                                            <p class="text-sm sm:text-base text-gray-900 dark:text-white">
                                                {{ auth()->user()->city }}</p>
                                        </div>
                                    @endif

                                    @if (auth()->user()->state)
                                        <div>
                                            <label
                                                class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">State
                                                *</label>
                                            <p class="text-sm sm:text-base text-gray-900 dark:text-white">
                                                {{ auth()->user()->state }}</p>
                                        </div>
                                    @endif

                                    @if (auth()->user()->zipcode)
                                        <div>
                                            <label
                                                class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Zipcode
                                                *</label>
                                            <p class="text-sm sm:text-base text-gray-900 dark:text-white">
                                                {{ auth()->user()->zipcode }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </section>

                        <!-- Type of Packaging Section -->
                        {{-- <section class="">
                        <h4 class="text-base sm:text-lg font-semibold mb-3 sm:mb-2 text-gray-800 dark:text-gray-200">
                            Type
                            of Packaging
                        </h4>
                        <div
                            class="bg-white dark:bg-gray-700 rounded-lg p-3 sm:p-4 mb-3 sm:mb-4  border-blue-200 dark:border-blue-600">
                            <div class="flex items-center space-x-4">
                                <img src="{{ asset('assets/images/Parcel-box.png') }}" alt=""
                                    class="w-[130px] h-[130px] object-cover rounded-lg shadow-md" />
                                <div>
                                    <h5 class="font-semibold text-sm sm:text-base text-gray-800 dark:text-gray-200">
                                        Box or
                                        Rigid Packaging</h5>
                                    <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Any custom box or
                                        thick
                                        parcel</p>
                                </div>
                            </div>
                        </div>
                    </section> --}}

                        <!-- Type of Packaging -->
                        <section class="mt-3">
                            <h1
                                class="text-base sm:text-lg font-semibold mb-3 sm:mb-2 text-gray-800 dark:text-gray-200">
                                Type of Packaging</h1>

                            <div x-data="{ packagingOpen: false }" class="rounded-[5px] border-2 transition-colors duration-200"
                                :class="packagingOpen ? 'border-[#00a9ff]' :
                                    'border-gray-300 dark:border-gray-600 bg-gradient-to-b from-white to-gray-100 dark:from-gray-700 dark:to-gray-800'">

                                <!-- Toggle Header -->
                                <div @click="packagingOpen = !packagingOpen"
                                    class="w-full flex items-center justify-between p-[10px] cursor-pointer rounded-[4px] transition-colors"
                                    :class="packagingOpen ? 'hover:bg-blue-50 dark:hover:bg-blue-900/20' :
                                        'hover:bg-gray-50 dark:hover:bg-gray-600'">
                                    <div class="flex items-center">
                                        <img src="/assets/images/Parcel-box.png" alt="Parcel"
                                            class="w-[130px] h-[130px] object-cover" />
                                        <div class="ml-[.9em]">
                                            <h1 class="text-[1em] font-[400] text-gray-900 dark:text-gray-100">Box or
                                                Rigid
                                                Packaging</h1>
                                            <p
                                                class="text-[.824em] font-[400] text-gray-500 dark:text-gray-400 mt-[3px]">
                                                Any custom box or thick parcel
                                            </p>
                                        </div>
                                    </div>
                                    <i class="fa-solid fa-caret-down text-[1.3em] text-gray-900 dark:text-gray-100"
                                        :class="packagingOpen ? 'rotate-180' : ''"
                                        style="transition: transform 0.2s;"></i>
                                </div>

                                <!-- Mapped Cards -->
                                <div x-show="packagingOpen" x-transition>
                                    <template x-for="(card, index) in packagingCards" :key="card.id">
                                        <div @click="selectPackage(card.id)"
                                            class="w-full flex items-center justify-between p-[10px] cursor-pointer transition-colors hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:border-[#00a9ff] dark:hover:border-blue-400"
                                            :class="{
                                                'border-t-[1px] border-gray-300 dark:border-gray-600': index === 0,
                                                'border-b-[1px] border-gray-300 dark:border-gray-600': index !==
                                                    packagingCards.length - 1
                                            }">
                                            <div class="flex items-center">
                                                <img :src="card.image" alt="Parcel"
                                                    class="w-[130px] h-[130px] object-cover" />
                                                <div class="ml-[.9em]">
                                                    <h1 class="text-[1em] font-[400] text-gray-900 dark:text-gray-100"
                                                        x-text="card.title"></h1>
                                                    <p class="text-[.824em] font-[400] text-gray-500 dark:text-gray-400 mt-[3px]"
                                                        x-text="card.description"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </section>

                        {{-- Box Items --}}
                        <section class="mt-3">
                            @foreach ($pieces as $index => $piece)
                                <div
                                    class="bg-white dark:bg-gray-700 rounded-lg p-3 sm:p-4 lg:p-6 mb-3 sm:mb-4 border border-gray-200 dark:border-gray-600">
                                    <div {{-- class="flex flex-col space-y-2 sm:flex-row sm:justify-between sm:items-center sm:space-y-0 mb-4 sm:mb-6">
                                    <h5 class="font-medium text-sm sm:text-base text-gray-800 dark:text-gray-200">
                                        Package
                                        {{ $index + 1 }}
                                    </h5> --}}
                                        @if (count($pieces) > 1) <x-button wire:click="removeItem({{ $index }})"
                                            wire:loading.attr="disabled" wire:target="removeItem({{ $index }})"
                                            class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 sm:px-3 sm:py-1 rounded text-xs sm:text-sm w-full sm:w-auto">
                                            <span wire:loading.remove
                                                wire:target="removeItem({{ $index }})">Remove</span>
                                            <span wire:loading
                                                wire:target="removeItem({{ $index }})">Removing...</span>
                                        </x-button> @endif
                                        </div>

                                        <!-- Package Dimensions -->

                                        <div class="mb-6 sm:mb-8">
                                            <h6
                                                class="text-sm sm:text-base font-medium text-gray-800 dark:text-gray-200 mb-3 sm:mb-4">
                                                Package
                                                Dimensions (Inches)
                                            </h6>

                                            <!-- Desktop Layout (Large screens) -->
                                            <div class="hidden lg:grid lg:grid-cols-5 gap-4 items-end">
                                                <div>
                                                    <x-input type="number"
                                                        wire:model="pieces.{{ $index }}.length"
                                                        label="Length *" step="0.01" min="0" required
                                                        id="pieces_{{ $index }}_length_desktop" />
                                                </div>
                                                <div
                                                    class="flex items-center justify-center text-gray-500 dark:text-gray-400 pb-3">
                                                    <span class="text-lg sm:text-xl">×</span>
                                                </div>
                                                <div>

                                                    <x-input type="number"
                                                        wire:model="pieces.{{ $index }}.width" label="Width *"
                                                        step="0.01" min="0" required
                                                        id="pieces_{{ $index }}_width_desktop" />
                                                </div>
                                                <div
                                                    class="flex items-center justify-center text-gray-500 dark:text-gray-400 pb-3">
                                                    <span class="text-lg sm:text-xl">×</span>
                                                </div>
                                                <div>
                                                    <x-input type="number"
                                                        wire:model="pieces.{{ $index }}.height"
                                                        label="Height *" step="0.01" min="0" required
                                                        id="pieces_{{ $index }}_height_desktop" />
                                                </div>
                                            </div>

                                            <!-- Tablet Layout (Medium screens) -->
                                            <div class="hidden md:grid lg:hidden md:grid-cols-3 gap-4">
                                                <div>

                                                    <x-input type="number"
                                                        wire:model="pieces.{{ $index }}.length"
                                                        label="Length *" step="0.01" min="0" required
                                                        id="pieces_{{ $index }}_length_tablet" />
                                                </div>
                                                <div>
                                                    <x-input type="number"
                                                        wire:model="pieces.{{ $index }}.width" label="Width *"
                                                        step="0.01" min="0" required
                                                        id="pieces_{{ $index }}_width_tablet" />
                                                </div>
                                                <div>
                                                    <x-input type="number"
                                                        wire:model="pieces.{{ $index }}.height"
                                                        label="Height *" step="0.01" min="0" required
                                                        id="pieces_{{ $index }}_height_tablet" />
                                                </div>
                                            </div>

                                            <!-- Mobile Layout (Small screens) -->
                                            <div class="md:hidden space-y-3 ">
                                                <div class="grid grid-cols-1 gap-3">
                                                    <div>
                                                        <x-input type="number" label="Length *"
                                                            wire:model="pieces.{{ $index }}.length"
                                                            min="0" step="0.01" required
                                                            id="pieces_{{ $index }}_length_mobile" />
                                                    </div>
                                                    <div>

                                                        <x-input type="number"
                                                            wire:model="pieces.{{ $index }}.width"
                                                            label="Width *" min="0" step="0.01" required
                                                            id="pieces_{{ $index }}_width_mobile" />
                                                    </div>
                                                    <div>
                                                        <x-input type="number"
                                                            wire:model="pieces.{{ $index }}.height"
                                                            label="Height *" step="0.01" min="0" required
                                                            id="pieces_{{ $index }}_height_mobile" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Package Weight -->
                                        <div class="mb-6 sm:mb-8">
                                            <h6
                                                class="text-sm sm:text-base font-medium text-gray-800 dark:text-gray-200 mb-3 sm:mb-4">
                                                Package Weight
                                            </h6>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 max-w-md">
                                                <div>

                                                    <x-input type="number"
                                                        wire:model="pieces.{{ $index }}.weight"
                                                        label="Pounds *" step="1" min="0" required
                                                        id="pieces_{{ $index }}_weight" />
                                                </div>
                                                <div>
                                                    <x-input type="number" label="Ounces" min="0"
                                                        max="15" wire:model="pieces.{{ $index }}.ounces"
                                                        id="pieces_{{ $index }}_ounces" />
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Insurance & Value -->
                                        {{-- <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 sm:gap-4 lg:gap-6">
                                        <div>
                                            <label
                                                class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Insurance
                                                Amount ($)</label>
                                            <input type="number"
                                                wire:model="pieces.{{ $index }}.insuranceAmount"
                                                class="w-full bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg px-3 py-2 sm:px-4 sm:py-3 text-sm sm:text-base text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                placeholder="12.15" step="0.01" required>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Declared
                                                Value ($)</label>
                                            <input type="number"
                                                wire:model="pieces.{{ $index }}.declaredValue"
                                                class="w-full bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg px-3 py-2 sm:px-4 sm:py-3 text-sm sm:text-base text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                placeholder="1.00" step="0.01" required>
                                        </div>
                                    </div> --}}
                                    </div>
                            @endforeach
                        </section>


                        <!-- Insurance Section -->
                        <div class="my-4">
                            <x-checkbox x-model="insuranceChecked" label="Insurance"
                                hint="Enter the total value of your shipment to add coverage by InsureShield"
                                class="text-sm" />
                            <div class="mt-2">
                                <x-link href="#" color="primary" class="text-sm">
                                    View Pricing, Excluded Items, and Terms
                                </x-link>
                            </div>

                            <div x-show="insuranceChecked" x-transition class="mt-3">
                                <x-input label="Declared Package Value ($)" placeholder="Enter package value"
                                    type="number" step="0.01" />
                            </div>
                        </div>
                        <!-- Extra Services Section -->
                        <div class="mb-8">

                            <div class="flex items-center space-x-2 text-sm pt-2">
                                <x-button.circle @click="extraServicesOpen = !extraServicesOpen" {{-- :icon="extraServicesOpen ? 'minus' : 'plus'" --}}
                                    size="xs" color="slate" outline />
                                <div>
                                    <span class="font-semibold text-gray-900 dark:text-gray-100">Extra Services</span>
                                    <span class="text-gray-500 dark:text-gray-400 text-sm ml-2"
                                        x-text="getSelectedServicesText() + ' activated'"></span>
                                </div>
                            </div>

                            <x-slide x-show="extraServicesOpen">
                                <x-card class="mt-3 bg-gray-50 dark:bg-gray-800">
                                    <!-- Signature Confirmation -->
                                    <div class="space-y-3">
                                        <x-checkbox x-model="signatureChecked"
                                            @change="if (!signatureChecked) signatureOptionsOpen = false"
                                            label="Signature Confirmation" />

                                        <div x-show="signatureChecked" x-transition>
                                            <x-select.styled x-model="selectedSignatureType" label="Signature Type"
                                                :options="[
                                                    [
                                                        'signature',
                                                        'Signature Confirmation',
                                                        'Signature required for delivery',
                                                    ],
                                                    [
                                                        'adult',
                                                        'Adult Signature',
                                                        '21+ age verification required for delivery',
                                                    ],
                                                ]" select="value:0|label:1|description:2" />
                                        </div>

                                        <!-- Return Labels -->
                                        <x-checkbox x-model="returnLabelsChecked" label="Return Labels"
                                            hint="To enable Return Labels, just let us know." disabled
                                            class="opacity-60" />
                                        <div class="ml-6">
                                            <x-link href="#" color="primary" class="text-sm underline">
                                                just let us know.
                                            </x-link>
                                        </div>

                                        <!-- Media Mail -->
                                        <x-checkbox x-model="mediaMailChecked" label="Qualifies for Media Mail"
                                            hint="Educational material only: books, music, or films (other products or any advertising prohibited)" />

                                        <!-- Irregular Package -->
                                        <x-checkbox x-model="irregularPackageChecked" label="Irregular Package"
                                            hint="For unusual package types like tubes, wooden crates, tires, etc." />
                                        <div class="ml-6">
                                            <x-link href="#" color="primary" class="text-sm">
                                                Learn More
                                            </x-link>
                                        </div>
                                    </div>
                                </x-card>
                            </x-slide>
                        </div>
                        <!-- Hazardous Materials Section -->
                        <div class="my-4">
                            <x-checkbox x-model="hazardousChecked" label="Hazardous Materials"
                                hint="Perfume, nail polish, hair spray, dry ice, lithium batteries, firearms, lighters, fuels, etc."
                                class="text-sm" />
                            <div class="mt-2">
                                <x-link href="#" color="primary" class="text-sm">
                                    Learn how to ship Hazardous Materials
                                </x-link>
                            </div>

                            <div x-show="hazardousChecked" x-transition class="mt-3">
                                <x-alert title="Important Notice"
                                    text="By using Pirate Ship, you certify that your shipment does not contain any undeclared hazardous materials or any matter prohibited by law or postal regulation."
                                    color="red" light close />
                            </div>
                        </div>
                        <!-- Customs Form Section -->
                        <div class="my-4">

                            <x-checkbox x-model="customsChecked" label="Customs Form"
                                hint="Required for International, Military APO/FPO, and U.S. Territories"
                                class="text-sm" />

                            <x-slide x-show="customsChecked">
                                <div class="mt-4 space-y-4">
                                    <x-input label="Sign Customs Form As" value="M Jibon" placeholder="Enter name" />
                                    <div>
                                        <x-label class="font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                            Package Content Type
                                        </x-label>
                                        <x-select.styled x-model="packageContentType" :options="$this->contentTypes"
                                            select="value:value|label:label|description:description" />
                                    </div>

                                    <!-- Customs Line Item -->
                                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                                        <div class="md:col-span-11">
                                            <div class="flex items-center justify-between mb-3">
                                                <x-label class="font-semibold text-gray-900 dark:text-gray-100">
                                                    Customs Line Item #1
                                                </x-label>
                                                <x-button.circle icon="plus" size="xs" color="primary"
                                                    outline title="Add Line Item" />
                                            </div>

                                            <div class="space-y-4">
                                                <x-input label="Describe what you're shipping"
                                                    placeholder="Item description" />

                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <x-input label="Quantity" type="number" placeholder="1" />
                                                    <x-input label="Total Value in $" type="number" step="0.01"
                                                        placeholder="0.00" />
                                                </div>

                                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                                                    <x-input label="Total Weight lbs" type="number" step="0.01"
                                                        placeholder="0" />
                                                    <div
                                                        class="flex justify-center items-center text-xl text-gray-500 dark:text-gray-400">
                                                        +
                                                    </div>
                                                    <x-input label="Total Weight Oz" type="number" step="0.01"
                                                        placeholder="0" />
                                                </div>

                                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                                    <div class="relative">
                                                        <x-input label="Harmonization #" placeholder="Enter code"
                                                            hint="Required for Certain Countries - Learn more" />
                                                        <x-button class="absolute right-0 top-0 h-full rounded-l-none"
                                                            color="primary" text="Search #'s" />
                                                    </div>
                                                    <div>
                                                        <x-select.native label="Item(s) Origin" :options="['United States', 'Canada', 'United Kingdom']" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="md:col-span-1 flex justify-center items-center">
                                            <x-button.circle icon="trash" size="sm" color="red" outline
                                                title="Delete item" />
                                        </div>
                                    </div>

                                    <!-- International Tax IDs -->
                                    <div>
                                        <x-label class="font-semibold text-gray-900 dark:text-gray-100 mb-3">
                                            International Tax IDs
                                        </x-label>
                                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                            <x-input label="Sender Tax ID" placeholder="Enter tax ID"
                                                hint="Optional: IOSS/HMRC/VOEC - Learn more" />
                                            <x-input label="Recipient Tax ID" placeholder="Enter tax ID"
                                                hint="Mexico/Brazil/EU (EORI) - Learn more" />
                                        </div>
                                    </div>
                                </div>
                            </x-slide>
                        </div>

                        <!-- Save Package Section -->
                        <div class="my-4">
                            <x-checkbox x-model="savePackageChecked" label="Save Package"
                                hint="Save your settings for repeated use" class="text-sm" />

                            <div x-show="savePackageChecked" x-transition class="mt-3">
                                <x-input label="Enter a nickname for your Saved Package"
                                    placeholder="Package nickname" />
                            </div>
                        </div>
                    </section>


                    <!-- Submit Button -->
                    <div class="flex justify-end pt-4">
                        <x-button type="submit" wire:loading.attr="disabled"
                            class="px-6 py-3 sm:px-8 sm:py-3 w-full sm:w-auto">
                            <span wire:loading.remove>Get Rates</span>
                            <span wire:loading>Getting Rates...</span>
                        </x-button>
                    </div>
                </x-card>
            </form>
        @endif
        <!-- Display Quotes Results -->
        @if ($hasResponse)
            <!-- Ship To Details Section -->
            <div class="mt-4 sm:mt-6">
                <h1 class="text-[30px] font-[700] text-gray-900 dark:text-white leading-[1.1] mb-[12px]">
                    {{ $receiver['name'] ?: 'Recipient Name' }}
                </h1>
                <p class="text-[17px] text-gray-700 dark:text-gray-300 leading-[1.42857143] font-[500]">
                    {{ $receiver['address'] ?: 'Address not provided' }}
                </p>
                <div class="flex items-center gap-2 mb-[48px]">
                    <p class="text-[17px] text-gray-700 dark:text-gray-300 leading-[1.42857143] font-[500]">
                        {{ $receiver['city'] }}
                        {{ $receiver['state'] ? $receiver['state'] . ' ' : '' }}{{ $receiver['zip'] }}
                    </p>
                    <i class="fa-solid fa-paste text-[1em] text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 cursor-pointer transition"
                        onclick="navigator.clipboard.writeText('{{ $receiver['city'] }} {{ $receiver['state'] ? $receiver['state'] . ' ' : '' }}{{ $receiver['zip'] }}')"
                        title="Copy to clipboard"></i>
                </div>
            </div>

            <!-- Shipment Details Section -->
            <div class="mb-[48px] py-[7px]" x-data="{ shipmentDetailsOpen: false }">
                <label @click="shipmentDetailsOpen = !shipmentDetailsOpen" class="flex cursor-pointer space-x-[5px]">
                    <div
                        class="w-[20px] h-[20px] border-[2px] border-gray-500 dark:border-gray-400 rounded-[50%] flex items-center justify-center cursor-pointer">
                        <span x-show="!shipmentDetailsOpen" class="text-[12px] text-gray-500 dark:text-gray-400">
                            +
                        </span>
                        <span x-show="shipmentDetailsOpen" class="text-[12px] text-gray-500 dark:text-gray-400">
                            -
                        </span>
                    </div>
                    <p class="font-[500] text-[15px] text-gray-700 dark:text-gray-300">Shipment Details</p>
                </label>
                <div x-show="shipmentDetailsOpen" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    class="p-[16px_16px_16px_24px] flex lg:flex-row flex-col justify-between bg-gray-100 dark:bg-gray-800 border-[2px] border-gray-300 dark:border-gray-600 rounded-[5px] mt-[10px] lg:gap-0 gap-[16px]">

                    <!-- Ship From Address -->
                    <div class="lg:w-[33.3333%] w-full text-[14px]">
                        <h1 class="font-[500] text-gray-600 dark:text-gray-300 pb-[6px] leading-[1.42857143]">Ship From
                            Address</h1>
                        @php
                            $user = Auth::user();
                            $fromName = '';
                            $fromAddress = '';
                            $fromCityState = '';

                            if ($user instanceof \App\Models\Customer) {
                                $fromName = $user->name;
                                $fromAddress = $user->address ?? 'Address not provided';
                                $fromCityState =
                                    ($user->city ?? '') . ' ' . ($user->state ?? '') . ' ' . ($user->zipcode ?? '');
                            } elseif ($user instanceof \App\Models\User) {
                                $fromName = $user->name;
                                $fromAddress = $user->address ?? 'Address not provided';
                                $fromCityState =
                                    ($user->city ?? '') . ' ' . ($user->state ?? '') . ' ' . ($user->zipcode ?? '');
                            }
                        @endphp
                        <p class="text-gray-500 dark:text-gray-400 leading-[1.42857143]">
                            {{ $fromName ?: 'Sender Name' }}</p>
                        <p class="text-gray-500 dark:text-gray-400 leading-[1.42857143]">{{ $fromAddress }}</p>
                        <p class="text-gray-500 dark:text-gray-400 leading-[1.42857143]">
                            {{ trim($fromCityState) ?: 'City, State ZIP' }}
                        </p>
                    </div>

                    <!-- Package Details -->
                    <div class="lg:w-[33.3333%] w-full text-[14px] lg:pl-[8px] pl-0">
                        <h1 class="font-[500] text-gray-600 dark:text-gray-300 pb-[6px] leading-[1.42857143]">Package
                            Details</h1>
                        @if (!empty($pieces))
                            @foreach ($pieces as $index => $piece)
                                @php
                                    $totalWeight = ($piece['weight'] ?? 0) + ($piece['ounces'] ?? 0) / 16;
                                    $weightLbs = floor($totalWeight);
                                    $weightOz = round(($totalWeight - $weightLbs) * 16);
                                @endphp
                                <div class="mb-2">
                                    <p class="text-gray-500 dark:text-gray-400 font-[500] leading-[1.42857143]">
                                        Package {{ $index + 1 }}:
                                        <span class="pl-[4px] font-[400]">Box or Rigid Packaging</span>
                                    </p>
                                    <p class="text-gray-500 dark:text-gray-400 font-[500] leading-[1.42857143]">
                                        Dimensions:
                                        <span class="pl-[4px] font-[400]">
                                            {{ $piece['length'] ?? 0 }}x{{ $piece['width'] ?? 0 }}x{{ $piece['height'] ?? 0 }}"
                                        </span>
                                    </p>
                                    <p class="text-gray-500 dark:text-gray-400 font-[500] leading-[1.42857143]">
                                        Weight:
                                        <span class="pl-[4px] font-[400]">
                                            {{ $weightLbs }} lbs {{ $weightOz }} oz
                                        </span>
                                    </p>
                                </div>
                            @endforeach
                        @endif
                        <p class="text-gray-500 dark:text-gray-400 font-[500] leading-[1.42857143]">Free Online
                            Delivery Confirmation</p>
                    </div>

                    <!-- Label Details -->
                    <div class="lg:w-[33.3333%] w-full text-[14px] lg:pl-[16px] pl-0">
                        <h1 class="font-[500] text-gray-600 dark:text-gray-300 pb-[6px] leading-[1.42857143]">
                            Label Details
                            <i class="fa-solid fa-circle-question text-[1.1em] text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 cursor-pointer"
                                title="Label information"></i>
                        </h1>
                        <p class="text-gray-500 dark:text-gray-400 font-[500] leading-[1.42857143]">
                            Label Size: <span class="pl-[4px] font-[400]">4x6"</span>
                        </p>
                        <p class="text-gray-500 dark:text-gray-400 font-[500] leading-[1.42857143]">
                            Label Filetype: <span class="pl-[4px] font-[400]">PDF</span>
                        </p>
                    </div>
                </div>
            </div>

            <x-card class="mt-4 sm:mt-6">
                <x-slot:header>
                    <div class="flex flex-col space-y-3 lg:flex-row lg:justify-between lg:items-center lg:space-y-0">
                        <div>
                            <h3 class="text-lg sm:text-xl font-semibold">Choose a Service</h3>
                            @if (!empty($quotes))
                                <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    Found {{ count($quotes) }} shipping option(s)
                                </p>
                            @endif
                        </div>
                        <!-- Sort options - mobile-friendly -->
                        {{-- <div class="flex flex-wrap items-center gap-2 text-xs sm:text-sm text-gray-600">
                            <span class="hidden sm:inline">Sort by:</span>
                            <span class="sm:hidden">Sort:</span>
                            <button class="text-blue-600 hover:text-blue-800 font-medium">Best</button>
                            <span class="text-gray-400">|</span>
                            <button class="text-blue-600 hover:text-blue-800 font-medium">Cheapest</button>
                            <span class="text-gray-400">|</span>
                            <button class="text-blue-600 hover:text-blue-800 font-medium">USPS</button>
                            <span class="text-gray-400">|</span>
                            <button class="text-blue-600 hover:text-blue-800 font-medium">UPS</button>
                        </div> --}}
                    </div>
                </x-slot:header>

                @if (!empty($errorMessage))
                    <x-alert text="{{ $errorMessage }}" color="red" />
                @elseif(!empty($quotes))
                    <!-- Collapsible Quotes Section -->
                    <div class="rounded-[5px] border-2 transition-colors duration-200 mx-2 sm:mx-0"
                        :class="quotesOpen ? 'border-[#00a9ff]' :
                            'border-gray-300 dark:border-gray-600 bg-gradient-to-b from-white to-gray-100 dark:from-gray-700 dark:to-gray-800'">

                        <!-- Selected Quote Preview (Always Visible) -->
                        @foreach ($quotes as $index => $quote)
                            @php
                                $isBest = $index === 0;
                                $isCheapest =
                                    collect($quotes)->pluck('totalAmount')->min() == ($quote['totalAmount'] ?? 0);
                                $baseAmount = $quote['baseAmount'] ?? 0;
                                $totalAmount = $quote['totalAmount'] ?? 0;
                                $savingsPercent =
                                    $baseAmount > 0 ? round((($baseAmount - $totalAmount) / $baseAmount) * 100) : 0;
                            @endphp

                            <div x-show="selectedQuoteIndex === {{ $index }}"
                                @click="quotesOpen = !quotesOpen"
                                class="cursor-pointer px-3 pt-3 pb-2.5 flex justify-between items-center relative hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors border-b border-gray-200 dark:border-gray-600">

                                <!-- Top Arrow Indicator -->
                                <div class="absolute top-1 left-1/2 transform -translate-x-1/2 transition-all duration-300"
                                    :class="quotesOpen ? 'opacity-100 translate-y-0' : 'opacity-0 -translate-y-1'">
                                    <svg class="w-3 h-3 text-blue-600 dark:text-blue-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 15l7-7 7 7"></path>
                                    </svg>
                                </div>

                                <div class="flex-1">
                                    <div class="flex items-center mb-2">
                                        <!-- Carrier Logo -->
                                        @if (strtolower($quote['carrierCode'] ?? '') === 'fedex')
                                            <img src="{{ asset('assets/images/fedex.svg') }}" class="h-6 mr-1"
                                                alt="FedEx" />
                                        @elseif(strtolower($quote['carrierCode'] ?? '') === 'ups')
                                            <img src="{{ asset('assets/images/ups.svg') }}" class="h-6 mr-1"
                                                alt="UPS" />
                                        @elseif(strtolower($quote['carrierCode'] ?? '') === 'usps')
                                            <img src="{{ asset('assets/images/usps.svg') }}" class="h-6 mr-1"
                                                alt="USPS" />
                                        @elseif(strtolower($quote['carrierCode'] ?? '') === 'dhl')
                                            <img src="{{ asset('assets/images/dhl.svg') }}" class="h-6 mr-1"
                                                alt="DHL" />
                                        @else
                                            <div
                                                class="w-6 h-6 bg-gray-500 rounded flex items-center justify-center mr-1">
                                                <span
                                                    class="text-white text-xs font-bold">{{ strtoupper(substr($quote['carrierCode'] ?? 'N', 0, 1)) }}</span>
                                            </div>
                                        @endif
                                        <h1
                                            class="text-[17px] font-[400] text-black dark:text-white leading-[19px] pl-1 mt-[-1px]">
                                            {{ $quote['serviceDescription'] ?? 'N/A' }}
                                        </h1>
                                    </div>
                                    <div class="mb-2">
                                        <div
                                            class="text-[13px] font-[500] text-gray-600 dark:text-gray-300 leading-[14px] line-clamp-1">
                                            {{ $quote['estimatedDeliveryDate'] ?? 'Delivery date not available' }}
                                        </div>
                                    </div>
                                    <p
                                        class="text-xs sm:text-sm md:text-[.824em] font-[400] text-gray-500 dark:text-gray-400 mt-[3px] line-clamp-2">
                                        Compare rates and delivery times • First option selected by default
                                    </p>
                                    @if (count($quotes) > 1)
                                        <div class="flex flex-wrap items-center gap-1 sm:gap-2 mt-2">
                                            @if ($isBest)
                                                <span
                                                    class="bg-gray-800 text-white text-xs px-1.5 py-0.5 sm:px-2 sm:py-1 rounded font-medium">BEST</span>
                                            @endif
                                            @if ($isCheapest)
                                                <span
                                                    class="bg-green-600 text-white text-xs px-1.5 py-0.5 sm:px-2 sm:py-1 rounded font-medium">CHEAPEST</span>
                                            @endif
                                            <span
                                                class="bg-blue-600 text-white text-xs px-1.5 py-0.5 sm:px-2 sm:py-1 rounded font-medium">SELECTED</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex flex-col items-end">
                                    <div class="text-[18px] font-[500] text-black dark:text-white leading-[20px] mb-1">
                                        ${{ number_format($totalAmount, 2) }}
                                    </div>
                                    @if ($savingsPercent > 0)
                                        <div
                                            class="text-[11px] font-[400] text-green-600 dark:text-green-400 leading-[12px]">
                                            Save {{ $savingsPercent }}%
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-shrink-0 ml-2 flex flex-col items-center">
                                    <!-- Main Caret Icon -->
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 transition-all duration-300 ease-in-out mb-1"
                                        :class="quotesOpen ? 'rotate-180 text-blue-600 dark:text-blue-400' :
                                            'text-gray-600 dark:text-gray-400'"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>

                                    <!-- Click Indicator Text -->
                                    <span class="text-[10px] text-gray-500 dark:text-gray-400 font-medium"
                                        x-text="quotesOpen ? 'Close' : 'Open'"></span>
                                </div>

                                <!-- Bottom Arrow Indicator -->
                                <div class="absolute bottom-1 left-1/2 transform -translate-x-1/2 transition-all duration-300"
                                    :class="!quotesOpen ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-1'">
                                    <svg class="w-3 h-3 text-blue-600 dark:text-blue-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Expandable Quotes List -->
                    <div x-show="quotesOpen" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform -translate-y-2"
                        x-transition:enter-end="opacity-100 transform translate-y-0">
                        <div class="space-y-2 p-2 border-t border-gray-200 dark:border-gray-600">
                            <div class="text-xs text-gray-500 dark:text-gray-400 mb-2 sm:mb-3 px-1 sm:px-2">
                                💡 Tip: Compare rates and delivery times to find the best option for your needs
                            </div>
                            @foreach ($quotes as $index => $quote)
                                @php
                                    $isBest = $index === 0;
                                    $isCheapest =
                                        collect($quotes)->pluck('totalAmount')->min() == ($quote['totalAmount'] ?? 0);
                                @endphp

                                <div @click="selectQuote({{ json_encode($quote) }}, {{ $index }})"
                                    class="border rounded-lg hover:shadow-md transition-all duration-300 cursor-pointer  "
                                    :class="{
                                        'bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/40 dark:to-indigo-900/40 border-blue-500 dark:border-blue-400 shadow-lg ring-2 ring-blue-200 dark:ring-blue-700 transform scale-[1.02]': selectedQuoteIndex ===
                                            {{ $index }}
                                    }">
                                    <div class="p-2 sm:p-3 md:p-4 transition-colors relative rounded-lg"
                                        :class="selectedQuoteIndex === {{ $index }} ?
                                            'bg-gradient-to-r from-blue-50/50 to-indigo-50/50 dark:from-blue-900/20 dark:to-indigo-900/20' :
                                            'hover:bg-blue-50 dark:hover:bg-blue-900/20'">
                                        <!-- Selected Quote Highlight Badge -->
                                        <div x-show="selectedQuoteIndex === {{ $index }}" x-transition
                                            class="absolute -top-2 -right-2 bg-blue-600 text-white rounded-full p-1 shadow-lg z-10">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        </div>

                                        <div
                                            class="flex flex-col space-y-3 sm:flex-row sm:justify-between sm:items-start sm:space-y-0">
                                            <div class="flex items-start space-x-2 sm:space-x-3 flex-1 min-w-0">
                                                <!-- Carrier Logo/Icon -->
                                                <div class="flex-shrink-0 mt-0.5 sm:mt-1">
                                                    @if (strtolower($quote['carrierCode'] ?? '') === 'fedex')
                                                        <img src="{{ asset('assets/images/fedex.svg') }}"
                                                            class="w-[55px] h-8 object-contain" alt="FedEx" />
                                                    @elseif(strtolower($quote['carrierCode'] ?? '') === 'ups')
                                                        <img src="{{ asset('assets/images/ups.svg') }}"
                                                            class="w-[55px] h-8 object-contain" alt="UPS" />
                                                    @elseif(strtolower($quote['carrierCode'] ?? '') === 'usps')
                                                        <img src="{{ asset('assets/images/usps.svg') }}"
                                                            class="w-[55px] h-8 object-contain" alt="USPS" />
                                                    @elseif(strtolower($quote['carrierCode'] ?? '') === 'dhl')
                                                        <img src="{{ asset('assets/images/dhl.svg') }}"
                                                            class="w-[55px] h-8 object-contain" alt="DHL" />
                                                    @else
                                                        <div
                                                            class="w-[55px] h-8 object-contain bg-gray-500 rounded flex items-center justify-center">
                                                            <span
                                                                class="text-white text-xs font-bold">{{ strtoupper(substr($quote['carrierCode'] ?? 'N/A', 0, 2)) }}</span>
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="flex-1 min-w-0">
                                                    <div
                                                        class="flex flex-col sm:flex-row sm:items-center space-y-1 sm:space-y-0 sm:space-x-2 mb-1">
                                                        <h4 class="font-semibold text-gray-900 dark:text-white text-xs sm:text-sm md:text-base truncate"
                                                            :class="selectedQuoteIndex === {{ $index }} ?
                                                                'text-blue-900 dark:text-blue-100' : ''">
                                                            {{ $quote['serviceDescription'] ?? 'N/A' }}
                                                        </h4>
                                                        <div class="flex flex-wrap gap-1">
                                                            @if ($isBest)
                                                                <span
                                                                    class="bg-gray-800 text-white text-xs px-2 py-1 rounded font-medium">BEST</span>
                                                            @endif
                                                            @if ($isCheapest)
                                                                <span
                                                                    class="bg-green-600 text-white text-xs px-2 py-1 rounded font-medium">CHEAPEST</span>
                                                            @endif
                                                            {{-- <span x-show="selectedQuoteIndex === {{ $index }}"
                                                                x-transition
                                                                class="bg-blue-600 text-white text-xs px-2 py-1 rounded font-medium">SELECTED</span> --}}
                                                        </div>
                                                    </div>

                                                    <div
                                                        class="text-xs sm:text-sm text-blue-600 dark:text-blue-400 mb-1 sm:mb-2">
                                                        ${{ number_format($quote['insuranceAmount'] ?? 0, 0) }}
                                                        carrier
                                                        liability
                                                        <span class="block sm:inline">
                                                            @if (isset($quote['estimatedDelivery']))
                                                                • {{ $quote['estimatedDelivery'] }}
                                                            @else
                                                                • Estimated delivery in 3-5 business days
                                                            @endif
                                                        </span>
                                                    </div>

                                                    @php
                                                        $totalSurcharges = collect($quote['surcharges'] ?? [])->sum(
                                                            'amount',
                                                        );
                                                        $baseAmount = $quote['baseAmount'] ?? 0;
                                                        $totalAmount = $quote['totalAmount'] ?? 0;
                                                        $savingsPercent =
                                                            $baseAmount > 0
                                                                ? round(
                                                                    (($baseAmount + $totalSurcharges - $totalAmount) /
                                                                        ($baseAmount + $totalSurcharges)) *
                                                                        100,
                                                                )
                                                                : 0;
                                                    @endphp

                                                    @if ($savingsPercent > 0)
                                                        <div
                                                            class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">
                                                            Save {{ $savingsPercent }}% • Deepest discount
                                                            available
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div
                                                class="text-center sm:text-right flex-shrink-0 self-center sm:self-start">
                                                @if ($baseAmount > $totalAmount)
                                                    <div class="text-xs sm:text-sm text-gray-500 line-through">
                                                        ${{ number_format($baseAmount + $totalSurcharges, 2) }}
                                                        retail
                                                    </div>
                                                @endif
                                                <div class="text-base sm:text-lg md:text-xl lg:text-2xl font-bold text-gray-900 dark:text-white mb-1 sm:mb-2"
                                                    :class="selectedQuoteIndex === {{ $index }} ?
                                                        'text-blue-900 dark:text-blue-100' : ''">
                                                    ${{ number_format($totalAmount, 2) }}
                                                </div>
                                                {{-- <button
                                                    @click.stop="selectQuote({{ json_encode($quote) }}, {{ $index }})"
                                                    class="px-2 py-1 sm:px-3 sm:py-1 text-xs font-medium rounded transition-all duration-200 transform"
                                                    :class="selectedQuoteIndex === {{ $index }} ?
                                                        'bg-green-600 hover:bg-green-700 text-white shadow-lg scale-110' :
                                                        'bg-blue-600 hover:bg-blue-700 text-white hover:scale-105'">
                                                    <span x-show="selectedQuoteIndex === {{ $index }}"
                                                        x-transition>✓
                                                        Selected</span>
                                                    <span x-show="selectedQuoteIndex !== {{ $index }}"
                                                        x-transition>Select</span>
                                                </button> --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="text-gray-500 dark:text-gray-400 mb-2">📦</div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">
                            No shipping quotes available
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Please check your shipping details and try again.
                        </p>
                    </div>
                @endif
            </x-card>
        @endif
    </div>
</div>


@script
    <script>
        // Initialize Alpine.js data for the shipping form
        Alpine.data('shippingForm', () => ({
            // State variables
            sidebarOpen: false,
            showPasteAddress: false,
            rubberStamps: @entangle('has_rubber_stamps').live,
            shipFromOpen: false,
            extraServicesOpen: false,
            signatureCheckbox: false,
            signatureOptionsOpen: false,
            qualifiesforMediaMail: false,
            irregularPackage: false,
            hazardousMaterials: false,
            customsForm: false,
            savePackage: false,
            packageContentTypeOpen: false,
            insurance: false,

            // Insurance section
            insuranceChecked: false,

            // Extra Services section
            signatureChecked: false,
            returnLabelsChecked: false,
            mediaMailChecked: false,
            irregularPackageChecked: false,
            selectedSignatureType: 'signature',

            // Hazardous Materials section
            hazardousChecked: false,

            // Customs Form section
            customsChecked: false,
            packageContentType: 'merchandise',

            // Save Package section
            savePackageChecked: false,

            // Quotes section
            quotesOpen: false,
            selectedQuoteIndex: 0, // Default to first quote

            // Packaging cards data
            packagingCards: [{
                    id: 1,
                    title: "Envelope, Padded Envelope, Poly Bag, Soft Pack, or Box in a Bag",
                    description: "Measure & use the Length and Width of the Envelope before putting anything in it",
                    image: "/assets/images/SoftEnvelope.png",
                },
                {
                    id: 2,
                    title: "USPS Priority Mail Small Flat Rate Box",
                    description: "Small Flat Rate Mailing Box only",
                    image: "/assets/images/SmallFlatRateBox.png",
                },
                {
                    id: 3,
                    title: "USPS Priority Mail Medium Flat Rate Box",
                    description: "Any Medium Flat Rate Box, including 1 (Top-Loading) and 2 (Side-Loading)",
                    image: "/assets/images/MediumFlatRateBox.png",
                },
                {
                    id: 4,
                    title: "USPS Priority Mail Large Flat Rate Box",
                    description: "Any Large Flat Rate Box, including APO/FPO or Board Game Flat Rate Boxes",
                    image: "/assets/images/LargeFlatRateBox.png",
                },
                {
                    id: 5,
                    title: "USPS Priority Mail Flat Rate Envelope",
                    description: "Non-padded Flat Rate Envelope including Small and Window",
                    image: "/assets/images/FlatRateEnvelope.png",
                },
                {
                    id: 6,
                    title: "USPS Priority Mail Legal Flat Rate Envelope",
                    description: "Priority Mail Legal Flat Rate Envelope",
                    image: "/assets/images/FlatRateLegalEnvelope.png",
                },
                {
                    id: 7,
                    title: "USPS Priority Mail Padded Flat Rate Envelope",
                    description: "Flat Rate-branded Padded Envelope only",
                    image: "/assets/images/FlatRatePaddedEnvelope.png",
                },
                {
                    id: 8,
                    title: "USPS Priority Mail Express Padded Flat Rate Envelope",
                    description: "Express-branded only",
                    image: "/assets/images/ExpressFlatRatePaddedEnvelope.png",
                },
                {
                    id: 9,
                    title: "USPS Priority Mail Express Legal Flat Rate Envelope",
                    description: "Express-branded only",
                    image: "/assets/images/ExpressFlatRateLegalEnvelope.png",
                },
                {
                    id: 10,
                    title: "USPS Priority Mail Express Flat Rate Envelope",
                    description: "Express-branded non-padded only",
                    image: "/assets/images/ExpressFlatRateEnvelope.png",
                },
                {
                    id: 11,
                    title: "UPS Express Envelope",
                    description: "UPS-branded Envelope for letter-sized documents",
                    image: "/assets/images/01.png",
                },
                {
                    id: 12,
                    title: "UPS Small Express Box",
                    description: "UPS-branded box for small-sized shipments",
                    image: "/assets/images/2a.png",
                },
                {
                    id: 13,
                    title: "UPS Medium Express Box",
                    description: "UPS-branded box for medium-sized shipments",
                    image: "/assets/images/2b.png",
                },
                {
                    id: 14,
                    title: "UPS Large Express Box",
                    description: "UPS-branded box for large-sized shipments",
                    image: "/assets/images/2c.png",
                },
                {
                    id: 15,
                    title: "UPS Express Tube",
                    description: "UPS-branded triangular box for rolled documents (blueprints, posters, etc.)",
                    image: "/assets/images/03.png",
                },
                {
                    id: 16,
                    title: "UPS Express Pak",
                    description: "UPS-branded poly envelope",
                    image: "/assets/images/04.png",
                },
            ],

            // Initialization
            init() {
                // Initialize floating labels
                this.updateFloatingLabels();

                // Watch for changes in rubber stamps
                this.$watch('rubberStamps', (value) => {
                    console.log('Rubber stamps toggled:', value);
                });

                // Access Livewire component through $wire
                console.log('Livewire component initialized');

                // Listen for Livewire lifecycle events
                this.$wire.on('loading-finished', () => {
                    this.hideLoader();
                });
            },

            // Methods
            showLoader() {
                if (window.showGlobalLoader) {
                    window.showGlobalLoader();
                }
            },

            hideLoader() {
                if (window.hideGlobalLoader) {
                    window.hideGlobalLoader();
                }
            },

            toggleSidebar() {
                this.sidebarOpen = !this.sidebarOpen;
                const sidebar = document.getElementById("sidebar");
                const menuIcon = document.getElementById("menuIcon");

                if (this.sidebarOpen) {
                    sidebar?.classList.remove("-translate-x-full");
                    if (menuIcon) {
                        menuIcon.innerHTML =
                            `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />`;
                    }
                } else {
                    sidebar?.classList.add("-translate-x-full");
                    if (menuIcon) {
                        menuIcon.innerHTML =
                            `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />`;
                    }
                }
            },

            togglePasteAddress() {
                this.showPasteAddress = !this.showPasteAddress;
            },

            toggleShipFrom() {
                this.shipFromOpen = !this.shipFromOpen;
            },

            toggleExtraServices() {
                this.extraServicesOpen = !this.extraServicesOpen;
            },

            toggleSignatureOptions() {
                if (this.signatureCheckbox) {
                    this.signatureOptionsOpen = !this.signatureOptionsOpen;
                } else {
                    this.signatureOptionsOpen = false;
                }
            },

            togglePackageContentType() {
                this.packageContentTypeOpen = !this.packageContentTypeOpen;
            },

            updateFloatingLabels() {
                this.$nextTick(() => {
                    const wrappers = document.querySelectorAll(".floating-label-wrapper");

                    wrappers.forEach((wrapper) => {
                        const input = wrapper.querySelector(".floating-input");
                        const label = wrapper.querySelector(".floating-label");

                        if (input && label) {
                            const updateLabel = () => {
                                if (document.activeElement === input || input.value.length >
                                    0) {
                                    label.classList.add("small");
                                    label.classList.remove("large");
                                } else {
                                    label.classList.add("large");
                                    label.classList.remove("small");
                                }
                            };

                            // Initialize
                            updateLabel();

                            // Add event listeners
                            input.addEventListener("focus", updateLabel);
                            input.addEventListener("blur", updateLabel);
                            input.addEventListener("input", updateLabel);
                        }
                    });
                });
            },

            // Computed properties
            get extraServicesTitle() {
                let services = [];
                if (this.signatureCheckbox) services.push('Signature Confirmation');
                if (this.qualifiesforMediaMail) services.push('Qualifies for Media Mail');
                if (this.irregularPackage) services.push('Irregular Package');

                return services.length > 0 ? services.join(', ') : 'No Extra Services';
            },

            getSelectedServicesText() {
                let services = [];
                if (this.signatureChecked) services.push('Signature Confirmation');
                if (this.returnLabelsChecked) services.push('Return Labels');
                if (this.mediaMailChecked) services.push('Media Mail');
                if (this.irregularPackageChecked) services.push('Irregular Package');
                return services.length ? services.join(', ') : 'No extra services';
            },

            // Package card selection
            selectPackage(packageId) {
                console.log('Package selected:', packageId);
                // You can emit events to Livewire here
                $wire.dispatch('package-selected', {
                    packageId: packageId
                });
            },

            // Quote selection
            selectQuote(quote, index) {
                console.log('Quote selected:', quote);
                this.selectedQuoteIndex = index;
                this.quotesOpen = false; // Close quotes list after selection
                // You can emit events to Livewire here or handle quote selection
                $wire.dispatch('quote-selected', {
                    quote: quote,
                    index: index
                });
            }
        }));

        // Register custom Livewire directive for dynamic checkbox toggling
        Livewire.directive('toggle-checkbox', ({
            el,
            directive,
            component,
            cleanup
        }) => {
            const checkboxId = directive.expression;
            const inputWrapper = el;

            const checkbox = document.getElementById(checkboxId);
            if (checkbox) {
                const toggleHandler = () => {
                    inputWrapper.classList.toggle("hidden", !checkbox.checked);
                };

                checkbox.addEventListener("change", toggleHandler);

                cleanup(() => {
                    checkbox.removeEventListener("change", toggleHandler);
                });
            }
        });

        // Global Livewire event listeners
        document.addEventListener('livewire:initialized', () => {
            console.log('Livewire fully initialized');
        });

        // Listen for Livewire events
        Livewire.on('quote-received', (event) => {
            console.log('Quote received:', event);
            // Hide global loader when quotes are received
            if (window.hideGlobalLoader) {
                window.hideGlobalLoader();
            }
        });

        Livewire.on('error-occurred', (event) => {
            console.error('Error occurred:', event);
            // Hide global loader on error
            if (window.hideGlobalLoader) {
                window.hideGlobalLoader();
            }
        });

        // Hook into form submission to show global loader
        document.addEventListener('DOMContentLoaded', () => {
            const shippingForm = document.querySelector('form[wire\\:submit="getQuote"]');
            if (shippingForm) {
                shippingForm.addEventListener('submit', (e) => {
                    console.log('Form submitted - showing global loader');
                    if (window.showGlobalLoader) {
                        window.showGlobalLoader();
                    }
                });
            }
        });

        // Also listen for Livewire request start/end
        document.addEventListener('livewire:init', () => {
            Livewire.hook('request', ({
                uri,
                options,
                payload,
                respond,
                succeed,
                fail
            }) => {
                // Check if this request is for getQuote method
                if (payload && payload.calls && payload.calls.some(call => call.method === 'getQuote')) {
                    console.log('getQuote request started - showing global loader');
                    if (window.showGlobalLoader) {
                        window.showGlobalLoader();
                    }
                }

                succeed(({
                    status,
                    response
                }) => {
                    console.log('getQuote request completed - hiding global loader');
                    if (window.hideGlobalLoader) {
                        window.hideGlobalLoader();
                    }
                });

                fail(({
                    status,
                    content,
                    preventDefault
                }) => {
                    console.log('getQuote request failed - hiding global loader');
                    if (window.hideGlobalLoader) {
                        window.hideGlobalLoader();
                    }
                });
            });
        });
    </script>
@endscript

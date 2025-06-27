<x-app-layout>
    <x-card>
        <x-slot:header>
            Welcome to Door Tag Shipper
        </x-slot:header>
        <div class="space-y-2">
            <p>
                �📦 Welcome to Door Tag - your reliable shipping partner for seamless package delivery! Door Tag provides smart shipping solutions with real-time tracking and delivery notifications right to your customer's doorstep.
            </p>
            <div class="mt-4 space-y-2">
                <i>
                    "What Door Tag shipping platform offers:"
                </i>
                <ul class="ml-2 mt-2 list-inside list-decimal font-semibold">
                    <li>Discounted UPS & USPS shipping rates</li>
                    <li>Real-time package tracking</li>
                    <li>Smart door tag notifications</li>
                    <li>Batch shipping & label printing</li>
                </ul>
                <p>Built with modern technology:</p>
                <ul class="ml-2 mt-2 list-inside list-decimal font-semibold">
                    <li>Laravel v12 - Robust backend framework</li>
                    <li>Livewire v3 - Dynamic user interactions</li>
                    <li>TallStackUI v2 - Beautiful interface components</li>
                    <li>TailwindCSS v4 - Modern responsive design</li>
                </ul>
            </div>
        </div>
        <x-slot:footer>
            <span class="text-xs">
                🚚 <x-link href="#" bold sm>Start shipping with Door Tag today - Fast, reliable, and cost-effective!</x-link>
            </span>
        </x-slot:footer>
    </x-card>
</x-app-layout>

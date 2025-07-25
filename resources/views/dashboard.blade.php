<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('New Booking') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                {{-- Success Message --}}
                @if (session('success'))
                    <div class="mb-4 text-green-600">{{ session('success') }}</div>
                @endif

                {{-- Overlap Error --}}
                @if ($errors->has('error'))
                    <div class="mb-4 text-red-600">
                        {{ $errors->first('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('bookings.store') }}" class="space-y-4">
                    @csrf

                    <!-- Customer Name -->
                    <div>
                        <x-input-label for="customer_name" :value="__('Customer Name')" />
                        <x-text-input id="customer_name" class="block mt-1 w-full" type="text" name="customer_name"
                            value="{{ old('customer_name') }}" required autofocus />
                        @error('customer_name')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Customer Email -->
                    <div>
                        <x-input-label for="customer_email" :value="__('Customer Email')" />
                        <x-text-input id="customer_email" class="block mt-1 w-full" type="email" name="customer_email"
                            value="{{ old('customer_email') }}" required />
                        @error('customer_email')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Booking Date -->
                    <div>
                        <x-input-label for="booking_date" :value="__('Booking Date')" />
                        <x-text-input id="booking_date" class="block mt-1 w-full" type="date" name="booking_date"
                            value="{{ old('booking_date', now()->toDateString()) }}" required />
                        @error('booking_date')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>


                    <!-- Booking Type -->
                    <div>
                        <x-input-label for="booking_type" :value="__('Booking Type')" />
                        <select id="booking_type" name="booking_type"
                            class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" onchange="toggleFields()"
                            required>
                            <option value="">Select Booking Type</option>
                            <option value="full_day" {{ old('booking_type') == 'full_day' ? 'selected' : '' }}>Full Day
                            </option>
                            <option value="half_day" {{ old('booking_type') == 'half_day' ? 'selected' : '' }}>Half Day
                            </option>
                            <option value="custom" {{ old('booking_type') == 'custom' ? 'selected' : '' }}>Custom
                            </option>
                        </select>
                        @error('booking_type')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Half Day Slot -->
                    <div id="half_day_fields" style="display:none;">
                        <x-input-label for="booking_slot" :value="__('Booking Slot')" />
                        <select name="booking_slot" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">Select Slot</option>
                            <option value="first_half" {{ old('booking_slot') == 'first_half' ? 'selected' : '' }}>
                                First Half</option>
                            <option value="second_half" {{ old('booking_slot') == 'second_half' ? 'selected' : '' }}>
                                Second Half</option>
                        </select>
                        @error('booking_slot')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Custom Time Fields -->
                    <div id="custom_fields" class="grid grid-cols-1 md:grid-cols-2 gap-4" style="display:none;">
                        <div>
                            <x-input-label for="booking_from" :value="__('Booking From')" />
                            <x-text-input id="booking_from" type="time" name="booking_from" class="block mt-1 w-full"
                                value="{{ old('booking_from') }}" />
                            @error('booking_from')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <x-input-label for="booking_to" :value="__('Booking To')" />
                            <x-text-input id="booking_to" type="time" name="booking_to" class="block mt-1 w-full"
                                value="{{ old('booking_to') }}" />
                            @error('booking_to')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <x-primary-button>{{ __('Submit Booking') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleFields() {
            const type = document.getElementById('booking_type').value;
            document.getElementById('half_day_fields').style.display = type === 'half_day' ? 'block' : 'none';
            document.getElementById('custom_fields').style.display = type === 'custom' ? 'grid' : 'none';
        }
        document.addEventListener('DOMContentLoaded', toggleFields);
    </script>
</x-app-layout>

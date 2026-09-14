<div>
    <div class="flex justify-end mb-4">
        <x-filament::input.wrapper>
            <x-filament::input.select wire:model.live="activePeriod">
                <option value="all">All Time</option>
                <option value="today">Today</option>
                <option value="month">This Month</option>
                <option value="year">This Year</option>
            </x-filament::input.select>
        </x-filament::input.wrapper>
    </div>
</div>

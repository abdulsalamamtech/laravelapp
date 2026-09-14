<x-filament-panels::page>
    @php
        $steps = $this->steps;
        $completedSteps = collect($steps)->where('completed', true)->count();
        $totalSteps = count($steps);
        $progressPercent = $totalSteps > 0 ? (int) round(($completedSteps / $totalSteps) * 100) : 0;
    @endphp

    <div
        class="rounded-3xl  p-6 ">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-3 p-4">
                <div
                    class="inline-flex items-center gap-2 rounded-full bg-primary-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.2em] text-primary-700 dark:bg-primary-950/70 dark:text-primary-300">
                    <span class="h-2 w-2 rounded-full bg-primary-500"></span>
                    Company journey
                </div>

                <div>
                    <h2 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">
                        Milestone Flow
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        A clear overview of the company onboarding progress for <span
                            class="font-semibold text-primary-600 dark:text-primary-400">{{ $record->name }}</span>.
                    </p>
                </div>
            </div>

            <div
                class="rounded-2xl border border-gray-200 bg-white/80 p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800/80">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p
                            class="text-[11px] font-semibold uppercase tracking-[0.2em] text-gray-500 dark:text-gray-400">
                            Completion
                        </p>
                        <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">
                            {{ $completedSteps }}/{{ $totalSteps }}
                        </p>
                    </div>
                    <div
                        class="flex h-14 w-14 items-center justify-center rounded-full bg-emerald-50 text-lg font-semibold text-emerald-700 dark:bg-emerald-950/70 dark:text-emerald-400">
                        {{ $progressPercent }}%
                    </div>
                </div>

                <div class="mt-3 h-2 w-full overflow-hidden rounded-full bg-gray-100 dark:bg-gray-700">
                    <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-primary-500 transition-all"
                        style="width: {{ $progressPercent }}%"></div>
                </div>
            </div>
        </div>

        <div class="mt-8 space-y-4">
            @foreach ($steps as $step)
                <div
                    class="group relative overflow-hidden rounded-2xl border border-gray-200/70 bg-white/90 p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-gray-700 dark:bg-gray-800/70">
                    <div
                        class="absolute inset-y-0 left-0 w-1 {{ $step['completed'] ? 'bg-emerald-500' : 'bg-gray-300 dark:bg-gray-600' }}">
                    </div>

                    <div class="flex items-start gap-4 pl-3">
                        <div
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl border {{ $step['completed'] ? 'border-emerald-200 bg-emerald-50 text-emerald-600 dark:border-emerald-900 dark:bg-emerald-950/60 dark:text-emerald-400' : 'border-gray-200 bg-gray-50 text-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400' }}">
                            @svg($step['icon'], 'w-5 h-5')
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ $step['name'] }}
                                </h3>
                                <span
                                    class="rounded-full px-4 py-1 text-[10px] font-semibold uppercase tracking-[0.2em] {{ $step['completed'] ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/70 dark:text-emerald-400' : 'bg-red-200 text-red-600 dark:bg-red-700 dark:text-red-300' }}">
                                    {{ $step['completed'] ? 'Completed' : 'Pending' }}
                                </span>
                            </div>

                            <p
                                class="mt-1 text-sm leading-6 {{ $step['completed'] ? 'text-emerald-700 dark:text-emerald-400' : 'text-red-500 dark:text-red-400' }}">
                                {{ $step['detail'] }}
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-filament-panels::page>

<x-filament-panels::page>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-950 pb-20 transition-colors duration-300">

        {{-- HEADER --}}
        <div class="top-0 z-50 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 py-3">
                <div class="flex justify-between items-center">
                    {{-- Left Section --}}
                    <div class="flex items-center gap-3">
                        <div>
                            <h1 class="text-lg font-bold text-gray-900 dark:text-white leading-tight">
                                Review Bank Soal
                            </h1>
                            <p class="text-xs text-gray-500 dark:text-gray-400 italic">
                                Pratinjau Naskah Ujian
                            </p>
                        </div>
                    </div>

                    {{-- Right Section --}}
                    <div class="text-right">
                        <h2 class="font-bold text-gray-900 dark:text-white leading-tight">
                            {{ $bankSoal->mapel->mapel ?? 'Mata Pelajaran' }}
                        </h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Kelas {{ $bankSoal->kelas }} • Semester {{ $bankSoal->semester }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- MAIN CONTENT --}}
        <div class="max-w-7xl mx-auto px-4 mt-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                {{-- LEFT COLUMN: Daftar Soal --}}
                <div class="lg:col-span-8 space-y-6">
                    @foreach ($bankSoal->soals as $index => $item)
                        <div class="soal-card" x-data="{ open: false }">
                            {{-- Card Soal --}}
                            <div
                                class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden">
                                {{-- Header Soal --}}
                                <div
                                    class="bg-gray-50 dark:bg-gray-800/50 px-6 py-3 border-b border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center justify-between">
                                        <span
                                            class="inline-flex items-center px-3 py-1 bg-primary-600 text-white text-xs font-semibold rounded-full">
                                            Soal {{ $index + 1 }}
                                        </span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            @if ($item->tipe_soal === 'multiple_choice')
                                                Pilihan Ganda
                                            @elseif($item->tipe_soal === 'matching')
                                                Menjodohkan
                                            @else
                                                Essay
                                            @endif
                                        </span>
                                    </div>
                                </div>

                                {{-- Body Soal --}}
                                <div class="p-6">
                                    {{-- Teks Soal dengan RichEditor --}}
                                    <div class="prose prose-xl max-w-none text-gray-800 dark:text-gray-200 mb-4">
                                        {!! $item->soal !!}
                                    </div>

                                    {{-- Gambar Soal --}}
                                    @if ($item->gambar)
                                        <div class="mt-3 mb-4">
                                            <img src="{{ asset('storage/' . $item->gambar) }}" alt="Gambar soal"
                                                class="rounded-xl border border-gray-200 dark:border-gray-700 max-w-xs h-auto shadow-sm">
                                        </div>
                                    @endif

                                    {{-- MULTIPLE CHOICE --}}
                                    @if ($item->tipe_soal === 'multiple_choice' && is_array($item->multiple_choice))
                                        <div class="mt-4 space-y-2">
                                            @foreach ($item->multiple_choice as $opsi)
                                                @php
                                                    $isCorrect =
                                                        isset($item->jawaban_benar) &&
                                                        $opsi['opsi'] === $item->jawaban_benar;
                                                    $colors = [
                                                        'A' => 'blue',
                                                        'B' => 'green',
                                                        'C' => 'yellow',
                                                        'D' => 'red',
                                                        'E' => 'purple',
                                                    ];
                                                    $color = $colors[$opsi['opsi']] ?? 'gray';
                                                @endphp

                                                <div
                                                    class="flex items-start gap-3 p-3 rounded-xl border-2 transition-all
                                                    @if ($isCorrect) border-green-500 bg-green-50 dark:bg-green-900/20
                                                    @else
                                                        border-gray-100 dark:border-gray-700 hover:border-{{ $color }}-200 dark:hover:border-{{ $color }}-800 @endif">

                                                    {{-- Opsi Letter --}}
                                                    <div
                                                        class="flex-none w-8 h-8 rounded-lg flex items-center justify-center font-bold text-sm
                                                        @if ($isCorrect) bg-green-500 text-white
                                                        @else
                                                            bg-{{ $color }}-100 text-{{ $color }}-700 dark:bg-{{ $color }}-900/30 dark:text-{{ $color }}-400 @endif">
                                                        {{ $opsi['opsi'] }}
                                                    </div>

                                                    {{-- Jawaban Text & Image --}}
                                                    <div class="flex-grow text-gray-700 dark:text-gray-300">
                                                        <p>{{ $opsi['jawaban'] }}</p>

                                                        @if (!empty($opsi['jawaban_img']))
                                                            <img src="{{ asset('storage/' . $opsi['jawaban_img']) }}"
                                                                alt="Gambar jawaban"
                                                                class="mt-2 rounded-lg border border-gray-200 dark:border-gray-700 max-w-xs h-auto">
                                                        @endif
                                                    </div>

                                                    {{-- Kunci Jawaban Badge --}}
                                                    @if ($isCorrect)
                                                        <span
                                                            class="flex-none px-2 py-1 bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-400 text-xs font-bold rounded border border-green-200 dark:border-green-800">
                                                            KUNCI
                                                        </span>
                                                    @endif

                                                    {{-- Skor --}}
                                                    <span class="flex-none text-xs text-gray-500 dark:text-gray-400">
                                                        Skor: {{ $opsi['skor'] ?? 0 }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    {{-- MATCHING --}}
                                    @if ($item->tipe_soal === 'matching' && is_array($item->matching))
                                        <div class="mt-4">
                                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                                Pasangan Jawaban:
                                            </h4>

                                            <div class="space-y-2">
                                                @foreach ($item->matching as $match)
                                                    <div
                                                        class="flex items-start gap-4 p-3 bg-gray-50 dark:bg-gray-800/50 rounded-xl">
                                                        {{-- Left Column --}}
                                                        <div class="flex-1">
                                                            <div class="flex items-center gap-2">
                                                                <span
                                                                    class="w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xs font-bold">
                                                                    {{ chr(64 + $loop->iteration) }}
                                                                </span>
                                                                <span
                                                                    class="text-gray-700 dark:text-gray-300">{{ $match['kiri'] }}</span>
                                                            </div>
                                                            @if ($match['kiri_img'])
                                                                <img src="{{ asset('storage/' . $match['kiri_img']) }}"
                                                                    class="mt-2 ml-8 h-16 w-auto rounded-lg border border-gray-200 dark:border-gray-700">
                                                            @endif
                                                        </div>

                                                        {{-- Arrow Icon --}}
                                                        <div class="flex-none text-gray-400">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3">
                                                                </path>
                                                            </svg>
                                                        </div>

                                                        {{-- Right Column --}}
                                                        <div class="flex-1">
                                                            <div class="flex items-center gap-2">
                                                                <span
                                                                    class="w-6 h-6 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 flex items-center justify-center text-xs font-bold">
                                                                    {{ $loop->iteration }}
                                                                </span>
                                                                <span
                                                                    class="text-gray-700 dark:text-gray-300">{{ $match['kanan'] }}</span>
                                                            </div>
                                                            @if ($match['kanan_img'])
                                                                <img src="{{ asset('storage/' . $match['kanan_img']) }}"
                                                                    class="mt-2 ml-8 h-16 w-auto rounded-lg border border-gray-200 dark:border-gray-700">
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>


            </div>
        </div>

        {{-- MOBILE BOTTOM NAV --}}

    </div>

    {{-- Print Styles --}}
    @push('styles')
        <style>
            @media print {
                body {
                    background-color: white !important;
                    padding: 0 !important;
                    margin: 0 !important;
                }

                .sticky,
                .fixed,
                .lg\:col-span-4 {
                    display: none !important;
                }

                .lg\:col-span-8 {
                    width: 100% !important;
                    max-width: 100% !important;
                    flex: 0 0 100% !important;
                }

                .bg-white,
                .dark\:bg-gray-900 {
                    background-color: white !important;
                    color: black !important;
                    border: 1px solid #e5e7eb !important;
                    box-shadow: none !important;
                }

                .text-gray-800,
                .text-gray-700,
                .text-gray-900 {
                    color: black !important;
                }

                .border-gray-200,
                .border-gray-100 {
                    border-color: #e5e7eb !important;
                }
            }

            /* Smooth Scrolling */
            html {
                scroll-behavior: smooth;
            }
        </style>
    @endpush
</x-filament-panels::page>

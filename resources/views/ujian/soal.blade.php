<form action="/ujian/submit" method="POST">
    @csrf

    <input type="hidden" name="peserta_id" value="{{ $peserta->id }}">

    @foreach ($soals as $index => $soal)
        <div class="mb-6 p-4 border rounded">
            <h4>
                {{ $index + 1 }}. {!! nl2br(e($soal->soal)) !!}
            </h4>

            {{-- GAMBAR SOAL --}}
            @if ($soal->gambar)
                <img src="{{ asset('storage/' . $soal->gambar) }}" width="200">
            @endif

            {{-- MULTIPLE CHOICE --}}
            @if ($soal->tipe_soal === 'multiple_choice')
                @foreach ($soal->multiple_choice ?? [] as $i => $opsi)
                    <label class="d-block">
                        <input type="checkbox" name="jawaban[{{ $soal->id }}][]" value="{{ $opsi['opsi'] }}">

                        {{ $opsi['opsi'] }}. {{ $opsi['jawaban'] }}

                        @if (!empty($opsi['jawaban_img']))
                            <br>
                            <img src="{{ asset('storage/' . $opsi['jawaban_img']) }}" width="150">
                        @endif
                    </label>
                @endforeach
            @endif

            {{-- MATCHING --}}
            @if ($soal->tipe_soal === 'matching')
                @foreach ($soal->matching ?? [] as $i => $match)
                    <div class="mb-2">
                        <strong>{{ $match['kiri'] }}</strong>

                        <select name="jawaban[{{ $soal->id }}][{{ $i }}]" required>
                            <option value="">-- pilih jawaban --</option>
                            @foreach ($soal->matching as $opt)
                                <option value="{{ $opt['kanan'] }}">
                                    {{ $opt['kanan'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endforeach
            @endif
        </div>
    @endforeach

    <button type="submit">Submit</button>
</form>

<form method="POST" action="/ujian/submit">
    @csrf
    <input type="hidden" name="peserta_id" value="{{ $peserta->id }}">

    @foreach ($soals as $soal)
        <div class="card mb-3">
            <div class="card-body">
                <p><strong>{{ $loop->iteration }}.</strong> {{ $soal->soal }}</p>

                @foreach (['a', 'b', 'c', 'd'] as $opsi)
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="jawaban[{{ $soal->id }}]"
                            value="{{ $opsi }}" required>
                        <label class="form-check-label">
                            {{ $soal->$opsi }}
                        </label>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach

    <button class="btn btn-success w-100">Submit Jawaban</button>
</form>

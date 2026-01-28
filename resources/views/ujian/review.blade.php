<h3>Review Data Peserta</h3>

<ul>
    <li>Nama: {{ $data['nama'] }}</li>
    <li>NIS: {{ $data['nis'] }}</li>
    <li>Kelas: {{ $ujian->kelase->kelas }}</li>
    <li>Mapel: {{ $ujian->mapel->mapel }}</li>
    <li>Durasi: {{ $ujian->durasi_menit }} menit</li>
</ul>

<form method="POST" action="/ujian/mulai">
    @csrf
    @foreach ($data as $key => $value)
        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
    @endforeach

    <input type="hidden" name="ujian_id" value="{{ $ujian->id }}">

    <button type="submit">Mulai Ujian</button>
</form>

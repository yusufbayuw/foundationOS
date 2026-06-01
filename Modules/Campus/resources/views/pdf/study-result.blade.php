@extends('core::pdf.layout')

@section('title', 'Detail Hasil Studi')

@section('document_title', 'DETAIL HASIL STUDI')

@section('meta')
    <table class="meta-bar">
        <tr>
            <td class="meta-label">NIM</td>
            <td>{{ $student?->student_number ?? '-' }}</td>
            <td class="meta-label">Periode</td>
            <td>{{ $studyPlan?->academicPeriod?->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Mahasiswa</td>
            <td colspan="3"><strong>{{ $student?->full_name ?? $student?->user?->name ?? '-' }}</strong></td>
        </tr>
        <tr>
            <td class="meta-label">Mata Kuliah</td>
            <td>{{ $item?->course?->code ?? '-' }} — {{ $item?->course?->name ?? '-' }}</td>
            <td class="meta-label">SKS</td>
            <td>{{ $item?->credits ?? '-' }}</td>
        </tr>
    </table>
@endsection

@section('content')
    <div class="section-title">NILAI AKHIR</div>
    <table class="data-table">
        <tr>
            <td>Huruf</td>
            <td class="amount"><strong>{{ $studyResult->grade_letter ?? '-' }}</strong></td>
        </tr>
        <tr>
            <td>Bobot</td>
            <td class="amount">{{ $studyResult->grade_point !== null ? number_format((float) $studyResult->grade_point, 2) : '-' }}</td>
        </tr>
        <tr>
            <td>Nilai Tertimbang</td>
            <td class="amount">{{ $studyResult->weight_score !== null ? number_format((float) $studyResult->weight_score, 2) : '-' }}</td>
        </tr>
        <tr>
            <td>Lulus</td>
            <td class="amount">{{ $studyResult->passed ? 'Ya' : 'Tidak' }}</td>
        </tr>
        <tr>
            <td>Dipublikasikan</td>
            <td class="amount">{{ optional($studyResult->published_at)->format('d/m/Y H:i') ?? '-' }}</td>
        </tr>
    </table>

    @if(is_array($studyResult->components_breakdown) && count($studyResult->components_breakdown) > 0)
        <div class="section-title">KOMPONEN PENILAIAN</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Komponen</th>
                    <th class="amount">Nilai</th>
                </tr>
            </thead>
            <tbody>
                @foreach($studyResult->components_breakdown as $component => $score)
                    <tr>
                        <td>{{ is_string($component) ? $component : 'Komponen' }}</td>
                        <td class="amount">{{ is_scalar($score) ? $score : '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($studyResult->notes)
        <div style="margin-top: 12px;">
            <strong>Catatan:</strong> {{ $studyResult->notes }}
        </div>
    @endif
@endsection

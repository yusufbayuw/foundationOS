@extends('core::pdf.layout')

@section('title', 'Kartu Rencana Studi')

@section('document_title', 'KARTU RENCANA STUDI (KRS)')

@section('meta')
    <table class="meta-bar">
        <tr>
            <td class="meta-label">No. KRS</td>
            <td><strong>{{ $studyPlan->plan_number ?? '-' }}</strong></td>
            <td class="meta-label">Status</td>
            <td>{{ strtoupper($studyPlan->status) }}</td>
        </tr>
        <tr>
            <td class="meta-label">NIM</td>
            <td>{{ $student?->student_number ?? '-' }}</td>
            <td class="meta-label">Periode</td>
            <td>{{ $studyPlan->academicPeriod?->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Mahasiswa</td>
            <td colspan="3"><strong>{{ $student?->full_name ?? $student?->user?->name ?? '-' }}</strong></td>
        </tr>
        <tr>
            <td class="meta-label">Total SKS</td>
            <td>{{ $studyPlan->total_credits }}</td>
            <td class="meta-label">Disetujui</td>
            <td>{{ optional($studyPlan->approved_at)->format('d/m/Y H:i') ?? '-' }}</td>
        </tr>
    </table>
@endsection

@section('content')
    <div class="section-title">MATA KULIAH DIAMBIL</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Kode</th>
                <th>Mata Kuliah</th>
                <th class="amount">SKS</th>
                <th>Kelas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($studyPlan->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->course?->code ?? '-' }}</td>
                    <td>{{ $item->course?->name ?? '-' }}</td>
                    <td class="amount">{{ $item->credits }}</td>
                    <td>{{ $item->courseOffering?->class_code ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if($studyPlan->notes)
        <div style="margin-top: 12px;">
            <strong>Catatan:</strong> {{ $studyPlan->notes }}
        </div>
    @endif
@endsection

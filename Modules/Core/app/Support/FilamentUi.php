<?php

namespace Modules\Core\Support;

use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;
use UnitEnum;

class FilamentUi
{
    /**
     * Exact phrases that need a natural Indonesian translation.
     *
     * @var array<string, string>
     */
    protected const PHRASES = [
        // ── Singular ────────────────────────────────────────────────────────────
        'Academic period' => 'Periode akademik',
        'Academic year' => 'Tahun akademik',
        'Achievement type' => 'Jenis prestasi',
        'Admission period' => 'Periode penerimaan',
        'Assessment item' => 'Item asesmen',
        'Attendance log' => 'Log kehadiran',
        'Audit log' => 'Log audit',
        'Book category' => 'Kategori buku',
        'Book copy' => 'Eksemplar buku',
        'Budget' => 'Anggaran',
        'Campus' => 'Kampus',
        'Chart of account' => 'Bagan akun',
        'City' => 'Kota',
        'Class student' => 'Siswa kelas',
        'Collage student' => 'Mahasiswa',
        'Core' => 'Inti',
        'Country' => 'Negara',
        'Course offering' => 'Penawaran mata kuliah',
        'Course' => 'Mata kuliah',
        'Curriculum' => 'Kurikulum',
        'Department' => 'Departemen',
        'District' => 'Kecamatan',
        'Employee' => 'Karyawan',
        'Employment contract' => 'Kontrak kerja',
        'Enrollment' => 'Penerimaan',
        'Exam result' => 'Hasil ujian',
        'Exam schedule' => 'Jadwal ujian',
        'Faculty' => 'Fakultas',
        'Feeder log' => 'Log Feeder',
        'File upload' => 'Unggahan berkas',
        'Finance' => 'Keuangan',
        'Fine' => 'Denda',
        'Global' => 'Global',
        'Goods receipt item' => 'Item penerimaan barang',
        'Goods receipt' => 'Penerimaan barang',
        'Journal entry line' => 'Baris entri jurnal',
        'Journal entry' => 'Entri jurnal',
        'Kpi indicator' => 'Indikator KPI',
        'Kpi score' => 'Nilai KPI',
        'Kpi template' => 'Template KPI',
        'Leave request' => 'Permohonan cuti',
        'Lecturer' => 'Dosen',
        'Library' => 'Perpustakaan',
        'Loan' => 'Peminjaman',
        'Member' => 'Anggota',
        'Module' => 'Modul',
        'Monitoring' => 'Pemantauan',
        'Organization setting' => 'Pengaturan organisasi',
        'Organization' => 'Organisasi',
        'Payment' => 'Pembayaran',
        'Payroll component' => 'Komponen payroll',
        'Position' => 'Jabatan',
        'Procurement category' => 'Kategori pengadaan',
        'Procurement item' => 'Item pengadaan',
        'Procurement' => 'Pengadaan',
        'Province' => 'Provinsi',
        'Purchase order item' => 'Item pesanan pembelian',
        'Purchase order' => 'Pesanan pembelian',
        'Purchase requisition item' => 'Item permintaan pembelian',
        'Purchase requisition' => 'Permintaan pembelian',
        'Registration' => 'Registrasi',
        'Request for quotation' => 'Permintaan penawaran',
        'Rfq item' => 'Item RFQ',
        'Rfq vendor' => 'Vendor RFQ',
        'Salary slip component' => 'Komponen slip gaji',
        'Salary slip' => 'Slip gaji',
        'Schedule' => 'Jadwal',
        'School class' => 'Kelas',
        'School' => 'Sekolah',
        'Shift' => 'Shift',
        'Student achievement' => 'Prestasi siswa',
        'Student assessment answer' => 'Jawaban asesmen siswa',
        'Student grade' => 'Nilai siswa',
        'Student invoice item' => 'Item tagihan siswa',
        'Student invoice' => 'Tagihan siswa',
        'Student' => 'Siswa',
        'Study plan item' => 'Item rencana studi',
        'Study plan' => 'Rencana studi',
        'Study program' => 'Program studi',
        'Study result' => 'Hasil studi',
        'Subject' => 'Mata pelajaran',
        'Subscription log' => 'Riwayat langganan',
        'Subscription plan' => 'Paket langganan',
        'Teacher' => 'Guru',
        'Tenant module' => 'Modul tenant',
        'Tenant role' => 'Peran tenant',
        'Tenant setting' => 'Pengaturan tenant',
        'Tenant' => 'Tenant',
        'Thesis' => 'Tesis',
        'Timezone' => 'Zona waktu',
        'Tuition type' => 'Jenis biaya pendidikan',
        'User tenant role' => 'Peran pengguna tenant',
        'User' => 'Pengguna',
        'Vendor bill item' => 'Item tagihan vendor',
        'Vendor bill' => 'Tagihan vendor',
        'Vendor' => 'Vendor',
        'Village' => 'Desa',
        'Violation type' => 'Jenis pelanggaran',
        'Violation' => 'Pelanggaran',

        // ── Plural forms (Filament auto-generates plural for navigation labels) ─
        'Academic periods' => 'Periode Akademik',
        'Academic years' => 'Tahun Akademik',
        'Achievement types' => 'Jenis Prestasi',
        'Admission periods' => 'Periode Penerimaan',
        'Assessment items' => 'Item Asesmen',
        'Assessments' => 'Asesmen',
        'Attendance logs' => 'Log Kehadiran',
        'Attendances' => 'Absensi',
        'Audit logs' => 'Log Audit',
        'Book categories' => 'Kategori Buku',
        'Book copies' => 'Eksemplar Buku',
        'Books' => 'Buku',
        'Budgets' => 'Anggaran',
        'Chart of accounts' => 'Bagan Akun',
        'Cities' => 'Kota',
        'Class students' => 'Siswa Kelas',
        'Collage students' => 'Mahasiswa',
        'Countries' => 'Negara',
        'Course offerings' => 'Penawaran Mata Kuliah',
        'Courses' => 'Mata Kuliah',
        'Curricula' => 'Kurikulum',
        'Departments' => 'Departemen',
        'Districts' => 'Kecamatan',
        'Employees' => 'Karyawan',
        'Employment contracts' => 'Kontrak Kerja',
        'Exam results' => 'Hasil Ujian',
        'Exam schedules' => 'Jadwal Ujian',
        'Faculties' => 'Fakultas',
        'Feeder logs' => 'Log Feeder',
        'File uploads' => 'Unggahan Berkas',
        'Fines' => 'Denda',
        'Goods receipt items' => 'Item Penerimaan Barang',
        'Goods receipts' => 'Penerimaan Barang',
        'Journal entries' => 'Entri Jurnal',
        'Journal entry lines' => 'Baris Entri Jurnal',
        'Kpi indicators' => 'Indikator KPI',
        'Kpi scores' => 'Nilai KPI',
        'Kpi templates' => 'Template KPI',
        'Leave requests' => 'Permohonan Cuti',
        'Lecturers' => 'Dosen',
        'Loans' => 'Peminjaman',
        'Members' => 'Anggota',
        'Modules' => 'Modul',
        'Organization settings' => 'Pengaturan Organisasi',
        'Organizations' => 'Organisasi',
        'Payments' => 'Pembayaran',
        'Payroll components' => 'Komponen Payroll',
        'Positions' => 'Jabatan',
        'Procurement categories' => 'Kategori Pengadaan',
        'Procurement items' => 'Item Pengadaan',
        'Provinces' => 'Provinsi',
        'Purchase order items' => 'Item Pesanan Pembelian',
        'Purchase orders' => 'Pesanan Pembelian',
        'Purchase requisition items' => 'Item Permintaan Pembelian',
        'Purchase requisitions' => 'Permintaan Pembelian',
        'Registrations' => 'Registrasi',
        'Request for quotations' => 'Permintaan Penawaran',
        'Rfq items' => 'Item RFQ',
        'Rfq vendors' => 'Vendor RFQ',
        'Salary slip components' => 'Komponen Slip Gaji',
        'Salary slips' => 'Slip Gaji',
        'Schedules' => 'Jadwal',
        'School classes' => 'Kelas',
        'Shifts' => 'Shift',
        'Student achievements' => 'Prestasi Siswa',
        'Student assessment answers' => 'Jawaban Asesmen Siswa',
        'Student grades' => 'Nilai Siswa',
        'Student invoice items' => 'Item Tagihan Siswa',
        'Student invoices' => 'Tagihan Siswa',
        'Students' => 'Siswa',
        'Study plan items' => 'Item Rencana Studi',
        'Study plans' => 'Rencana Studi',
        'Study programs' => 'Program Studi',
        'Study results' => 'Hasil Studi',
        'Subjects' => 'Mata Pelajaran',
        'Subscription logs' => 'Riwayat Langganan',
        'Subscription plans' => 'Paket Langganan',
        'Teachers' => 'Guru',
        'Tenant modules' => 'Modul Tenant',
        'Tenant roles' => 'Peran Tenant',
        'Tenant settings' => 'Pengaturan Tenant',
        'Tenants' => 'Tenant',
        'Theses' => 'Tesis',
        'Timezones' => 'Zona Waktu',
        'Tuition types' => 'Jenis Biaya Pendidikan',
        'User tenant roles' => 'Peran Pengguna Tenant',
        'Users' => 'Pengguna',
        'Vendor bill items' => 'Item Tagihan Vendor',
        'Vendor bills' => 'Tagihan Vendor',
        'Vendors' => 'Vendor',
        'Villages' => 'Desa',
        'Violation types' => 'Jenis Pelanggaran',
        'Violations' => 'Pelanggaran',

        // ── Specific field/relation labels ──────────────────────────────────────
        'Study program' => 'Program Studi',    // field: studyProgram.name
        'Academic advisor' => 'Dosen Pembimbing', // field: academicAdvisor
        'Student number' => 'NIM',
        'National student number' => 'NISN/NIM Nasional',
        'Full name' => 'Nama Lengkap',
        'Entry year' => 'Tahun Masuk',
        'Entry semester' => 'Semester Masuk',
        'Admission type' => 'Jalur Masuk',
        'Current semester' => 'Semester Saat Ini',
        'Graduation date' => 'Tanggal Lulus',
        'Created at' => 'Dibuat pada',
        'Updated at' => 'Diperbarui pada',
        'Email address' => 'Alamat Email',
        'Is active' => 'Aktif',
        'Is locked' => 'Dikunci',
        'Start date' => 'Tanggal mulai',
        'End date' => 'Tanggal selesai',

        // ── Fase 1.2 — tambahan frasa audit 2026-05-22 ──────────────────────────
        'Phone number' => 'Nomor telepon',
        'Started at' => 'Dimulai pada',
        'Finished at' => 'Selesai pada',
        'Due at' => 'Jatuh tempo',
        'Published at' => 'Dipublikasikan pada',
        'Birth date' => 'Tanggal lahir',
        'Birth place' => 'Tempat lahir',
        'Father name' => 'Nama ayah',
        'Mother name' => 'Nama ibu',
        'Quantity' => 'Jumlah',
        'Unit price' => 'Harga satuan',
        'Total amount' => 'Total',
        'Sub total' => 'Subtotal',
        'Tax' => 'Pajak',
        'Discount' => 'Diskon',
        'Reference number' => 'Nomor referensi',
        'Reference' => 'Referensi',
        'Remarks' => 'Catatan',
        'Notes' => 'Catatan',
        'Description' => 'Deskripsi',
        'Status' => 'Status',
        'General information' => 'Informasi umum',
        'Settings' => 'Pengaturan',
        'Scope' => 'Cakupan',
        'Configuration' => 'Konfigurasi',
        'Approval' => 'Persetujuan',
        'Trigger event' => 'Event pemicu',
        'Trigger mode' => 'Mode pemicu',
        'Subject type' => 'Tipe subjek',
        'Subject label' => 'Label subjek',
        'Step' => 'Langkah',
        'Steps' => 'Langkah',
        'Action type' => 'Jenis aksi',
        'Sort order' => 'Urutan',
        'Version' => 'Versi',
        'Current step' => 'Langkah saat ini',
        'Requester' => 'Pemohon',
        'Status before' => 'Status sebelum',
        'Status after' => 'Status sesudah',
        'Workflow version' => 'Versi workflow',
        'Step type' => 'Tipe langkah',
        'To step' => 'Ke langkah',
        'Sla hours' => 'Batas waktu (jam)',
        'Calculation' => 'Kalkulasi',
        'Payment details' => 'Detail pembayaran',
        'Payment method' => 'Metode pembayaran',
        'Loan limits' => 'Batas peminjaman',
        'Quantity and pricing' => 'Jumlah & harga',
        'Is taxable' => 'Kena pajak',
        'Is mandatory' => 'Wajib',
        'Created by' => 'Dibuat oleh',
        'Updated by' => 'Diperbarui oleh',
        'Preferred locale' => 'Bahasa',
        'Language' => 'Bahasa',
        // Collage student typo dipertahankan — alias UI; jangan rename tanpa rename file Resource

        // ── Sprint 2 — Workflow-specific phrases ────────────────────────────────
        'Workflow' => 'Workflow',
        'Workflow instance' => 'Instans workflow',
        'Workflow step' => 'Langkah workflow',
        'Workflow transition' => 'Transisi workflow',
        'Workflow instances' => 'Instans Workflow',
        'Workflow steps' => 'Langkah Workflow',
        'Workflow transitions' => 'Transisi Workflow',
        'Workflow snapshot' => 'Snapshot workflow',
        'Automated actions' => 'Aksi otomatis',
        'Automated action' => 'Aksi otomatis',
        'From step' => 'Dari langkah',
        'Action name' => 'Nama aksi',
        'Assignee type' => 'Tipe penerima',
        'Assignee value' => 'Nilai penerima',
        'Assignee config' => 'Konfigurasi penerima',
        'Is initial' => 'Langkah awal',
        'Is terminal' => 'Langkah akhir',
        'Allow reassign' => 'Izinkan reassign',
        'Allow delegate' => 'Izinkan delegasi',
        'Form schema' => 'Skema form',
        'Action schema' => 'Skema aksi',
        'Accepted types' => 'Tipe yang diizinkan',
        'Max size' => 'Ukuran maksimum',
        'Condition rules' => 'Aturan kondisi',
        'Rule type' => 'Jenis aturan',
        'Transition meta' => 'Meta transisi',
        'Is default' => 'Default',
        'Is global' => 'Global',
        'Is published' => 'Dipublikasikan',
        'Context data' => 'Data konteks',
        'Form data' => 'Data form',
        'Computed data' => 'Data terkomputasi',
        'Current assignees' => 'Penerima saat ini',
        'Completed at' => 'Selesai pada',
        'Request number' => 'Nomor permintaan',
        'Budget code' => 'Kode anggaran',
        'Requested amount' => 'Jumlah diminta',
        'Allocated amount' => 'Jumlah dialokasikan',
        'Assignment role' => 'Peran penugasan',
        'Assigned at' => 'Ditugaskan pada',
        'Assigned to type' => 'Tipe penerima tugas',
        'Assigned user' => 'Pengguna ditugaskan',
        'Log type' => 'Tipe log',
        'Logged at' => 'Dicatat pada',
        'Action taken' => 'Aksi diambil',
        'Actor' => 'Pelaku',
        'Payload before' => 'Payload sebelum',
        'Payload after' => 'Payload sesudah',
        'Form data snapshot' => 'Snapshot data form',
        'Priority' => 'Prioritas',
        'Leave blank for tenant-wide workflow' => 'Kosongkan untuk workflow tenant-wide',
        'Enter the FQCN of the subject model' => 'Isi FQCN model subject',
        'Use valid JsonLogic format' => 'Gunakan format JsonLogic yang valid',
        'Reason for reassignment' => 'Alasan pemindahan',
        'Reassign' => 'Pindahkan',
        'Target user' => 'Pengguna tujuan',
        'Publish' => 'Publikasikan',
        'Archive' => 'Arsipkan',
        'Duplicate as New Version' => 'Duplikat sebagai Versi Baru',
        'Open Subject' => 'Buka Subjek',
        'Return To Step' => 'Kembalikan ke Langkah',
        'Target Step' => 'Langkah Tujuan',
        'Workflow Note' => 'Catatan Workflow',
    ];

    /**
     * Word-by-word translations used as a fallback when a phrase is not in the map.
     *
     * @var array<string, string>
     */
    protected const WORDS = [
        'academic' => 'akademik',
        'accreditation' => 'akreditasi',
        'account' => 'akun',
        'achievement' => 'prestasi',
        'admission' => 'penerimaan',
        'address' => 'alamat',
        'advisor' => 'pembimbing',
        'allow' => 'izinkan',
        'amount' => 'jumlah',
        'applicant' => 'pendaftar',
        'assessment' => 'asesmen',
        'assistant' => 'asisten',
        'attendance' => 'kehadiran',
        'author' => 'penulis',
        'available' => 'tersedia',
        'average' => 'rata-rata',
        'award' => 'penghargaan',
        'bank' => 'bank',
        'billing' => 'penagihan',
        'book' => 'buku',
        'brand' => 'merek',
        'budget' => 'anggaran',
        'category' => 'kategori',
        'certificate' => 'sertifikat',
        'chart' => 'bagan',
        'class' => 'kelas',
        'code' => 'kode',
        'collage' => 'perguruan tinggi',
        'color' => 'warna',
        'component' => 'komponen',
        'contact' => 'kontak',
        'contract' => 'kontrak',
        'country' => 'negara',
        'course' => 'mata kuliah',
        'created' => 'dibuat',
        'currency' => 'mata uang',
        'current' => 'saat ini',
        'curriculum' => 'kurikulum',
        'date' => 'tanggal',
        'day' => 'hari',
        'department' => 'departemen',
        'description' => 'deskripsi',
        'district' => 'kecamatan',
        'document' => 'dokumen',
        'due' => 'jatuh tempo',
        'duration' => 'durasi',
        'education' => 'pendidikan',
        'email' => 'email',
        'employee' => 'karyawan',
        'employment' => 'kepegawaian',
        'end' => 'akhir',
        'entry' => 'entri',
        'exam' => 'ujian',
        'expense' => 'beban',
        'faculty' => 'fakultas',
        'feeder' => 'Feeder',
        'file' => 'berkas',
        'finance' => 'keuangan',
        'first' => 'pertama',
        'frequency' => 'frekuensi',
        'full' => 'lengkap',
        'gender' => 'jenis kelamin',
        'grade' => 'nilai',
        'graduation' => 'kelulusan',
        'guardian' => 'wali',
        'handled' => 'ditangani',
        'health' => 'kesehatan',
        'history' => 'riwayat',
        'id' => 'ID',
        'indicator' => 'indikator',
        'invoice' => 'tagihan',
        'is' => 'status',
        'journal' => 'jurnal',
        'job' => 'pekerjaan',
        'kpi' => 'KPI',
        'language' => 'bahasa',
        'leave' => 'cuti',
        'level' => 'level',
        'lecturer' => 'dosen',
        'library' => 'perpustakaan',
        'loan' => 'peminjaman',
        'log' => 'log',
        'logo' => 'logo',
        'main' => 'utama',
        'member' => 'anggota',
        'module' => 'modul',
        'mother' => 'ibu',
        'name' => 'nama',
        'national' => 'nasional',
        'number' => 'nomor',
        'offering' => 'penawaran',
        'organization' => 'organisasi',
        'parent' => 'induk',
        'payment' => 'pembayaran',
        'period' => 'periode',
        'phone' => 'telepon',
        'plan' => 'rencana',
        'position' => 'jabatan',
        'previous' => 'sebelumnya',
        'principal' => 'kepala',
        'procurement' => 'pengadaan',
        'program' => 'program',
        'province' => 'provinsi',
        'purchase' => 'pembelian',
        'question' => 'pertanyaan',
        'quotation' => 'penawaran',
        'receipt' => 'penerimaan',
        'registration' => 'registrasi',
        'related' => 'terkait',
        'report' => 'laporan',
        'required' => 'wajib',
        'result' => 'hasil',
        'role' => 'peran',
        'route' => 'rute',
        'salary' => 'gaji',
        'schedule' => 'jadwal',
        'school' => 'sekolah',
        'score' => 'nilai',
        'secondary' => 'sekunder',
        'semester' => 'semester',
        'setting' => 'pengaturan',
        'shift' => 'shift',
        'slip' => 'slip',
        'student' => 'siswa',
        'study' => 'studi',
        'subject' => 'mata pelajaran',
        'subscription' => 'langganan',
        'teacher' => 'guru',
        'tenant' => 'tenant',
        'template' => 'template',
        'thesis' => 'tesis',
        'time' => 'waktu',
        'timezone' => 'zona waktu',
        'title' => 'judul',
        'total' => 'total',
        'type' => 'jenis',
        'updated' => 'diperbarui',
        'user' => 'pengguna',
        'vendor' => 'vendor',
        'village' => 'desa',
        'violation' => 'pelanggaran',
        'week' => 'minggu',
        'year' => 'tahun',

        // Fase 1.2 — tambahan kata audit 2026-05-22
        'started' => 'dimulai',
        'finished' => 'selesai',
        'published' => 'dipublikasikan',
        'remarks' => 'catatan',
        'phase' => 'fase',
        'cycle' => 'siklus',
        'mode' => 'mode',
        'gateway' => 'gerbang',
        'quorum' => 'kuorum',
        'assignee' => 'penanggung jawab',
        'approver' => 'penyetuju',
        'reviewer' => 'peninjau',
        'outcome' => 'hasil',
        'branch' => 'cabang',
        'instance' => 'instans',
        'preferred' => 'disukai',
        'locale' => 'bahasa',
        'sla' => 'SLA',
        'rfq' => 'RFQ',
        'kpi' => 'KPI',
        'tax' => 'pajak',
        'discount' => 'diskon',
        'quantity' => 'jumlah',
        'calculation' => 'kalkulasi',
        'approval' => 'persetujuan',
        'requester' => 'pemohon',
        'version' => 'versi',

        // Sprint 2 — Workflow words
        'from' => 'dari',
        'to' => 'ke',
        'rule' => 'aturan',
        'condition' => 'kondisi',
        'trigger' => 'pemicu',
        'transition' => 'transisi',
        'context' => 'konteks',
        'computed' => 'terkomputasi',
        'snapshot' => 'snapshot',
        'actor' => 'pelaku',
        'payload' => 'payload',
        'priority' => 'prioritas',
        'schema' => 'skema',
        'initial' => 'awal',
        'terminal' => 'akhir',
        'delegate' => 'delegasi',
        'reassign' => 'pindahkan',
        'target' => 'tujuan',
        'logged' => 'dicatat',
        'completed' => 'selesai',
        'assigned' => 'ditugaskan',
        'allocation' => 'alokasi',
        'allocated' => 'dialokasikan',
        'request' => 'permintaan',
        'requested' => 'diminta',
    ];

    public static function text(string $value): string
    {
        if (! static::isIndonesian()) {
            return $value;
        }

        if (array_key_exists($value, static::PHRASES)) {
            return static::PHRASES[$value];
        }

        $normalized = str($value)->replace(['_', '.'], ' ')->squish()->toString();

        if (array_key_exists($normalized, static::PHRASES)) {
            return static::PHRASES[$normalized];
        }

        // Case-insensitive lookup: 'Study Program' should match 'Study program'
        $lowercaseFirst = ucfirst(strtolower($normalized));
        if (array_key_exists($lowercaseFirst, static::PHRASES)) {
            return static::PHRASES[$lowercaseFirst];
        }

        $translated = collect(preg_split('/(\s+)/', $normalized, -1, PREG_SPLIT_DELIM_CAPTURE) ?: [])
            ->map(function (string $token): string {
                if (trim($token) === '') {
                    return $token;
                }

                if (preg_match('/^\s+$/', $token)) {
                    return $token;
                }

                return static::translateWord($token);
            })
            ->implode('');

        return trim($translated);
    }

    public static function field(string $field): string
    {
        $isDottedRelation = str_contains($field, '.');

        $label = str($field)
            ->beforeLast('_id')              // strip FK suffix first
            ->replace('_', ' ')              // snake_case → spaces
            ->replace('.', ' ')              // dot notation → spaces
            ->replaceMatches('/([a-z])([A-Z])/', '$1 $2')  // camelCase → spaces
            ->squish()
            ->toString();

        // Only strip trailing ' name', ' id', ' code' for dotted relation columns
        // like 'studyProgram.name' → 'study program name' → 'study program'
        // But NOT for plain fields like 'full_name' or 'first_name'
        if ($isDottedRelation) {
            $label = preg_replace('/\s+(name|id|code)$/i', '', $label) ?? $label;
        }

        if ($label === '') {
            $label = $field;
        }

        return static::text(Str::headline($label));
    }

    public static function resource(string $modelClass): string
    {
        $basename = class_basename($modelClass);
        $basename = str($basename)->beforeLast('Resource')->toString();

        return static::text(Str::headline($basename));
    }

    public static function resourceIcon(string $modelClass): string|UnitEnum
    {
        $basename = class_basename($modelClass);
        $basename = str($basename)->beforeLast('Resource')->toString();

        return match ($basename) {
            'AcademicPeriod', 'AcademicYear', 'AdmissionPeriod', 'Schedule', 'ExamSchedule', 'CourseOffering', 'Attendance', 'AttendanceLog', 'Shift' => Heroicon::CalendarDays,
            'AchievementType', 'Assessment', 'AssessmentItem', 'StudentAchievement', 'StudentAssessmentAnswer', 'StudentGrade', 'StudyResult', 'KpiIndicator', 'KpiScore', 'Violation', 'ViolationType' => Heroicon::ChartBar,
            'Applicant', 'Registration' => Heroicon::ClipboardDocumentCheck,
            'AuditLog', 'FileUpload', 'Monitoring' => Heroicon::ShieldCheck,
            'Book', 'BookCategory', 'BookCopy', 'Fine', 'Loan', 'Member' => Heroicon::BookOpen,
            'Budget', 'ChartOfAccount', 'JournalEntry', 'JournalEntryLine', 'Payment', 'StudentInvoice', 'StudentInvoiceItem', 'TuitionType' => Heroicon::Banknotes,
            'City', 'Country', 'District', 'Province', 'Timezone', 'Village' => Heroicon::MapPin,
            'CollageStudent', 'Student', 'Teacher', 'Lecturer', 'User' => Heroicon::Users,
            'Course', 'Curriculum', 'Subject', 'ClassStudent', 'SchoolClass' => Heroicon::AcademicCap,
            'Department', 'Employee', 'EmploymentContract', 'LeaveRequest', 'PayrollComponent', 'Position', 'SalarySlip', 'SalarySlipComponent' => Heroicon::Identification,
            'Faculty', 'StudyPlan', 'StudyPlanItem', 'StudyProgram', 'Thesis' => Heroicon::BuildingLibrary,
            'GoodsReceipt', 'GoodsReceiptItem', 'ProcurementCategory', 'ProcurementItem', 'PurchaseOrder', 'PurchaseOrderItem', 'PurchaseRequisition', 'PurchaseRequisitionItem', 'RequestForQuotation', 'RfqItem', 'RfqVendor', 'Vendor', 'VendorBill', 'VendorBillItem' => Heroicon::Truck,
            'Module', 'Organization', 'OrganizationSetting', 'Tenant', 'TenantModule', 'TenantRole', 'TenantSetting', 'UserTenantRole', 'SubscriptionLog', 'SubscriptionPlan' => Heroicon::Cog6Tooth,
            default => static::navigationIcon(static::moduleNameFromClass($modelClass)),
        };
    }

    public static function module(string $module): string
    {
        return static::text(Str::headline($module));
    }

    public static function navigationIcon(string $module): string|UnitEnum
    {
        return match ($module) {
            'Campus' => Heroicon::AcademicCap,
            'Core' => Heroicon::BuildingOffice2,
            'Employee' => Heroicon::Identification,
            'Enrollment' => Heroicon::ClipboardDocumentCheck,
            'Finance' => Heroicon::Banknotes,
            'Global' => Heroicon::GlobeAlt,
            'Library' => Heroicon::BookOpen,
            'Monitoring' => Heroicon::ShieldCheck,
            'Procurement' => Heroicon::Truck,
            'School' => Heroicon::AcademicCap,
            default => Heroicon::RectangleStack,
        };
    }

    protected static function moduleNameFromClass(string $modelClass): string
    {
        return str($modelClass)
            ->after('Modules\\')
            ->before('\\')
            ->toString();
    }

    public static function isIndonesian(): bool
    {
        return app()->getLocale() === 'id';
    }

    protected static function translateWord(string $word): string
    {
        $key = strtolower($word);

        if (array_key_exists($word, static::PHRASES)) {
            return static::PHRASES[$word];
        }

        if (array_key_exists($key, static::WORDS)) {
            $translated = static::WORDS[$key];
            // Preserve initial capitalization from the original word
            if (strlen($word) > 0 && ctype_upper($word[0])) {
                return ucfirst($translated);
            }

            return $translated;
        }

        if (preg_match('/^[A-Z0-9]{2,}$/', $word) === 1) {
            return $word;
        }

        return $word;
    }
}

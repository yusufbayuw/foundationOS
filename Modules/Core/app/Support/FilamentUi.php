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
        'Academic period' => 'Periode akademik',
        'Academic year' => 'Tahun akademik',
        'Admission period' => 'Periode penerimaan',
        'Audit log' => 'Log audit',
        'Attendance log' => 'Log kehadiran',
        'Book category' => 'Kategori buku',
        'Book copy' => 'Eksemplar buku',
        'Budget' => 'Anggaran',
        'Chart of account' => 'Bagan akun',
        'City' => 'Kota',
        'Campus' => 'Kampus',
        'Collage student' => 'Mahasiswa',
        'Country' => 'Negara',
        'Course offering' => 'Penawaran mata kuliah',
        'Course' => 'Mata kuliah',
        'Curriculum' => 'Kurikulum',
        'Department' => 'Departemen',
        'District' => 'Kecamatan',
        'Employee' => 'Karyawan',
        'Employment contract' => 'Kontrak kerja',
        'Exam result' => 'Hasil ujian',
        'Exam schedule' => 'Jadwal ujian',
        'Fine' => 'Denda',
        'File upload' => 'Unggahan berkas',
        'Faculty' => 'Fakultas',
        'Goods receipt' => 'Penerimaan barang',
        'Goods receipt item' => 'Item penerimaan barang',
        'Journal entry' => 'Entri jurnal',
        'Journal entry line' => 'Baris entri jurnal',
        'Kpi indicator' => 'Indikator KPI',
        'Kpi score' => 'Nilai KPI',
        'Kpi template' => 'Template KPI',
        'Leave request' => 'Permohonan cuti',
        'Lecturer' => 'Dosen',
        'Loan' => 'Peminjaman',
        'Member' => 'Anggota',
        'Core' => 'Inti',
        'Employee' => 'Karyawan',
        'Enrollment' => 'Penerimaan',
        'Finance' => 'Keuangan',
        'Global' => 'Global',
        'Library' => 'Perpustakaan',
        'Monitoring' => 'Pemantauan',
        'Module' => 'Modul',
        'Organization' => 'Organisasi',
        'Organization setting' => 'Pengaturan organisasi',
        'Payroll component' => 'Komponen payroll',
        'Payment' => 'Pembayaran',
        'Position' => 'Jabatan',
        'Province' => 'Provinsi',
        'Purchase order' => 'Pesanan pembelian',
        'Purchase order item' => 'Item pesanan pembelian',
        'Purchase requisition' => 'Permintaan pembelian',
        'Purchase requisition item' => 'Item permintaan pembelian',
        'Request for quotation' => 'Permintaan penawaran',
        'Registration' => 'Registrasi',
        'Rfq item' => 'Item RFQ',
        'Rfq vendor' => 'Vendor RFQ',
        'Salary slip' => 'Slip gaji',
        'Salary slip component' => 'Komponen slip gaji',
        'Schedule' => 'Jadwal',
        'School class' => 'Kelas',
        'Shift' => 'Shift',
        'Student achievement' => 'Prestasi siswa',
        'Student assessment answer' => 'Jawaban asesmen siswa',
        'Student grade' => 'Nilai siswa',
        'Student invoice' => 'Tagihan siswa',
        'Student invoice item' => 'Item tagihan siswa',
        'Procurement' => 'Pengadaan',
        'School' => 'Sekolah',
        'Student' => 'Siswa',
        'Study plan' => 'Rencana studi',
        'Study plan item' => 'Item rencana studi',
        'Study program' => 'Program studi',
        'Study result' => 'Hasil studi',
        'Subject' => 'Mata pelajaran',
        'Subscription log' => 'Riwayat langganan',
        'Subscription plan' => 'Paket langganan',
        'Teacher' => 'Guru',
        'Tenant module' => 'Modul tenant',
        'Tenant role' => 'Peran tenant',
        'Tenant setting' => 'Pengaturan tenant',
        'Tenant' => 'Penyewa',
        'Thesis' => 'Tesis',
        'Timezone' => 'Zona waktu',
        'Tuition type' => 'Jenis biaya pendidikan',
        'User tenant role' => 'Peran pengguna tenant',
        'User' => 'Pengguna',
        'Vendor bill' => 'Tagihan vendor',
        'Vendor bill item' => 'Item tagihan vendor',
        'Vendor' => 'Vendor',
        'Village' => 'Desa',
        'Violation type' => 'Jenis pelanggaran',
        'Violation' => 'Pelanggaran',
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
        'color' => 'warna',
        'component' => 'komponen',
        'contact' => 'kontak',
        'contract' => 'kontrak',
        'country' => 'negara',
        'course' => 'mata kuliah',
        'created' => 'dibuat',
        'currency' => 'mata uang',
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
        'file' => 'berkas',
        'finance' => 'keuangan',
        'first' => 'pertama',
        'frequency' => 'frekuensi',
        'gender' => 'jenis kelamin',
        'grade' => 'nilai',
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
        'logo' => 'logo',
        'main' => 'utama',
        'member' => 'anggota',
        'module' => 'modul',
        'mother' => 'ibu',
        'name' => 'nama',
        'national' => 'nasional',
        'number' => 'nomor',
        'organization' => 'organisasi',
        'parent' => 'induk',
        'payment' => 'pembayaran',
        'period' => 'periode',
        'phone' => 'telepon',
        'plan' => 'paket',
        'position' => 'jabatan',
        'previous' => 'sebelumnya',
        'principal' => 'kepala',
        'procurement' => 'pengadaan',
        'province' => 'provinsi',
        'purchase' => 'pembelian',
        'question' => 'pertanyaan',
        'quotation' => 'penawaran',
        'receipt' => 'penerimaan',
        'registration' => 'registrasi',
        'related' => 'terkait',
        'report' => 'laporan',
        'required' => 'wajib',
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
        $label = str($field)
            ->whenContains('.', fn ($string) => $string->before('.'))
            ->beforeLast('_id')
            ->replace('_', ' ')
            ->replaceMatches('/([a-z])([A-Z])/', '$1 $2')
            ->squish()
            ->toString();

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
            return static::WORDS[$key];
        }

        if (preg_match('/^[A-Z0-9]{2,}$/', $word) === 1) {
            return $word;
        }

        return $word;
    }
}

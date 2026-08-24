<?php

return [

    'pdf' => [
        'bulk_sync_limit' => (int) env('FOS_PDF_BULK_SYNC_LIMIT', 50),
    ],

    'product_profiles' => [
        'default' => env('FOS_DEFAULT_PRODUCT_PROFILE', 'school'),

        'profiles' => [
            'school' => [
                'label' => 'Sekolah (K-12)',
                'version' => '1.0',
                'description' => 'Operasional sekolah dari penerimaan sampai evaluasi dan pendampingan siswa.',
                'capabilities' => [
                    'akademik sekolah',
                    'penerimaan siswa',
                    'keuangan',
                    'SDM',
                    'perpustakaan',
                ],
                'setup_tasks' => ['academic_year', 'academic_period'],
                'modules' => [
                    'core',
                    'global',
                    'school',
                    'enrollment',
                    'finance',
                    'employee',
                    'library',
                    'monitoring',
                    'workflow',
                    'messaging',
                    'exam',
                    'counseling',
                ],
            ],

            'campus' => [
                'label' => 'Perguruan Tinggi',
                'version' => '1.0',
                'description' => 'Operasional kampus dari penerimaan, perkuliahan, sampai pelaporan akademik.',
                'capabilities' => [
                    'akademik kampus',
                    'penerimaan mahasiswa',
                    'keuangan',
                    'ujian',
                    'alumni',
                ],
                'setup_tasks' => ['academic_year', 'academic_period'],
                'modules' => [
                    'core',
                    'global',
                    'campus',
                    'enrollment',
                    'finance',
                    'employee',
                    'library',
                    'monitoring',
                    'workflow',
                    'messaging',
                    'exam',
                    'alumni',
                ],
            ],

            'foundation' => [
                'label' => 'Yayasan & Back Office',
                'version' => '1.0',
                'description' => 'Tata kelola lintas unit untuk fungsi bersama dan layanan operasional yayasan.',
                'capabilities' => [
                    'keuangan',
                    'SDM',
                    'pengadaan',
                    'aset',
                    'dokumen',
                ],
                'setup_tasks' => [],
                'modules' => [
                    'core',
                    'global',
                    'finance',
                    'employee',
                    'procurement',
                    'inventory',
                    'asset',
                    'facility',
                    'dms',
                    'eoffice',
                    'monitoring',
                    'workflow',
                    'messaging',
                ],
            ],
        ],
    ],

];

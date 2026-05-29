<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exam_participants', function (Blueprint $table) {
            $table->string('participant_source')->nullable()->after('exam_definition_id');
            $table->unsignedBigInteger('user_reference')->nullable()->after('participant_source');
            $table->unsignedBigInteger('school_student_reference')->nullable()->after('user_reference');
            $table->unsignedBigInteger('campus_student_reference')->nullable()->after('school_student_reference');
            $table->timestamp('assigned_at')->nullable()->after('status');
        });

        Schema::table('exam_participants', function (Blueprint $table) {
            $table->renameColumn('display_name', 'student_name');
            $table->renameColumn('participant_code', 'student_identifier');
            $table->renameColumn('context_reference_id', 'participant_legacy_id');
            $table->renameColumn('context_reference_uuid', 'participant_uuid');
        });

        $this->backfillParticipantFields();

        Schema::table('exam_participants', function (Blueprint $table) {
            $table->dropUnique('exam_participants_exam_definition_id_participant_code_unique');
        });

        Schema::table('exam_participants', function (Blueprint $table) {
            $table->unique(['exam_definition_id', 'school_student_reference'], 'exam_participants_exam_school_student_unique');
            $table->unique(['exam_definition_id', 'campus_student_reference'], 'exam_participants_exam_campus_student_unique');
            $table->unique(['exam_definition_id', 'user_reference'], 'exam_participants_exam_user_unique');
        });
    }

    public function down(): void
    {
        Schema::table('exam_participants', function (Blueprint $table) {
            $table->dropUnique('exam_participants_exam_school_student_unique');
            $table->dropUnique('exam_participants_exam_campus_student_unique');
            $table->dropUnique('exam_participants_exam_user_unique');
        });

        Schema::table('exam_participants', function (Blueprint $table) {
            $table->renameColumn('student_name', 'display_name');
            $table->renameColumn('student_identifier', 'participant_code');
            $table->renameColumn('participant_legacy_id', 'context_reference_id');
            $table->renameColumn('participant_uuid', 'context_reference_uuid');
        });

        Schema::table('exam_participants', function (Blueprint $table) {
            $table->dropColumn([
                'participant_source',
                'user_reference',
                'school_student_reference',
                'campus_student_reference',
                'assigned_at',
            ]);
            $table->unique(['exam_definition_id', 'participant_code']);
        });
    }

    protected function backfillParticipantFields(): void
    {
        $participants = DB::table('exam_participants')->get();

        foreach ($participants as $participant) {
            $source = $this->inferParticipantSource($participant->context_reference_type);
            $schoolRef = null;
            $campusRef = null;
            $userRef = null;

            if ($participant->context_reference_type === 'Modules\\School\\Models\\Student'
                || $participant->context_reference_type === 'school_student') {
                $schoolRef = $participant->participant_legacy_id;
            } elseif ($participant->context_reference_type === 'Modules\\Campus\\Models\\CollageStudent'
                || $participant->context_reference_type === 'campus_student') {
                $campusRef = $participant->participant_legacy_id;
            } elseif ($participant->context_reference_type === 'Modules\\Core\\Models\\User'
                || $participant->context_reference_type === 'user') {
                $userRef = $participant->participant_legacy_id;
            }

            $status = $participant->status === 'registered' ? 'assigned' : $participant->status;

            DB::table('exam_participants')
                ->where('id', $participant->id)
                ->update([
                    'participant_source' => $source ?? 'manual',
                    'school_student_reference' => $schoolRef,
                    'campus_student_reference' => $campusRef,
                    'user_reference' => $userRef,
                    'status' => $status,
                    'assigned_at' => $participant->created_at,
                ]);
        }
    }

    protected function inferParticipantSource(?string $contextReferenceType): ?string
    {
        return match ($contextReferenceType) {
            'Modules\\School\\Models\\Student', 'school_student' => 'school_student',
            'Modules\\Campus\\Models\\CollageStudent', 'campus_student' => 'campus_student',
            'Modules\\Core\\Models\\User', 'user' => 'user',
            default => null,
        };
    }
};

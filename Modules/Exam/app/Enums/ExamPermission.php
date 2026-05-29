<?php

namespace Modules\Exam\Enums;

enum ExamPermission: string
{
    case ViewExam = 'view_exam';
    case CreateExam = 'create_exam';
    case UpdateExam = 'update_exam';
    case DeleteExam = 'delete_exam';
    case PublishExam = 'publish_exam';
    case SyncExamResult = 'sync_exam_result';
    case GradeExamAnswer = 'grade_exam_answer';
    case ExportExamResult = 'export_exam_result';
    case ManageQuestionBank = 'manage_question_bank';
    case ImportQuestion = 'import_question';
    case GenerateQuestionAi = 'generate_question_ai';
    case ManageOsnPrep = 'manage_osn_prep';
    case ManageExamToken = 'manage_exam_token';
    case ManageExamProctor = 'manage_exam_proctor';
    case OpenControlRoom = 'open_control_room';
    case PushExamGradebook = 'push_exam_gradebook';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

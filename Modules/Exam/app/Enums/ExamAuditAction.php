<?php

namespace Modules\Exam\Enums;

enum ExamAuditAction: string
{
    case PublishExam = 'exam.publish';
    case RepublishExam = 'exam.republish';
    case SyncParticipants = 'exam.sync_participants';
    case SyncResults = 'exam.sync_results';
    case GradeAnswer = 'exam.grade_answer';
    case RegenerateToken = 'exam.regenerate_token';
    case ExportResult = 'exam.export_result';
    case GenerateQuestionAi = 'exam.generate_question_ai';
    case OpenControlRoom = 'exam.open_control_room';
}

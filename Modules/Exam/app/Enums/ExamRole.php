<?php

namespace Modules\Exam\Enums;

enum ExamRole: string
{
    case SuperAdmin = 'super_admin';
    case ExamAdmin = 'exam_admin';
    case Teacher = 'teacher';
    case Lecturer = 'lecturer';
    case Proctor = 'proctor';
    case Viewer = 'viewer';
}

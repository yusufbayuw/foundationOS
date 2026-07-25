<?php

namespace Modules\Member\Http\Requests;

use App\Http\Requests\Api\V1\ApiRequest;
use Illuminate\Validation\Rule;

class RegisterMemberRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $memberType = $this->string('member_type')->toString();

        return [
            'member_type' => ['required', 'string', Rule::exists('member_types', 'code')],
            'profile' => ['required', 'array'],
            'profile.graduation_year' => [Rule::requiredIf($memberType === 'alumni'), 'integer', 'digits:4'],
            'profile.occupation' => [Rule::requiredIf($memberType === 'alumni'), 'string', 'max:255'],
            'profile.institution' => [Rule::requiredIf(in_array($memberType, ['teacher', 'lecturer', 'employee'], true)), 'string', 'max:255'],
            'profile.position' => [Rule::requiredIf($memberType === 'employee'), 'string', 'max:255'],
            'profile.employee_number' => [Rule::requiredIf($memberType === 'employee'), 'string', 'max:100'],
            'profile.teacher_number' => [Rule::requiredIf($memberType === 'teacher'), 'string', 'max:100'],
            'profile.lecturer_number' => [Rule::requiredIf($memberType === 'lecturer'), 'string', 'max:100'],
            'profile.student_number' => [Rule::requiredIf(in_array($memberType, ['student', 'college_student'], true)), 'string', 'max:100'],
            'profile.study_program' => [Rule::requiredIf($memberType === 'college_student'), 'string', 'max:255'],
            'proof' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'mimetypes:image/jpeg,image/png,application/pdf', 'max:5120'],
        ];
    }
}

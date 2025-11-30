<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'clock_in' => ['nullable', 'date_format:H:i'],
            'clock_out' => ['nullable', 'date_format:H:i', 'after:clock_in'],
            'break_start.*' => ['nullable', 'date_format:H:i'],
            'break_end.*' => ['nullable', 'date_format:H:i'],
            'remarks' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'clock_in.date_format' => '出勤時間もしくは退勤時間が不適切な値です',
            'clock_out.date_format' => '出勤時間もしくは退勤時間が不適切な値です',
            'clock_out.after' => '出勤時間もしくは退勤時間が不適切な値です',
            'break_start.*.date_format' => '休憩時間が不適切な値です',
            'break_end.*.date_format' => '休憩時間が不適切な値です',
            'remarks.required' => '備考を記入してください',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $clockIn = $this->input('clock_in');
            $clockOut = $this->input('clock_out');
            $breakStarts = $this->input('break_start', []);
            $breakEnds = $this->input('break_end', []);

            // 出勤時間と退勤時間のチェック
            if ($clockIn && $clockOut && $clockIn >= $clockOut) {
                $validator->errors()->add('clock_in', '出勤時間もしくは退勤時間が不適切な値です');
            }

            // 休憩時間のチェック
            foreach ($breakStarts as $index => $breakStart) {
                $breakEnd = $breakEnds[$index] ?? null;
                
                // 休憩開始時間が出勤時間より前の場合
                if ($breakStart && $clockIn && $breakStart < $clockIn) {
                    $validator->errors()->add('break_start.' . $index, '休憩時間が不適切な値です');
                }
                
                // 休憩開始時間が退勤時間より後の場合
                if ($breakStart && $clockOut && $breakStart > $clockOut) {
                    $validator->errors()->add('break_start.' . $index, '休憩時間が不適切な値です');
                }
                
                // 休憩終了時間が退勤時間より後の場合
                if ($breakEnd && $clockOut && $breakEnd > $clockOut) {
                    $validator->errors()->add('break_end.' . $index, '休憩時間もしくは退勤時間が不適切な値です');
                }
                
                // 休憩終了時間が休憩開始時間より前の場合
                if ($breakStart && $breakEnd && $breakEnd <= $breakStart) {
                    $validator->errors()->add('break_end.' . $index, '休憩時間が不適切な値です');
                }
            }
        });
    }
}


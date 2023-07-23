<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'founder_name',
        'company_name',
        'short_note',
        'founder_email',
        'location',
        'founder_phone',
        'business_category',
        'founder_gender',
        'website_url',
        'employee_number',
        'formation_of_company',
        'company_video_link',
        'facebook_link',
        'youtube_link',
        'linkedin_link',
        'attachment_file',
        'batch_id'
    ];

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }
    public function exams()
    {
        return $this->belongsToMany(Exam::class, 'exam_student')->withPivot('score');
    }
}

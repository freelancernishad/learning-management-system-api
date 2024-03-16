<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;


class Student extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory;

    protected $fillable = [
        'founder_name',
        'company_name',
        'short_note',
        'founder_email',
        'password',
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
        'batch_id',
        'ref_code',
        'referedby',
        'balance',
        'refer_count',
        'rating'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    // Required method from JWTSubject
    public function getJWTCustomClaims()
    {
        return [];
    }



    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }
    public function exams()
    {
        return $this->belongsToMany(Exam::class, 'exam_student')->withPivot('score');
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'student_enrollments');
    }


    public function payments()
    {
        return $this->hasMany(Payment::class);
    }



    public function referer()
    {
        return $this->belongsTo(Student::class, 'referedby');
    }

    /**
     * Generate and set a unique referral code based on the founder name.
     */
    public function setRefCodeAttribute($value)
    {
        $refCode = strtolower(str_replace(' ', '', $value)); // Remove spaces and convert to lowercase
        $counter = 1;

        // Check if the generated ref_code is unique, if not, append a counter until it becomes unique
        while (Student::where('ref_code', $refCode)->exists()) {
            $refCode = strtolower(str_replace(' ', '', $value)) . $counter;
            $counter++;
        }

        $this->attributes['ref_code'] = $refCode;
    }
    public function referrals()
    {
        return $this->hasMany(Student::class, 'referedby');
    }


        /**
     * Get the enrollments for the student.
     */
    public function enrollments()
    {
        return $this->hasMany(StudentEnrollment::class);
    }

    /**
     * Get paid students (students who have enrolled in at least one course).
     */
    public static function getPaidStudents()
    {
        return static::has('enrollments')->get();
    }

    public function referredStudents()
    {
        return $this->hasMany(Student::class, 'referedby');
    }

    public function referredPaidStudents()
    {
        return $this->hasMany(Student::class, 'referedby');
    }

}

<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class StudentEnrollment extends Model
{
    use HasFactory;

    protected $table = 'student_enrollments';

    protected $fillable = [
        'student_id',
        'course_id',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    public function courses()
    {
        return $this->hasMany(Course::class,'id','course_id');
    }

}

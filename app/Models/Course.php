<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'course_name',
        'category_id',
    ];

    public function category()
    {
        return $this->belongsTo(CourseCategory::class);
    }

    public function modules()
    {
        return $this->hasMany(CourseModule::class);
    }
    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_enrollments');
    }
}

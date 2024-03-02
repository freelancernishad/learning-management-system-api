<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class Course extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'course_name',
        'course_category_id',
        'instructor',
        'rating',
        'price',
        'previous_price',
        'discount',
        'about_video',
        'targeted_audience',
        'descriptions',
        'requirements',
        'whatUlearn',
        'features',
        'demo_certificate',
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
    protected static function booted()
    {
        static::creating(function ($category) {
            $uuid = Str::uuid();

            // Check if the UUID already exists in the database
            while (static::where('id', $uuid)->exists()) {
                $uuid = Str::uuid(); // Generate a new UUID
            }

            $category->id = $uuid;
        });
    }
}

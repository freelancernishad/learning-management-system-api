<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class CourseModule extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'module_name',
        'course_id',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function videos()
    {
        return $this->hasMany(CourseVideo::class);
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


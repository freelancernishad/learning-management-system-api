<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CourseCategory extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'category_name',
    ];

    public function courses()
    {
        return $this->hasMany(Course::class);
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'name', 'question', 'ans'];

    public function student()
    {
        return $this->belongsTo(Student::class, 'user_id');
    }
}

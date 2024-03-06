<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'trxid',
        'amount',
        'vat',
        'total_amount',
        'mobile_no',
        'payment_wallet',
        'method',
        'mer_trxid',
        'date',
        'status',
        'month',
        'year',
        'payment_type',
        'ipn',
        'payment_url',
        'paymentID',
        'id_token',
        'refresh_token',
        'app_key',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

}

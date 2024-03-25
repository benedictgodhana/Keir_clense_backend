<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'service_id', 'employee_id', 'date_time', 'location', 'contact', 'payment_method', 'status'];


    // Define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Define the relationship with the Service model
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    // Define the relationship with the Employee model
  // Define the relationship with the Employee model
public function employee()
{
    return $this->belongsTo(Employee::class);
}

}

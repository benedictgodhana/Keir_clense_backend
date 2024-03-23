<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description', 'price'];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }


    public function service()
{
    return $this->belongsTo(Service::class); // Replace Service::class with the actual name of your service model
}

}

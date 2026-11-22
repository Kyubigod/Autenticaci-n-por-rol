<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    protected $fillable= [
        'user_id',
        'nombre',
        'faltas',
        'calificacion'
    ];

    public function user() 
    {
        return $this->belongsTo(User::class);
    }

    public function getEquivalenciaAttribute(): string
{
    if ($this->faltas >= 4) {
        return 'NA';
    }

    if ($this->calificacion >= 90) {
        return 'AU';
    } elseif ($this->calificacion >= 80) {
        return 'DE';
    } elseif ($this->calificacion >= 70) {
        return 'SA';
    }

    return 'NA';
}
}


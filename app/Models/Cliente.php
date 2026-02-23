<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = ['nombre', 'telefono', 'email', 'direccion', 'rfc'];

    public function servicios()
    {
        return $this->hasMany(Servicio::class);
    }
}

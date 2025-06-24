<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    protected $fillable = [
        'tipo',
        'nombre',
        'codigo',
        'descripcion',
        'precio_renta',
        'estado'
    ];

    public function atributos(): HasMany
    {
        return $this->hasMany(AtributoProducto::class);
    }

    public function imagenes(): HasMany
    {
        return $this->hasMany(ImagenProducto::class);
    }

    public function getImagenPrincipalAttribute()
    {
        return $this->imagenes()->where('es_principal', true)->first() 
            ?? $this->imagenes->first();
    }

    public function scopeDisponibles($query)
    {
        return $query->where('estado', 'disponible');
    }

    public function itemsRenta(): HasMany
    {
        return $this->hasMany(ItemRenta::class);
    }
}

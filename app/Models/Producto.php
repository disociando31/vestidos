<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    // ✅ Nueva relación válida para eager loading
    public function imagenPrincipal(): HasOne
    {
        return $this->hasOne(ImagenProducto::class)->where('es_principal', true);
    }

    public function itemsRenta(): HasMany
    {
        return $this->hasMany(ItemRenta::class);
    }

    public function scopeDisponibles($query)
    {
        return $query->where('estado', 'disponible');
    }

    public function scopeRentados($query)
    {
        return $query->where('estado', 'rentado');
    }

    public function scopeEnMantenimiento($query)
    {
        return $query->where('estado', 'mantenimiento');
    }

    public function marcarComoRentado()
    {
        $this->estado = 'rentado';
        $this->save();
    }

    public function marcarComoDisponible()
    {
        $this->estado = 'disponible';
        $this->save();
    }
}
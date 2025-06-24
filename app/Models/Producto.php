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

    public function itemsRenta(): HasMany
    {
        return $this->hasMany(ItemRenta::class);
    }

    public function getImagenPrincipalAttribute()
    {
        return $this->imagenes()->where('es_principal', true)->first() ?? $this->imagenes->first();
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

    /**
     * ✅ Método para marcar el producto como rentado
     */
    public function marcarComoRentado()
    {
        $this->estado = 'rentado';
        $this->save();
    }

    /**
     * ✅ Método para marcar el producto como disponible
     */
    public function marcarComoDisponible()
    {
        $this->estado = 'disponible';
        $this->save();
    }
}

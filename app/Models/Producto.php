<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    protected $fillable = [
        'tipo',         // traje, vestido, vestido_15
        'nombre',
        'codigo',       // VES001, TRA002, etc.
        'descripcion',
        'precio_renta',
        'estado'        // disponible, rentado, mantenimiento
    ];
    
    /**
     * Relación con los atributos del producto
     */
    public function atributos(): HasMany
    {
        return $this->hasMany(AtributoProducto::class);
    }
    
    /**
     * Relación con las imágenes del producto
     */
    public function imagenes(): HasMany
    {
        return $this->hasMany(ImagenProducto::class);
    }
    
    /**
     * Relación con los items de renta
     */
    public function itemsRenta(): HasMany
    {
        return $this->hasMany(ItemRenta::class);
    }
    
    /**
     * Accesor para obtener la imagen principal
     */
    public function getImagenPrincipalAttribute()
    {
        return $this->imagenes()->where('es_principal', true)->first() 
            ?? $this->imagenes->first();
    }
    
    /**
     * Scope para productos disponibles
     */
    public function scopeDisponibles($query)
    {
        return $query->where('estado', 'disponible');
    }
    
    /**
     * Scope para productos rentados
     */
    public function scopeRentados($query)
    {
        return $query->where('estado', 'rentado');
    }
    
    /**
     * Scope para productos en mantenimiento
     */
    public function scopeEnMantenimiento($query)
    {
        return $query->where('estado', 'mantenimiento');
    }
    
    /**
     * Marcar producto como rentado
     */
    public function marcarComoRentado()
    {
        $this->estado = 'rentado';
        $this->save();
    }
    
    /**
     * Marcar producto como disponible
     */
    public function marcarComoDisponible()
    {
        $this->estado = 'disponible';
        $this->save();
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo',
        'nombre',
        'codigo',
        'descripcion',
        'precio_renta',
        'estado',
        'rentado',
        'fecha_disponible',
    ];

    protected $dates = [
        'fecha_disponible' => 'datetime',
    ];

    // Relación con imagen principal (una imagen destacada)
    public function imagenPrincipal()
    {
        return $this->hasOne(ImagenProducto::class)->where('es_principal', true);
    }

    // Relación con todas las imágenes del producto
    public function imagenes()
    {
        return $this->hasMany(ImagenProducto::class);
    }
    public function getImagenPrincipalAttribute()
    {
    // Si no hay principal, toma la primera imagen, si no, null
    return $this->imagenPrincipal()->first() ?? $this->imagenes()->first();
    }

    // Relación con atributos (adicionales, ej: zapatos, camisas)
    public function atributos()
    {
        return $this->hasMany(AtributoProducto::class);
    }

    // Relación con rentas
    public function rentas()
    {
        return $this->belongsToMany(Renta::class, 'item_rentas');
    }

    // Marcar producto como rentado y establecer cuándo estará disponible nuevamente
    public function marcarComoRentado($fechaDisponible = null)
    {
        $this->estado = 'rentado';
        $this->fecha_disponible = $fechaDisponible ?? Carbon::now()->addDays(3); // por defecto, 3 días
        $this->save();
    }

    // Verificar si está disponible para renta
public function estaDisponible()
{
    if ($this->estado === 'disponible') {
        return true;
    }

    if ($this->estado === 'rentado' && $this->fecha_disponible) {
        return Carbon::parse($this->fecha_disponible)->isPast();
    }

    return false;
}

    // Accesor para obtener precio con atributos adicionales (si deseas usarlo en facturas)
    public function getPrecioConAtributosAttribute()
    {
        $atributos = $this->atributos->pluck('valor');
        $precioAtributos = $atributos->sum();
        return $this->precio_renta + $precioAtributos;
    }
}

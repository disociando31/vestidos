<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio_renta',
        'estado',
        'disponible_desde',
    ];

    protected $dates = [
        'disponible_desde',
    ];

    // Relación con imagen principal (una imagen destacada)
    public function imagenPrincipal()
    {
        return $this->hasOne(ImagenProducto::class)->where('principal', true);
    }

    // Relación con todas las imágenes del producto
    public function imagenes()
    {
        return $this->hasMany(ImagenProducto::class);
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
        $this->disponible_desde = $fechaDisponible ?? Carbon::now()->addDays(3); // por defecto, 3 días
        $this->save();
    }

    // Verificar si está disponible para renta
    public function estaDisponible()
    {
        if ($this->estado === 'disponible') {
            return true;
        }

        // Si la fecha de disponibilidad ya pasó, actualizar estado
        if ($this->estado === 'rentado' && $this->disponible_desde && $this->disponible_desde->isPast()) {
            $this->estado = 'disponible';
            $this->save();
            return true;
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

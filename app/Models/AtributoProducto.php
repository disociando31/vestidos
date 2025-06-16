<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AtributoProducto extends Model
{
    protected $fillable = [
        'producto_id',
        'nombre',      // Ejemplo: color, talla, tipo, material
        'valor',       // Ejemplo: rojo, M, traje de gala, seda
        'tipo',        // Opcional: texto, numero, seleccion
        'opciones'     // Opcional: JSON para seleccion multiple
    ];

    protected $casts = [
        'opciones' => 'array'
    ];

    /**
     * Relación con el producto al que pertenece
     */
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    /**
     * Scope para atributos de tipo específico
     */
    public function scopeDeTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * Obtener opciones como array
     */
    public function getOpcionesArrayAttribute()
    {
        return $this->opciones ?? [];
    }

    /**
     * Verificar si el atributo es de selección
     */
    public function esDeSeleccion(): bool
    {
        return $this->tipo === 'seleccion';
    }
}
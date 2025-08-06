<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Renta extends Model
{
    protected $fillable = [
        'cliente_id',
        'fecha_renta',
        'fecha_devolucion',
        'monto_total',
        'monto_pagado',
        'estado',       // pendiente, parcial, pagado, devuelto, atrasado
        'notas',        // Notas varias
        'recibido_por', // Quién registró la renta
        'adicionales',  // JSON de adicionales: trajes, extras, etc
    ];

    protected $casts = [
        'fecha_renta' => 'datetime',
        'fecha_devolucion' => 'datetime',
        'adicionales' => 'array',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'item_rentas');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ItemRenta::class);
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class);
    }

    // Suma productos + adicionales (usado para mostrar/calcular total real)
    public function getTotalConAdicionalesAttribute(): float
    {
        $total = 0;
        // Suma productos rentados
        foreach ($this->items as $item) {
            $total += ($item->precio_unitario ?? 0) * ($item->cantidad ?? 1);
        }
        // Suma adicionales
        if (is_array($this->adicionales)) {
            foreach ($this->adicionales as $ad) {
                $total += isset($ad['precio']) ? floatval($ad['precio']) : 0;
            }
        }
        return $total;
    }

    // Solo la suma de productos
    public function getTotalProductosAttribute(): float
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += ($item->precio_unitario ?? 0) * ($item->cantidad ?? 1);
        }
        return $total;
    }

    // Solo la suma de adicionales
    public function getTotalAdicionalesAttribute(): float
    {
        $total = 0;
        if (is_array($this->adicionales)) {
            foreach ($this->adicionales as $ad) {
                $total += isset($ad['precio']) ? floatval($ad['precio']) : 0;
            }
        }
        return $total;
    }

    // Helper: Adicionales limpios (array siempre)
    public function getAdicionalesCleanAttribute()
    {
        if (is_array($this->adicionales)) {
            return $this->adicionales;
        }
        if (is_string($this->adicionales)) {
            return json_decode($this->adicionales, true) ?? [];
        }
        return [];
    }

    // Saldo pendiente (campo virtual, ya existe)
    public function getSaldoAttribute(): float
    {
        return $this->monto_total - $this->monto_pagado;
    }

    // Estado inteligente (ajustado a los pagos y fechas)
    public function actualizarEstado(): void
    {
        if ($this->monto_pagado >= $this->monto_total) {
            $this->estado = 'pagado';
        } elseif ($this->monto_pagado > 0) {
            $this->estado = 'parcial';
        } else {
            $this->estado = 'pendiente';
        }

        if (Carbon::now()->gt($this->fecha_devolucion)) {
            if (!in_array($this->estado, ['devuelto', 'pagado'])) {
                $this->estado = 'atrasado';
            }
        }

        $this->save();
    }
}

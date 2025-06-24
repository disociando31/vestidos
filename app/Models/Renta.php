<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\CastsAttribute;
class Renta extends Model
{
    protected $fillable = [
        'cliente_id',
        'fecha_renta',
        'fecha_devolucion',
        'monto_total',
        'monto_pagado',
        'estado',       // pendiente, parcial, pagado, devuelto, atrasado
        'notas',  // Módera u otros
        'recibido_por'  // Quién registró la renta
    ];
    
    protected $casts = [
        'fecha_renta' => 'datetime',
        'fecha_devolucion' => 'datetime',
    ];
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'item_rentas'); // Ajusta si usas otro nombre de tabla pivot
    }
    public function items(): HasMany
    {
        return $this->hasMany(ItemRenta::class);
    }
    
    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class);
    }
    
    public function getSaldoAttribute(): float
    {
        return $this->monto_total - $this->monto_pagado;
    }
    
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
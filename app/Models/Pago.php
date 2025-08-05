<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Pago extends Model
{
    protected $fillable = [
        'renta_id',
        'monto',
        'metodo_pago',
        'notas',
        'recibido_por',
        'fecha_pago'
    ];

    public function renta(): BelongsTo
    {
        return $this->belongsTo(Renta::class);
    }

    public function cliente(): HasOneThrough
    {
        return $this->hasOneThrough(
            Cliente::class,
            Renta::class,
            'id',         // Foreign key on Rentas
            'id',         // Foreign key on Clientes
            'renta_id',   // Local key on Pagos
            'cliente_id'  // Local key on Rentas
        );
    }
}

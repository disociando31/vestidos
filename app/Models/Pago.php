<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pago extends Model
{
    protected $fillable = [
        'renta_id',
        'monto',
        'metodo_pago',
        'notas',
        'recibido_por'
    ];
    
    public function renta(): BelongsTo
    {
        return $this->belongsTo(Renta::class);
    }
    
    public function cliente()
    {
        return $this->hasOneThrough(
            Cliente::class,
            Renta::class,
            'id',
            'id',
            'renta_id',
            'cliente_id'
        );
    }
}

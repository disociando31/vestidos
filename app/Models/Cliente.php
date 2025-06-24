<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use App\Models\Renta;
use App\Models\Pago;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Cliente extends Model
{
    protected $fillable = [
        'nombre',
        'email',
        'telefono',
        'direccion',
        'fecha_registro',
        'dias_atraso' // Nuevo campo según tus requerimientos
    ];

    protected $casts = [
        'fecha_registro' => 'date'
    ];
    
    public function pagos(): HasManyThrough
    {
        return $this->hasManyThrough(
            Pago::class,
            Renta::class,
            'cliente_id',
            'renta_id',
            'id',
            'id'
        );
    }

    public function rentas(): HasMany
    {
        return $this->hasMany(Renta::class, 'cliente_id');
    }
    
    public function getRentasAtrasadasAttribute()
    {
        return $this->rentas()
            ->where('fecha_devolucion', '<', now())
            ->where('estado', '!=', 'devuelto')
            ->get();
    }
    
    public function actualizarDiasAtraso(): void
    {
        $rentasAtrasadas = $this->rentasAtrasadas;
        $dias = 0;
        
        foreach ($rentasAtrasadas as $renta) {
            $dias += now()->diffInDays(Carbon::parse($renta->fecha_devolucion));
        }
        
        $this->dias_atraso = $dias;
        $this->save();
    }
}
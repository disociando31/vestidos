<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemRenta extends Model
{
    protected $table = 'item_rentas';

    protected $fillable = [
        'renta_id',
        'producto_id',
        'precio_unitario',
        'camisa_color',
        'zapatos_color',
        'zapatos_talla',
        'cartera_color',
        'otro_nombre',
        'otro_precio',
    ];

    public function renta()
    {
        return $this->belongsTo(Renta::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImagenProducto extends Model
{
    protected $fillable = ['producto_id', 'ruta', 'es_principal'];
    
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }
}

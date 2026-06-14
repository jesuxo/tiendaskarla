<?php
// app/Models/GrupoDescuento.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrupoDescuento extends Model
{
    use HasFactory;

    protected $table = 'grupos_descuento';
    protected $fillable = ['nombre', 'descripcion', 'activo', 'comercial', 'porcentaje_descuento'];

    // Relación con productos
    public function productos()
    {
        return $this->belongsToMany(Saprod::class, 'producto_grupo_descuento', 'grupo_id', 'codprod', 'id', 'codprod')
            ->withPivot('precio_final', 'tasa_cambio_usd', 'creado_por')
            ->withTimestamps();
    }

    // Obtener el factor de descuento
    public function getFactorDescuentoAttribute()
    {
        return 1 - ($this->porcentaje_descuento / 100);
    }

    // Calcular precio con descuento
    public function calcularPrecioConDescuento($precioUsd, $tasa)
    {
        $precioBase = $precioUsd * $tasa;
        $precioConDescuento = $precioBase * $this->factor_descuento;
        return round($precioConDescuento);
    }

    // Scope para filtrar por comercial
    public function scopePorComercial($query, $comercialId = null)
    {
        $comercialId = $comercialId ?: session('comercialid');
        return $query->where('comercial', $comercialId);
    }

    // Scope para activos
    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }
}

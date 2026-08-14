<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saprod extends Model
{
    use HasFactory;

    protected $table    = 'saprod';
    protected $fillable = ['codprod','descrip','descrip2','descrip3',
                          'marca','color','refere','codinst','observaciones','activo',
                          'esexento','exdecimal','cantxempaq','volumen','peso','unidad',
                          'preciod','preciod2','costod','costod2','costod3'];

    public function instancia(){
        $comercial = session('comercialid') ;
        return $this->belongsTo(Sainsta::class, 'codinst', 'codinst')->where('comercial',$comercial);
    }

    public function existencias(){
        return $this->hasMany(Saexis::class, 'codprod', 'codprod');
    }

    public function sucursales  (){
        return $this->hasMany(Saprodsucursal::class, 'codprod', 'codprod');
    }

    public function comercial  (){
        return $this->belongsTo(Sacomercial::class, 'comercial', 'id');
    }

    // NUEVAS RELACIONES PARA GRUPOS DE DESCUENTO
    public function gruposDescuento()
    {
        return $this->belongsToMany(GrupoDescuento::class, 'producto_grupo_descuento', 'codprod', 'grupo_id', 'codprod', 'id')
            ->withPivot('precio_final', 'tasa_cambio_usd', 'creado_por')
            ->withTimestamps();
    }

    // Método para obtener el precio especial en un grupo específico
    public function getPrecioEspecialEnGrupo($grupoId)
    {
        $pivot = $this->gruposDescuento()->where('grupo_id', $grupoId)->first();
        return $pivot ? $pivot->pivot->precio_final : null;
    }

    public function imagenes()
    {
        return $this->hasMany(SaprodImagen::class, 'codprod', 'codprod')
            ->where('comercial', $this->comercial)
            ->orderBy('orden', 'asc');
    }

    public function imagenPrincipal()
    {
        return $this->hasOne(SaprodImagen::class, 'codprod', 'codprod')
            ->where('comercial', $this->comercial)
            ->where('tipo', 'principal')
            ->where('activo', 1);
    }

    public function imagenesSecundarias()
    {
        return $this->hasMany(SaprodImagen::class, 'codprod', 'codprod')
            ->where('comercial', $this->comercial)
            ->where('tipo', 'secundaria')
            ->where('activo', 1)
            ->orderBy('orden', 'asc');
    }

    public function thumbnail()
    {
        return $this->hasOne(SaprodImagen::class, 'codprod', 'codprod')
            ->where('comercial', $this->comercial)
            ->where('tipo', 'thumbnail')
            ->where('activo', 1);
    }

    public function icono()
    {
        return $this->hasOne(SaprodImagen::class, 'codprod', 'codprod')
            ->where('comercial', $this->comercial)
            ->where('tipo', 'icono')
            ->where('activo', 1);
    }
}

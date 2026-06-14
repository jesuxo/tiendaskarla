<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CWVehiculo extends Model
{
    use HasFactory;
    protected $table    = 'CWVehiculo';
    protected $fillable = ['codclie', 'fk_tipo', 'modelo', 'marca', 'identificacion', 'year', 'observaciones', 'serialchasis', 'serialmotor'];

    public function cliente  (){
        return $this->hasOne(Saclie::class, 'codclie', 'codclie');
    }

    public function tipo  (){
        return $this->hasOne(CWTipoVehiculo::class, 'id', 'fk_tipo');
    }

    public function getFormattedDateAttribute()
    {
        return Carbon::parse($this->attributes['created_at'])->format('d/m/Y');
    }
}

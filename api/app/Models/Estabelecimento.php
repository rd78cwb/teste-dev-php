<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Estabelecimento extends Model
{
    use SoftDeletes;

    protected $table = 'estabelecimentos';

    protected $fillable = [
        'uuid',
        'tipo',
        'documento',
        'nome',
        'contato',
        'email',
        'telefone',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }
}

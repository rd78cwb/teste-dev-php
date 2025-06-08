<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Estabelecimento extends Model
{
    use HasFactory, SoftDeletes;

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

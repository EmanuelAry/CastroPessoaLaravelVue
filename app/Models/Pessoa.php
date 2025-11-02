<?php

namespace App\Models;

use GuzzleHttp\Promise\Create;
use Illuminate\Database\Eloquent\Model;

class Pessoa extends Model{
    protected $fillable = [
        'nome',
        'cpf',
        'tipo',
        'telefone',
        'email',
    ];
}

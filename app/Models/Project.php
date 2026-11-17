<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $table = 'projects';

    public $timestamps = false;

    protected $fillable = [
        'client_id',
        'judul',
        'deskripsi',
        'kategori',
        'budget_min',
        'budget_max',
        'batas_lelang',
        'status',
    ];

    // Project dimiliki oleh satu client
    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    // Project memiliki banyak bid
    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class, 'project_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bid extends Model
{
    protected $table = 'bids';

    public $timestamps = false;

    protected $fillable = [
        'project_id',
        'developer_id',
        'harga',
        'durasi',
        'proposal',
        'is_winner',
    ];

    // Bid dimiliki oleh satu project
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    // Bid diajukan oleh satu developer
    public function developer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'developer_id');
    }
}

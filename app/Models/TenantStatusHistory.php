<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'changed_by_user_id',
        'previous_status',
        'new_status',
        'reason',
        'notes',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array'
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'changed_by_user_id');
    }

    public static function logStatusChange(
        int $tenantId, 
        ?string $previousStatus, 
        string $newStatus, 
        int $changedByUserId, 
        ?string $reason = null, 
        ?string $notes = null,
        ?array $metadata = null
    ): self {
        return self::create([
            'tenant_id' => $tenantId,
            'changed_by_user_id' => $changedByUserId,
            'previous_status' => $previousStatus,
            'new_status' => $newStatus,
            'reason' => $reason,
            'notes' => $notes,
            'metadata' => $metadata
        ]);
    }

    public function getFormattedChangeAttribute(): string
    {
        $previous = $this->previous_status ?? 'none';
        return "{$previous} → {$this->new_status}";
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->new_status) {
            'active' => 'text-green-600',
            'inactive' => 'text-red-600',
            'suspended' => 'text-yellow-600',
            'pending' => 'text-blue-600',
            default => 'text-gray-600'
        };
    }

    public function scopeForTenant($query, int $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('new_status', $status);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}

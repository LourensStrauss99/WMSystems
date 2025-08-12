<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TenantCommunication extends Model
{
    use HasFactory;

    protected $connection = 'mysql'; // Always use central database

    protected $fillable = [
        'tenant_id',
        'initiated_by_user_id',
        'assigned_to_user_id',
        'subject',
        'category',
        'priority',
        'status',
        'tags',
        'resolved_at',
        'first_response_at',
        'last_activity_at'
    ];

    protected $casts = [
        'tags' => 'array',
        'resolved_at' => 'datetime',
        'first_response_at' => 'datetime',
        'last_activity_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($communication) {
            $communication->last_activity_at = now();
        });

        static::updating(function ($communication) {
            $communication->last_activity_at = now();
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function initiatedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'initiated_by_user_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'assigned_to_user_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(CommunicationMessage::class);
    }

    public function latestMessage(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(CommunicationMessage::class)->latest();
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isResolved(): bool
    {
        return $this->status === 'resolved';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    public function isHighPriority(): bool
    {
        return $this->priority === 'high';
    }

    public function markAsResolved(): void
    {
        $this->update([
            'status' => 'resolved',
            'resolved_at' => now()
        ]);
    }

    public function markAsClosed(): void
    {
        $this->update([
            'status' => 'closed'
        ]);
    }

    public function reopen(): void
    {
        $this->update([
            'status' => 'open',
            'resolved_at' => null
        ]);
    }

    public function addMessage(string $message, int $userId): CommunicationMessage
    {
        $messageModel = $this->messages()->create([
            'user_id' => $userId,
            'message' => $message,
            'is_internal' => false
        ]);

        if (!$this->first_response_at && $userId !== $this->initiated_by_user_id) {
            $this->update(['first_response_at' => now()]);
        }

        return $messageModel;
    }

    public function addInternalNote(string $note, int $userId): CommunicationMessage
    {
        return $this->messages()->create([
            'user_id' => $userId,
            'message' => $note,
            'is_internal' => true
        ]);
    }

    public function getResponseTimeAttribute(): ?int
    {
        if (!$this->first_response_at) {
            return null;
        }

        return $this->created_at->diffInMinutes($this->first_response_at);
    }

    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'low' => 'text-green-600',
            'medium' => 'text-yellow-600',
            'high' => 'text-red-600',
            default => 'text-gray-600'
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'open' => 'text-blue-600',
            'resolved' => 'text-green-600',
            'closed' => 'text-gray-600',
            default => 'text-gray-600'
        };
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'high');
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }
}

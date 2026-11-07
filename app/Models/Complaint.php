<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'user_id',
        'category_id',
        'custom_category',
        'title',
        'incident_location',
        'incident_date',
        'description',
        'attachment',
        'status',
        'admin_notes',
        'resolution_proof',
        'resolved_at',
    ];

    protected $casts = [
        'incident_date' => 'date',
        'resolved_at' => 'datetime',
    ];

    public static function generateTicketNumber(): string
    {
        $year = date('Y');
        $prefix = "SIP-{$year}-";

        // Cari nomor urut terakhir pada tahun ini
        $lastTicket = self::where('ticket_number', 'LIKE', "{$prefix}%")
            ->orderBy('ticket_number', 'desc')
            ->value('ticket_number');

        if ($lastTicket) {
            $number = intval(substr($lastTicket, strlen($prefix))) + 1;
        } else {
            $number = 1;
        }

        return $prefix . str_pad((string) $number, 6, '0', STR_PAD_LEFT);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(ComplaintHistory::class)->orderBy('created_at', 'asc');
    }

    public function getCategoryLabelAttribute(): string
    {
        if ($this->category) {
            return $this->category->name;
        }

        return $this->custom_category ?: 'Lainnya';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'baru' => 'Pengaduan Baru',
            'diverifikasi' => 'Diverifikasi',
            'diproses' => 'Sedang Diproses',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
            default => ucfirst($this->status),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'baru' => 'bg-amber-100 text-amber-800 border-amber-200',
            'diverifikasi' => 'bg-cyan-100 text-cyan-800 border-cyan-200',
            'diproses' => 'bg-blue-100 text-blue-800 border-blue-200',
            'selesai' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'ditolak' => 'bg-rose-100 text-rose-800 border-rose-200',
            default => 'bg-gray-100 text-gray-800 border-gray-200',
        };
    }

    public function getTimelineStepAttribute(): int
    {
        return match ($this->status) {
            'baru' => 1,
            'diverifikasi' => 2,
            'diproses' => 3,
            'selesai' => 4,
            'ditolak' => 4,
            default => 1,
        };
    }
}


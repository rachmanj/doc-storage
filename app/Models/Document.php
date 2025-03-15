<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'original_filename',
        'filename',
        'path',
        'mime_type',
        'size',
        'extension',
        'invoice_id',
        'access_token',
        'token_expires_at',
        'is_public',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'size' => 'integer',
        'is_public' => 'boolean',
        'token_expires_at' => 'datetime',
    ];

    /**
     * Generate a unique access token for the document.
     *
     * @return string
     */
    public static function generateAccessToken(): string
    {
        return md5(uniqid() . time() . rand(1000, 9999));
    }

    /**
     * Check if the document's access token is expired.
     *
     * @return bool
     */
    public function isTokenExpired(): bool
    {
        if (!$this->token_expires_at) {
            return false;
        }

        return now()->gt($this->token_expires_at);
    }
}

<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class EmailVerification extends Model
{
    protected $table = 'email_verifications';
    
    protected $fillable = [
        'email',
        'otp',
        'expires_at',
        'is_verified'
    ];
    
    protected $casts = [
        'expires_at' => 'datetime',
        'is_verified' => 'boolean',
    ];
    
    public static function generateOTP(): string
    {
        return str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }
    
    /**
     * Create or update OTP for email
     */
    public static function createOrUpdate(string $email): self // Idinagdag ang string type
    {
        self::where('email', $email)->delete();
        
        return self::create([
            'email' => $email,
            'otp' => self::generateOTP(),
            'expires_at' => now()->addMinutes(10),
            'is_verified' => false,
        ]);
    }
    
    /**
     * Verify OTP
     */
    public static function verify(string $email, string $otp): bool // Idinagdag ang string types
    {
        $verification = self::where('email', $email)
            ->where('otp', $otp)
            ->where('expires_at', '>', now())
            ->where('is_verified', false)
            ->first();
        
        if ($verification) {
            $verification->update(['is_verified' => true]);
            return true;
        }
        
        return false;
    }
}
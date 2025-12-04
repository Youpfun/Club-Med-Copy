<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\TwoFactorCodeMail;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'genre', 'datenaissance', 
        'telephone', 'numrue', 'nomrue', 'codepostal', 'ville',
        'two_factor_code', 'two_factor_expires_at', 'two_factor_preference'
    ];

    protected $hidden = ['password', 'remember_token'];
    
    protected $casts = [
        'email_verified_at' => 'datetime',
        'two_factor_expires_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function generateTwoFactorCode()
    {
        $this->timestamps = false;
        $this->two_factor_code = rand(100000, 999999);
        $this->two_factor_expires_at = now()->addMinutes(10);
        $this->save();
    }

    public function resetTwoFactorCode()
    {
        $this->timestamps = false;
        $this->two_factor_code = null;
        $this->two_factor_expires_at = null;
        $this->save();
    }

    public function sendTwoFactorCode()
    {
        $this->generateTwoFactorCode();

        if ($this->two_factor_preference === 'sms') {
            Log::info("SMS envoyé au {$this->telephone} : Votre code de validation est {$this->two_factor_code}");
        } else {
            try {
                Mail::to($this->email)->send(new TwoFactorCodeMail($this->two_factor_code));
            } catch (\Exception $e) {
                Log::error("Erreur envoi mail: " . $e->getMessage());
            }
        }
    }
}
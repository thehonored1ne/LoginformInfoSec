<?php

namespace App\Models;

// We must use Authenticatable instead of the base Model for logins to work
use Illuminate\Foundation\Auth\User as Authenticatable; // Importing the base User class from Laravel's authentication system to extend it for our custom UserModel.
use Illuminate\Notifications\Notifiable; // Importing the Notifiable trait to allow our UserModel to send notifications (like password resets).
use Illuminate\Database\Eloquent\Factories\HasFactory; // Importing the HasFactory trait to enable factory-based testing and seeding for our UserModel.
use Database\Factories\NewUserFactory;

class UserModel extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     * These are the fields we are sending from the Controller.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'salt',
    ];

    /**
     * The attributes that should be hidden for serialization.
     * This keeps your password and token out of JSON responses.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // This method is required by Laravel's authentication system. It tells Laravel to use the 'password' field as the password for authentication purposes, which in our case is already a hashed value.
    public function getAuthPassword()
    {
        // Tells Laravel to just use the raw string in the 'password' column.
        return $this->password;
    }

    protected static function newFactory()
    {
        return NewUserFactory::new();
    }
}
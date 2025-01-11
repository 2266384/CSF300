<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;



    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $guarded = [
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }


    /**
     * Functions for relationships
     */

    /**
     * Polymorphic Relation for User to be recorded in both Need and Service Class
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function updatedneeds() {
        return $this->morphMany(Need::class, 'lastupdate', 'lastupdate_type', 'lastupdate_id', 'id');
    }

    public function updatedservices() {
        return $this->morphMany(Service::class, 'lastupdate', 'lastupdate_type', 'lastupdate_id', 'id');
    }

}

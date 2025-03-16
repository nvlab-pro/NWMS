<?php

namespace App\Models;

use App\Orchid\Presenters\UserPresenter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Orchid\Access\UserAccess;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;
use Orchid\Metrics\Chartable;
use Orchid\Platform\Models\User as Authenticatable;
use Orchid\Screen\AsSource;

class User extends Authenticatable
{
    use AsSource, Chartable, Filterable, Attachable, HasFactory, Notifiable, UserAccess, Chartable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'lang',
        'email',
        'storage_id',
        'domain_id',
        'wh_id',
        'password',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'permissions',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'permissions' => 'array',
        'email_verified_at' => 'datetime',
    ];

    /**
     * The attributes for which you can use filters in url.
     *
     * @var array
     */
    protected $allowedFilters = [
        'id' => Where::class,
        'name' => Like::class,
        'email' => Like::class,
        'updated_at' => WhereDateStartEnd::class,
        'created_at' => WhereDateStartEnd::class,
    ];

    /**
     * The attributes for which can use sort in url.
     *
     * @var array
     */
    protected $allowedSorts = [
        'id',
        'name',
        'email',
        'domain_id',
        'storage_id',
        'updated_at',
        'created_at',
    ];

    /**
     * Throw an exception if email already exists, create admin user.
     *
     * @throws \Throwable
     */
    public static function createAdmin(string $name, string $email, string $password): void
    {
        throw_if(static::where('email', $email)->exists(), 'User exists');

        static::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'permissions' => Dashboard::getAllowAllPermission(),
        ]);
    }

    /**
     * @return UserPresenter
     */
    public function presenter()
    {
        return new UserPresenter($this);
    }

    public function getDomain()
    {
        return $this->hasOne(rwDomain::class, 'dm_id', 'domain_id');
    }

    public function getWh()
    {
        return $this->hasOne(rwWarehouse::class, 'wh_id', 'wh_id');
    }

    public function role()
    {
        return $this->belongsToMany(Role::class, 'role_users', 'user_id', 'role_id');
    }

    public function hasRole($role)
    {
        $tmpRole = false;

        foreach ($this->role as $currentRole)
            if ($role === $currentRole->slug) $tmpRole = true;

        return $tmpRole;
    }

}

<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_STAFF = 'staff';

    /** @deprecated Usar ROLE_STAFF + can_operate. Solo para migraciones legadas. */
    public const ROLE_OPERATOR = 'operator';

    /** @deprecated Usar ROLE_STAFF + can_inventory. Solo para migraciones legadas. */
    public const ROLE_INVENTORY = 'inventory';

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'can_operate',
        'can_inventory',
        'active',
    ];

    /**
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'active' => 'boolean',
        'can_operate' => 'boolean',
        'can_inventory' => 'boolean',
        'password' => 'hashed',
    ];

    /**
     * @return array<int, string>
     */
    public static function accountTypes(): array
    {
        return [
            self::ROLE_ADMIN,
            self::ROLE_STAFF,
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function canOperate(): bool
    {
        return $this->isAdmin() || (bool) $this->can_operate;
    }

    public function canManageInventory(): bool
    {
        return $this->isAdmin() || (bool) $this->can_inventory;
    }

    public function canAccessPanel(): bool
    {
        return $this->isAdmin() || $this->can_operate || $this->can_inventory;
    }

    public function accessLabel(): string
    {
        if ($this->isAdmin()) {
            return 'Administrador';
        }

        $modules = [];

        if ($this->can_operate) {
            $modules[] = 'Operación';
        }

        if ($this->can_inventory) {
            $modules[] = 'Inventario';
        }

        return $modules !== [] ? implode(' + ', $modules) : 'Personal sin acceso';
    }

    /**
     * @param  array{name: string, email: string, password: string, role: string, can_operate?: bool, can_inventory?: bool}  $data
     */
    public function applyAccountSettings(array $data): void
    {
        $this->name = $data['name'];
        $this->email = $data['email'];
        $this->role = $data['role'];

        if ($this->isAdmin()) {
            $this->can_operate = false;
            $this->can_inventory = false;
        } else {
            $this->can_operate = (bool) ($data['can_operate'] ?? false);
            $this->can_inventory = (bool) ($data['can_inventory'] ?? false);
        }

        if (! empty($data['password'])) {
            $this->password = $data['password'];
        }
    }
}

<?php

namespace App\Models;

use CodeIgniter\Model;
use doc\UserRoleModel;
use ReflectionException;

class UserModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'oauth_users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $insertID = 0;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['password', 'username', 'first_name', 'last_name','email', 'email_verified','scope'];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['hashPassword'];
    protected $afterInsert = [];
    protected $beforeUpdate = ['hashPassword'];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    protected function hashPassword(array $data): array
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        return $data;
    }

    protected function userRole(): UserRoleModel
    {
        return new UserRoleModel();
    }

    public function getRoles(int $user_id): array
    {
        return $this->userRole()
            ->join('roles', 'roles.id = user_roles.role_id')
            ->where('user_roles.user_id', $user_id)
            ->findAll();
    }

    /**
     * @throws ReflectionException
     */
    public function assignRole(int $user_id, int $role_id): bool
    {
        return $this->userRole()
            ->insert(['user_id' => $user_id, 'role_id' => $role_id]);
    }

    public function hasRole(int $user_id, int $role_name): bool
    {
        $query = $this->userRole()
            ->join('roles', 'roles.id = user_roles.role_id')
            ->where('user_roles.user_id', $user_id)
            ->where('roles.name', $role_name)
            ->get();

        return $query->getRow() !== null;
    }
}

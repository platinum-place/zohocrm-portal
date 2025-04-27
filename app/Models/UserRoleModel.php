<?php

namespace App\Models;

use CodeIgniter\Model;
use ReflectionException;

class UserRoleModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'user_roles';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $insertID = 0;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['user_id', 'role_id', 'created_at', 'updated_at'];

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
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * @throws ReflectionException
     */
    public function assignRoleToUser(int $user_id, int $role_id): bool
    {
        return $this->insert(['user_id' => $user_id, 'role_id' => $role_id]);
    }

    public function getRolesByUser(int $user_id): array
    {
        return $this->where('user_id', $user_id)->findAll();
    }

    public function getUsersByRole(int $role_id): array
    {
        return $this->where('role_id', $role_id)->findAll();
    }

    public function removeRoleFromUser(int $user_id, int $role_id): bool
    {
        return $this->where('user_id', $user_id)
            ->where('role_id', $role_id)
            ->delete();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolePrivilegeModel extends Model
{
    use HasFactory;

    protected $primaryKey = 'trp_id';
    protected $table = 'role__privilege';
    protected $fillable = ['tr_id', 'tp_id'];
}

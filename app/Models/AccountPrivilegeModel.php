<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountPrivilegeModel extends Model
{
    use HasFactory;

    protected $primaryKey = 'tap_id';
    protected $table = 'account__privilege';
    protected $fillable = ['ta_id', 'tp_id'];
}

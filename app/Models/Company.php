<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_en',
        'name_ar',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot(['user_id', 'company_role_id', 'active'])->using(CompanyUser::class);
    }
}

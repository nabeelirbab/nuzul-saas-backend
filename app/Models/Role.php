<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    public const ADMIN = '1';
    public const COMPANY = '2';
    public const COMPANY_OWNER = '3';
    public const COMPANY_MANAGER = '4';
    public const COMPANY_AGENT = '5';
}

<?php
/**
 * Summary of namespace App\Models
 * @mixin 
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasUuids;
    protected $fillable=['name'];
    
}

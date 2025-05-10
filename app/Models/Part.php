<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\SoftDeletes;

class Part extends Model
{

    use SoftDeletes;
    protected $table = 'parts';

    protected $fillable = [
        'name',
        'category_id',
        'supplier_id',
        'user_id',
        'quantity',
        'price',
        'image_path',
        'date_acquired',
    ];

    // Relationship: Part belongs to Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relationship: Part belongs to Supplier
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    // Relationship: Part belongs to User
    public function user()
    {
        return $this->belongsTo(User::class, 'category_id');
    }
}

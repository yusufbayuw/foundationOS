<?php

namespace Modules\Procurement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Procurement\Database\Factories\GoodsReceiptItemFactory;

class GoodsReceiptItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): GoodsReceiptItemFactory
    // {
    //     // return GoodsReceiptItemFactory::new();
    // }
}

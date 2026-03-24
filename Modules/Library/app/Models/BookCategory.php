<?php

namespace Modules\Library\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Library\Database\Factories\BookCategoryFactory;

class BookCategory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): BookCategoryFactory
    // {
    //     // return BookCategoryFactory::new();
    // }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id');
    }

    // public function productPrices()
    // {
    //     return $this->hasMany(ProductPrice::class, 'product_id');
    // }

    public function productCategory()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function promotionProducts()
    {
        return $this->hasMany(PromotionProduct::class, 'promotion_id');
    }

    public function cart()
    {
        return $this->hasMany(Cart::class, 'product_id');
    }

    public function wishList()
    {
        return $this->hasMany(Wishlist::class, 'product_id');
    }

    public function productUnit()
    {
        return $this->belongsTo(ProductUnit::class, 'product_unit_id');
    }

    public function uomProductUnit()
    {
        return $this->belongsTo(ProductUnit::class, 'uom_product_unit_id');
    }
}

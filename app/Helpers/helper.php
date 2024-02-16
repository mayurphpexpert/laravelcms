<?php
use App\Models\Category;
use App\Models\ProductImage;

function getCategories(){
    return Category::orderBy('name','ASC')
                    ->whereNull('parent_id')
                    
                    ->where('showHome','Yes')
                    ->where('status',1)
                    ->get();
}

function getProductImage($productId){
    return ProductImage::where('product_id',$productId)->first();
}
//->orderBy('id','DESC')
?>
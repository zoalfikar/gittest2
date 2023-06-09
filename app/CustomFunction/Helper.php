<?php

use App\Models\Cart;
use App\Models\Category;
use App\Models\CategoryVisit;
use App\Models\Language;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Stevebauman\Location\Facades\Location;

use function PHPUnit\Framework\isNull;

// languages
function lang()
{
  return app()->getLocale();
}

function changLang($lang)
{
    app()->setLocale($lang);
}

/////////////////////////////

function CategoryPhoto($img)
{
    $file=$img;
    $ext=$file->getClientOriginalExtension();
    $filename=time().'.'.$ext;
    $file->move(public_path('assets/uploads/category'), $filename);
    return $filename;
}
function productPhoto($img)
{
    $file=$img;
    $ext=$file->getClientOriginalExtension();
    $filename=time().'.'.$ext;
    $file->move(public_path('assets/uploads/products'), $filename);
    return $filename;
}
//store logo
function storelogo($img)
{
    $file=$img;
    $ext=$file->getClientOriginalExtension();
    $filename=time().'.'.$ext;
    $file->move(public_path('assets/uploads/stores'), $filename);
    return $filename;
}

//////////////////////////////
function selectLan()
{
    return Language::Active()->get();
}

function langDir()
{
    $lang=Language::Active()->where('abbe',lang())->first();
    if(isNull( $lang)){
        return 'en' ;
    }
    return $lang->direction;

}
function isAdmin()
{  if(Auth::check())
    {
        if(Auth::user()->role_as == '1')
        {
            return true;
        }
        else
        {
            return false;
        }

    }
    return false;
}

function trendingProduct($id)
{
    //Dynamic method for calculating if a product is popular

    $PurchasedPod =DB::table('orders')->join('order_items','order_items.order_id','orders.id')->where('order_items.prod_id', $id)->whereNotNull('orders.payment_id')->select('orders.user_id')->distinct('orders.user_id')->count();
    $PurchasedPods =DB::table('order_items')->join('orders','orders.id','order_items.order_id')->select('order_items.prod_id')->distinct('order_items.prod_id')->select('orders.user_id')->whereNotNull('orders.payment_id')->count();
    if($PurchasedPods==0){return 0;}
    return  $PurchasedPod/$PurchasedPods >= 0.15;
}
function pupularCategory($id)
{
    //Dynamic method for calculating if a category is popular
    $visitedcat=CategoryVisit::where('cat_id',$id)->count();
    $visitedcats=CategoryVisit::all()->count();
    if($visitedcats==0){return 0;}
    return $visitedcat/$visitedcats >= 0.09;
}
function lat($ip)
{
    $data = Location::get($ip);
    if ($data) {
        return $data->latitude;
    }
    return "1";
}
function lng($ip)
{
    $data = Location::get($ip);
    if ( $data ) {
        return $data->longitude;
    }
    return "1";
}
function storeOwner()
{
    $store=Store::where('owner_id',Auth::id())->first();
    if ( $store) {
        return true;
    }
    return false;
}
function isActiveStore($slug)
{
    $store=Store::where('slug',$slug)->first();
    if ( $store) {
        if($store->active==1){return 1 ;}
        return false;
    }
    return 2;
}

/////   Store Products Filter
function StoreproductsFilter($product)
{
    $cat_id=$product['cat_id'];

    if (Category::where('id',$cat_id)->pluck('store_id')->first()== Store::where('owner_id',Auth::id())->pluck('id')->first())
    {
        return true;
    }
    else
    {
        return false;
    }
}

function storeProducts($all_Products)
{
    foreach($all_Products as $object)
    {
        $arrays[] = $object->toArray();
    }
    $f_Products=array_filter($arrays, "StoreproductsFilter");
    $Products=(object)$f_Products;
    foreach ($f_Products as $key => $value)
    {
        $Product = new Product();
        $Product->fill($value);
        $Products->{$key} = $Product;
    }
    return $Products;
}

////////////// end of  Store Products Filter

function userHasStore()
{
    if (Auth::check())
    {
        if (Store::where('owner_id',Auth::id())->exists())
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    return 1;

}
//////////////////////////////////////////

function mainCategory($Cat_id)
{
    $Category=Category::where('id',$Cat_id)->first();
    if ($Category->sub_cat_of==null)
    {
        return $Category;
    }
    else
    {
        return mainCategory($Category->sub_cat_of);
    }

}

function orginalCategory($Cat_id)
{
    $Category=Category::where('id',$Cat_id)->first();
    if ($Category->translation_of==null)
    {
        return $Category;
    }
    else
    {
        return mainCategory($Category->translation_of);
    }
}
//////  transVersionOfCategory (en,fr,ar,....)()

function preTransVersion($abbe , $Cat_id )
{
    $Category=Category::where('id',$Cat_id)->first();
    if ($Category)
    {
        if ($Category->languages_abbe==$abbe)
        {
            return $Category;
        }
        else
        {
            return pretransVersion($abbe,$Category->translation_of);
        }
    }
    return false;

}

function postTransVersion($abbe , $Cat_id )
{
    $Category=Category::where('translation_of',$Cat_id)->first();
    if ($Category)
    {
        if ($Category->languages_abbe==$abbe)
        {
            return $Category;
        }
        else
        {
            return postTransVersion($abbe,$Category->id);
        }
    }

    return false;
}

function transVersion($abbe , $Cat_id )
{
    $Category=Category::where('id',$Cat_id)->first();
    if ($Category)
    {
        $cat =  preTransVersion($abbe , $Cat_id );
        if ($cat)
        {
                return $cat;
        }
        else
        {
            $cat=postTransVersion($abbe , $Cat_id );
            if ($cat)
            {
                return $cat;
            }
        }
    }
    return false ;
}

//////  end of transVersion()



// user has products from this stores in cart
function productsFromStores()
{
    $userCartProduct=Cart::select('prod_id')->where('user_id',Auth::id())->get()->toArray();
    return Category::select('store_id')->with('Products')->where('status',0)->whereHas('Products',function ($q) use ($userCartProduct)
    {
        $q->whereIn ('id' , $userCartProduct);
    })->distinct('store_id')->orderBy('store_id')->get()->toArray();
}

function productsFromStore($id)
{
    if ($id == 0) { $id = null ; }
    return $storeCartItems= Cart::where('user_id',Auth::id())->with('Product')->whereHas('Product',function ($q) use ($id)
    {
        $q->with('Category')->whereHas('Category',function ($q) use ($id)
        {
            $q->where('store_id',$id);
        });
    })->get();
}










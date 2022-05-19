<?php

namespace App\Http\Controllers\admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    //proucts

    public function index() {

        $Products=Product::all();
        return view('admin.products.index',compact('Products'));

    }


     public function addProduct() {

        $categories=Category::all();
        return view('admin.products.addProduct',compact('categories'));

    }


    public function editProduct($id) {

        $product=Product::find($id);
        return view('admin.products.editProduct',compact('product'));

    }


    public function insertProduct ( Request $request) {

        $product = new Product();
        if( $request->hasFile('image'))
            {
                $file=$request->file('image');
                $ext=$file->getClientOriginalExtension();
                $filename=time().'.'.$ext;
                $file->move(public_path('assets/uploads/product'), $filename);
                $product->img= $filename;
            }
         $product->cat_id= $request->input('cat_id');
         $product->name= $request->input('name');
         $product->slug= $request->input('slug');
         $product->small_description= $request->input('small_description');
         $product->description= $request->input('description');
         $product->orginal_price= $request->input('orginal_price');
         $product->selling_price= $request->input('selling_price');
         $product->status= $request->input('status')==true?'1':'0';
         $product->trending= $request->input('trending')==true?'1':'0';
         $product->qty= $request->input('qty');
         $product->tax= $request->input('tax');
         $product->meta_title= $request->input('meta_title');
         $product->meta_descrip= $request->input('meta_descrip');
         $product->meta_kewwords= $request->input('meta_kewwords');
         $product->save();
         return redirect('/products')->with('status','product inserted successfully');

    }


        public function updateProduct ( Request $request , $id) {

            $product =Product::find($id);
            if( $request->hasFile('image'))
                {
                    $path= 'assets/uploads/product/'.$product->img;
                    if (File::exists($path))
                        {
                            File::delete($path);
                        }
                    $file=$request->file('image');
                    $ext=$file->getClientOriginalExtension();
                    $filename=time().'.'.$ext;
                    $file->move(public_path('assets/uploads/product'), $filename);
                    $product->img= $filename;
                }
            $product->name= $request->input('name');
            $product->slug= $request->input('slug');
            $product->description= $request->input('description');
            $product->small_description= $request->input('small_description');
            $product->orginal_price= $request->input('orginal_price');
            $product->selling_price= $request->input('selling_price');
            $product->status= $request->input('status')==true?'1':'0';
            $product->trending= $request->input('trending')==true?'1':'0';
            $product->meta_title= $request->input('meta_title');
            $product->meta_kewwords= $request->input('meta_kewwords');
            $product->meta_descrip= $request->input('meta_descrip');
            $product->update();
            return redirect('/products')->with('status','product updated successfully');

        }



        public function deleteProduct($id) {
            $product=Product::find($id);
            if ($product->img)
                {
                    $path='assets/uploads/product/'.$product->img;
                    if (File::exists($path))
                        {
                            File::delete($path);
                        }
                }
            $product->delete();
            return redirect('products')->with('status','product has deleted');

        }

}
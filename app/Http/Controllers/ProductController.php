<?php

namespace App\Http\Controllers;

//import model product
use App\Models\Product; 

//import return type View
use Illuminate\View\View;

//import return type redirectResponse
use Illuminate\Http\Request;

//import Http Request
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
//import Facades Storage
use Illuminate\Support\Facades\Storage;


class ProductController extends Controller
{
    public function index() : View
    {
        // get all products
        $products = Product::latest()->paginate(10);

        // return view with products
        return view('products.index', compact('products'));
    }

    public function create(): View
    {
        return view('products.create');
    }

    public function store(Request $request): RedirectResponse
    {
        // validate form
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'title' => 'required|min:5',
            'description' => 'required|min:10',
            'price' => 'required|numeric',
            'stock' => 'required|numeric'
        ]);

        // upload image
        $image = $request->file('image');
        $image->storeAs('public/products', $image->hashName());

        // create product
        Product::create([
            'image' => $image->hashName(),
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock
        ]);

        // redirect to index
        return redirect()->route('products.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }

    // show
    public function show(string $id): View
    {
        // get product by ID
        $product = Product::findOrFail($id);

        // render view with product
        return view('products.show', compact('product'));
    }


    // edit
    public function edit(string $id): View
    {
        // get product by ID
        $product = Product::findOrFail($id);

        // render view with product
        return view('products.edit', compact('product'));
    }

    // update
    public function update(Request $request, $id): RedirectResponse
    {
        // validate form
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'title' => 'required|min:5',
            'description' => 'required|min:10',
            'price' => 'required|numeric',
            'stock' => 'required|numeric'
        ]);

        // get product by ID
        $product = Product::findOrFail($id);

        // check if image id upload
        if ($request->hasFile('image')) {
            
            // upload new image
            $image = $request->file('image');
            $image->storeAs('public/products/', $image->hasName());

            // delete old image
            Storage::delete('public/products'.$product->image);

            // update product with new image
            $product->update([
                'image' => $image->hashName(),
                'title' => $request->title,
                'description' => $request->description,
                'price' => $request->price,
                'stock' => $request->stock
            ]);
        } else {
            
            // update product without image
            $product->update([
                'title' => $request->title,
                'description' => $request->description,
                'price' => $request->price,
                'stock' => $request->stock
            ]);
        }

        // redirect to index
        return redirect()->route('products.index')->with(['success' => 'Data Berhasil Diubah!']);
        
    }

    public function destroy($id): RedirectResponse
    {
        // get product by ID
        $product = Product::findOrFail($id);

        // delete image
        Storage::delete('public/products/'. $product->image);

        // delete product
        $product->delete();

        // redirect to index
        return redirect()->route('products.index')->with(['success' => 'Data Berhasil Dihapus!']);
    }
}

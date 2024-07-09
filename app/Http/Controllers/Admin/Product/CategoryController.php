<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Models\Product\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;

        $categories = Category::where('name', 'LIKE', '%' . $search . '%')->orderBy("id", "desc")->paginate(25);

        return response()->json([
            "total" => $categories->total(),
            "categories" => $categories,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $is_exists = Category::where("name", $request->name)->first();
        if($is_exists) {
            return response()->json(["message" => 403]);
        }
        if ($request->hasFile('image')()) {
            $path = Storage::putFile("categories", $request->file('image'));
            $request->request->add(["imagen" => $path]);
        }
        $category = Category::create($request->all());

        return response()->json(["message" => 200]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::findOrFail($id);
        return response()->json(["category" => $category]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $is_exists = Category::where("id",'<>', $id)->where("name", $request->name)->first();
        if($is_exists) {
            return response()->json(["message" => 403]);
        }
        $category = Category::findOrFail($id);
        if($request->hasFile('image')()) {
            if ($category->imagen) {
                Storage::delete($category->imagen);
            }
            $path = Storage::putFile("categories", $request->file('image'));
            $request->request->add(["imagen" => $path]);
        }
        $category->update($request->all());
        return response()->json(["message" => 200]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json(["message" => 200]);
    }
}

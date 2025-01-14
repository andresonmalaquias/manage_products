<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::query();

        // Filtro de busca por nome ou descrição
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $products = $query->paginate(5);

        return response()->json($products);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'description' => 'nullable|string|max:200',
            'price' => 'required|numeric|min:0',
            'expiration_date' => 'required|date|after_or_equal:today',
            'image' => 'nullable|file|image|max:2048', // 2 MB de limite
            'category_id' => 'required|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();

        // Lógica para upload de imagem
        if ($request->hasFile('image')) {
            $file = $request->file('image');

            $uniqueName = uniqid().'_'.$file->getClientOriginalName();

            // Armazenar a imagem com o novo nome
            //$file->storeAs('products', $uniqueName, 'public');

            $data['image'] = $uniqueName;
        }

        $product = Product::create($data);

        return response()->json(['message' => 'Created successfully', 'data' => $product], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $product = Product::find($id);

        if (!$product)
            return response()->json(['message' => 'Not found'], 404);

        return response()->json($product);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:200',
            'price' => 'nullable|numeric|min:0',
            'expiration_date' => 'nullable|date|after_or_equal:today',
            'image' => 'nullable|file|image|max:2048',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->only(['name', 'description', 'price', 'expiration_date', 'category_id']);

        if ($request->hasFile('image')) {
            $file = $request->file('image');

            $uniqueName = uniqid() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('products', $uniqueName, 'public');

            $data['image'] = $filePath;

        }

        $product->update($data);

        return response()->json([
            'message' => 'Produto atualizado com sucesso!',
            'data' => $product,
        ], 202);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        $product->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}

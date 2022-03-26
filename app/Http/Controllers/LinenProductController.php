<?php

namespace App\Http\Controllers;

use App\Models\LinenProduct;
use Illuminate\Http\Request;

/**
 * Class LinenProductController
 * @package App\Http\Controllers
 */
class LinenProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $linenProducts = LinenProduct::paginate();

        return view('linen-product.index', compact('linenProducts'))
            ->with('i', (request()->input('page', 1) - 1) * $linenProducts->perPage());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $linenProduct = new LinenProduct();
        return view('linen-product.create', compact('linenProduct'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(LinenProduct::$rules);

        $linenProduct = LinenProduct::create($request->all());

        return redirect()->route('linen-products.index')
            ->with('success', 'LinenProduct created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $linenProduct = LinenProduct::find($id);

        return view('linen-product.show', compact('linenProduct'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $linenProduct = LinenProduct::find($id);

        return view('linen-product.edit', compact('linenProduct'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  LinenProduct $linenProduct
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LinenProduct $linenProduct)
    {
        request()->validate(LinenProduct::$rules);

        $linenProduct->update($request->all());

        return redirect()->route('linen-products.index')
            ->with('success', 'LinenProduct updated successfully');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $linenProduct = LinenProduct::find($id)->delete();

        return redirect()->route('linen-products.index')
            ->with('success', 'LinenProduct deleted successfully');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\LinenType;
use Illuminate\Http\Request;

/**
 * Class LinenTypeController
 * @package App\Http\Controllers
 */
class LinenTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $linenTypes = LinenType::paginate();

        return view('linen-type.index', compact('linenTypes'))
            ->with('i', (request()->input('page', 1) - 1) * $linenTypes->perPage());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $linenType = new LinenType();
        return view('linen-type.create', compact('linenType'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(LinenType::$rules);

        $linenType = LinenType::create($request->all());

        return redirect()->route('linen-types.index')
            ->with('success', 'LinenType created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $linenType = LinenType::find($id);

        return view('linen-type.show', compact('linenType'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $linenType = LinenType::find($id);

        return view('linen-type.edit', compact('linenType'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  LinenType $linenType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LinenType $linenType)
    {
        request()->validate(LinenType::$rules);

        $linenType->update($request->all());

        return redirect()->route('linen-types.index')
            ->with('success', 'LinenType updated successfully');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $linenType = LinenType::find($id)->delete();

        return redirect()->route('linen-types.index')
            ->with('success', 'LinenType deleted successfully');
    }
}

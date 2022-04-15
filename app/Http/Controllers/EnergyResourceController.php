<?php

namespace App\Http\Controllers;

use App\Models\EnergyResource;
use Illuminate\Http\Request;

/**
 * Class EnergyResourceController
 * @package App\Http\Controllers
 */
class EnergyResourceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $energyResources = EnergyResource::paginate();

        return view('energy-resource.index', compact('energyResources'))
            ->with('i', (request()->input('page', 1) - 1) * $energyResources->perPage());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $energyResource = new EnergyResource();
        return view('energy-resource.create', compact('energyResource'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(EnergyResource::$rules);

        $energyResource = EnergyResource::create($request->all());

        return redirect()->route('energy-resources.index')
            ->with('success', 'EnergyResource created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $energyResource = EnergyResource::find($id);

        return view('energy-resource.show', compact('energyResource'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $energyResource = EnergyResource::find($id);

        return view('energy-resource.edit', compact('energyResource'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  EnergyResource $energyResource
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EnergyResource $energyResource)
    {
        request()->validate(EnergyResource::$rules);

        $energyResource->update($request->all());

        return redirect()->route('energy-resources.index')
            ->with('success', 'EnergyResource updated successfully');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $energyResource = EnergyResource::find($id)->delete();

        return redirect()->route('energy-resources.index')
            ->with('success', 'EnergyResource deleted successfully');
    }
}

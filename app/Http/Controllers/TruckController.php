<?php

namespace App\Http\Controllers;

use App\Models\Truck;
use Illuminate\Http\Request;

/**
 * Class TruckController
 * @package App\Http\Controllers
 */
class TruckController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $trucks = Truck::paginate();

        return view('truck.index', compact('trucks'))
            ->with('i', (request()->input('page', 1) - 1) * $trucks->perPage());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $truck = new Truck();
        return view('truck.create', compact('truck'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(Truck::$rules);

        $truck = Truck::create($request->all());

        return redirect()->route('trucks.index')
            ->with('success', 'Truck created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $truck = Truck::find($id);

        return view('truck.show', compact('truck'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $truck = Truck::find($id);

        return view('truck.edit', compact('truck'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  Truck $truck
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Truck $truck)
    {
        request()->validate(Truck::$rules);

        $truck->update($request->all());

        return redirect()->route('trucks.index')
            ->with('success', 'Truck updated successfully');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $truck = Truck::find($id)->delete();

        return redirect()->route('trucks.index')
            ->with('success', 'Truck deleted successfully');
    }
}

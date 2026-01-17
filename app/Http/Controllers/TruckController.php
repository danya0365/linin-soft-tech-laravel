<?php

namespace App\Http\Controllers;

use App\Enums\ExpenseType;
use App\Managers\ExpenseManager;
use App\Models\Note;
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

        $query = Note::where('truck_id', $id);
        
        // Filter by tag if provided (search in JSON array)
        $selectedTag = request('tag');
        if ($selectedTag) {
            $query->whereJsonContains('tags', $selectedTag);
        }
        
        $notes = $query->orderBy('created_at', 'desc')->paginate();
        
        // Get tags specific to this truck (cached)
        $machineTags = Note::getTagsForMachine('truck', $id);

        return view('truck.show', compact('truck', 'notes', 'machineTags', 'selectedTag'));
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


    public function createNote($id)
    {
        $truck = Truck::find($id);
        $note = new Note();

        if (request()->isMethod('post')) {

            request()->validate(
                [
                    'message' => 'required',
                    'cost' => 'required',
                    'truck_id' => 'required',
                    'image_upload' => 'mimes:jpeg,jpg,png,gif|max:10000'
                ]
            );

            $post = request()->all();
            $request = request();
            if (request()->hasFile('image_upload')) {
                if (request()->file('image_upload')->isValid()) {
                    $request->photo = request()->file('image_upload');
                    $path = $request->photo->path();
                    $fileName = $request->photo->getClientOriginalName();
                    $fileName = str_replace(' ', '_', $fileName);
                    $extension = $request->photo->extension();
                    $date = \Carbon\Carbon::now()->format('Y-m-d');
                    $storeDir = "$date/$fileName";
                    $storePath = $request->photo->storeAs('images', $storeDir);
                    $post['image_url'] = $storePath;
                }
            }

            $note = new Note();
            $note->message = $post['message'];
            $note->image_url = $post['image_url'] ?? '';
            $note->cost = $post['cost'];
            
            // Process tags - convert comma-separated string to array
            $tagsInput = $post['tags'] ?? '';
            if (!empty($tagsInput)) {
                $tagsArray = array_map('trim', explode(',', $tagsInput));
                $tagsArray = array_filter($tagsArray);
                $note->tags = array_values(array_unique($tagsArray));
            }
            
            $note->truck_id = $post['truck_id'];
            if ($post['note_date']) {
                $note->timestamps = false;
                $note->created_at = \Carbon\Carbon::parse($post['note_date']);
                $note->updated_at = \Carbon\Carbon::now();
            }
            $note->save();

            if ($note) {
                ExpenseManager::create(ExpenseType::Truck(), $note, $note->cost, $note->created_at);
            }

            return redirect()->route('trucks.show', $truck)
                ->with('success', 'Note created successfully');
        }
        $existingTags = Note::getExistingTags();

        return view('truck.create-note', compact('truck', 'note', 'existingTags'));
    }
}

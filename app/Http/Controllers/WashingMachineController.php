<?php

namespace App\Http\Controllers;

use App\Enums\ExpenseType;
use App\Managers\ExpenseManager;
use App\Models\Note;
use App\Models\WashingMachine;
use Illuminate\Http\Request;

/**
 * Class WashingMachineController
 * @package App\Http\Controllers
 */
class WashingMachineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $washingMachines = WashingMachine::paginate();

        return view('washing-machine.index', compact('washingMachines'))
            ->with('i', (request()->input('page', 1) - 1) * $washingMachines->perPage());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $washingMachine = new WashingMachine();
        return view('washing-machine.create', compact('washingMachine'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(WashingMachine::$rules);

        $data = $request->all();

        if ($request->hasFile('photo')) {
            if ($request->file('photo')->isValid()) {
                $file = $request->file('photo');
                $extension = $file->extension();
                $fileName = 'washing_' . time() . '.' . $extension;
                $date = \Carbon\Carbon::now()->format('Y-m-d');
                $storeDir = "$date";
                $path = $file->storeAs('images/' . $storeDir, $fileName, 'public');
                $data['photo'] = 'storage/' . $path;
            }
        }

        $washingMachine = WashingMachine::create($data);

        return redirect()->route('washing-machines.index')
            ->with('success', 'WashingMachine created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $washingMachine = WashingMachine::find($id);

        $query = Note::where('washing_machine_id', $id);
        
        // Filter by tag if provided (search in JSON array)
        $selectedTag = request('tag');
        if ($selectedTag) {
            $query->whereJsonContains('tags', $selectedTag);
        }
        
        $notes = $query->orderBy('created_at', 'desc')->paginate();
        
        // Get tags specific to this washing machine (cached)
        $machineTags = Note::getTagsForMachine('washing_machine', $id);

        return view('washing-machine.show', compact('washingMachine', 'notes', 'machineTags', 'selectedTag'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $washingMachine = WashingMachine::find($id);

        return view('washing-machine.edit', compact('washingMachine'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  WashingMachine $washingMachine
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, WashingMachine $washingMachine)
    {
        request()->validate(WashingMachine::$rules);

        $data = $request->all();

        if ($request->hasFile('photo')) {
            if ($request->file('photo')->isValid()) {
                $file = $request->file('photo');
                $extension = $file->extension();
                $fileName = 'washing_' . time() . '.' . $extension;
                $date = \Carbon\Carbon::now()->format('Y-m-d');
                $storeDir = "$date";
                $path = $file->storeAs('images/' . $storeDir, $fileName, 'public');
                $data['photo'] = 'storage/' . $path;
            }
        }

        $washingMachine->update($data);

        return redirect()->route('washing-machines.index')
            ->with('success', 'WashingMachine updated successfully');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $washingMachine = WashingMachine::find($id)->delete();

        return redirect()->route('washing-machines.index')
            ->with('success', 'WashingMachine deleted successfully');
    }


    public function createNote($id)
    {
        $washingMachine = WashingMachine::find($id);
        $note = new Note();

        if (request()->isMethod('post')) {

            request()->validate(
                [
                    'message' => 'required',
                    'cost' => 'required',
                    'washing_machine_id' => 'required',
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
                $tagsArray = array_filter($tagsArray); // Remove empty values
                $note->tags = array_values(array_unique($tagsArray));
            }
            
            $note->washing_machine_id = $post['washing_machine_id'];
            if ($post['note_date']) {
                $note->timestamps = false;
                $note->created_at = \Carbon\Carbon::parse($post['note_date']);
                $note->updated_at = \Carbon\Carbon::now();
            }
            $note->save();

            if ($note) {
                ExpenseManager::create(ExpenseType::WashingMachine(), $note, $note->cost, $note->created_at);
            }

            return redirect()->route('washing-machines.show', $washingMachine)
                ->with('success', 'Note created successfully');
        }
        $existingTags = Note::getExistingTags();

        return view('washing-machine.create-note', compact('washingMachine', 'note', 'existingTags'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Enums\ExpenseType;
use App\Managers\ExpenseManager;
use App\Models\DryerMachine;
use App\Models\Note;
use Illuminate\Http\Request;

/**
 * Class DryerMachineController
 * @package App\Http\Controllers
 */
class DryerMachineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dryerMachines = DryerMachine::paginate();

        return view('dryer-machine.index', compact('dryerMachines'))
            ->with('i', (request()->input('page', 1) - 1) * $dryerMachines->perPage());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $dryerMachine = new DryerMachine();
        return view('dryer-machine.create', compact('dryerMachine'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(DryerMachine::$rules);

        $dryerMachine = DryerMachine::create($request->all());

        return redirect()->route('dryer-machines.index')
            ->with('success', 'DryerMachine created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $dryerMachine = DryerMachine::find($id);

        $query = Note::where('dryer_machine_id', $id);
        
        // Filter by tag if provided (search in JSON array)
        $selectedTag = request('tag');
        if ($selectedTag) {
            $query->whereJsonContains('tags', $selectedTag);
        }
        
        $notes = $query->orderBy('created_at', 'desc')->paginate();
        
        // Get tags specific to this dryer machine (cached)
        $machineTags = Note::getTagsForMachine('dryer_machine', $id);

        return view('dryer-machine.show', compact('dryerMachine', 'notes', 'machineTags', 'selectedTag'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $dryerMachine = DryerMachine::find($id);

        return view('dryer-machine.edit', compact('dryerMachine'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  DryerMachine $dryerMachine
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DryerMachine $dryerMachine)
    {
        request()->validate(DryerMachine::$rules);

        $dryerMachine->update($request->all());

        return redirect()->route('dryer-machines.index')
            ->with('success', 'DryerMachine updated successfully');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $dryerMachine = DryerMachine::find($id)->delete();

        return redirect()->route('dryer-machines.index')
            ->with('success', 'DryerMachine deleted successfully');
    }

    public function createNote($id)
    {
        $dryerMachine = DryerMachine::find($id);
        $note = new Note();

        if (request()->isMethod('post')) {

            request()->validate(
                [
                    'message' => 'required',
                    'cost' => 'required',
                    'dryer_machine_id' => 'required',
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
            
            $note->dryer_machine_id = $post['dryer_machine_id'];
            if ($post['note_date']) {
                $note->timestamps = false;
                $note->created_at = \Carbon\Carbon::parse($post['note_date']);
                $note->updated_at = \Carbon\Carbon::now();
            }
            $note->save();

            if ($note) {
                ExpenseManager::create(ExpenseType::DryerMachine(), $note, $note->cost, $note->created_at);
            }

            return redirect()->route('dryer-machines.show', $dryerMachine)
                ->with('success', 'Note created successfully');
        }
        $existingTags = Note::getExistingTags();

        return view('dryer-machine.create-note', compact('dryerMachine', 'note', 'existingTags'));
    }
}


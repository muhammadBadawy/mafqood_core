<?php

namespace App\Http\Controllers\Suspect;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Suspect;
use Illuminate\Http\Request;

class SuspectsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 25;

        if (!empty($keyword)) {
            $suspects = Suspect::where('name', 'LIKE', "%$keyword%")
                ->orWhere('appearance_times', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $suspects = Suspect::latest()->paginate($perPage);
        }

        return view('suspect.suspects.index', compact('suspects'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('suspect.suspects.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $this->validate($request, [
			'name' => 'max:255'
		]);
        $requestData = $request->all();
        
        Suspect::create($requestData);

        return redirect('suspect/suspects')->with('flash_message', 'Suspect added!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $suspect = Suspect::findOrFail($id);

        return view('suspect.suspects.show', compact('suspect'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $suspect = Suspect::findOrFail($id);

        return view('suspect.suspects.edit', compact('suspect'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
			'name' => 'max:255'
		]);
        $requestData = $request->all();
        
        $suspect = Suspect::findOrFail($id);
        $suspect->update($requestData);

        return redirect('suspect/suspects')->with('flash_message', 'Suspect updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        Suspect::destroy($id);

        return redirect('suspect/suspects')->with('flash_message', 'Suspect deleted!');
    }
}

<?php

namespace App\Http\Controllers\Match;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Match;
use App\Report;
use Illuminate\Http\Request;

class MatchesController extends Controller
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
            $matches = Match::where('missing_id', 'LIKE', "%$keyword%")
                ->orWhere('found_id', 'LIKE', "%$keyword%")
                ->orWhere('serial', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $matches = Match::latest()->paginate($perPage);
        }

        return view('match.matches.index', compact('matches'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $missing_reports = Report::where('type', 'missing')->get();
        $found_reports = Report::where('type', 'found')->get();
        return view('match.matches.create', compact('missing_reports', 'found_reports'));
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
			'missing_id' => 'numeric',
			'found_id' => 'numeric',
			'serial' => 'max:255|numeric'
		]);
        $requestData = $request->all();

        Match::create($requestData);

        return redirect('match/matches')->with('flash_message', 'Match added!');
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
        $match = Match::findOrFail($id);

        return view('match.matches.show', compact('match'));
    }


    public function getMatchingReports($id)
    {

        $report = Report::find($id);

        if(!$report){
          return response()->json([
              'data' => [],
              'status' =>  200
              ], 200);
        }

        if ($report->type == 'missing') {
          $selected_ids = Match::where('missing_id', $report->id)->pluck('found_id');
        }
        else{
          $selected_ids = Match::where('found_id', $report->id)->pluck('missing_id');
        }

        $required_reports = Report::whereIn('id', $selected_ids)->get();

        foreach ($required_reports as $value) {
          $value->images = $value->images();
        }

        return response()->json([
            'data' => $required_reports,
            'status' =>  200
            ], 200);
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
        $match = Match::findOrFail($id);
        $missing_reports = Report::where('type', 'missing')->get();
        $found_reports = Report::where('type', 'found')->get();

        return view('match.matches.edit', compact('match', 'missing_reports', 'found_reports'));
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
			'missing_id' => 'numeric',
			'found_id' => 'numeric',
			'serial' => 'max:255|numeric'
		]);
        $requestData = $request->all();

        $match = Match::findOrFail($id);
        $match->update($requestData);

        return redirect('match/matches')->with('flash_message', 'Match updated!');
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
        Match::destroy($id);

        return redirect('match/matches')->with('flash_message', 'Match deleted!');
    }
}

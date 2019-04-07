<?php

namespace App\Http\Controllers\Report;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Report;
use Illuminate\Http\Request;

class ReportsController extends Controller
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
            $reports = Report::where('name', 'LIKE', "%$keyword%")
                ->orWhere('phone', 'LIKE', "%$keyword%")
                ->orWhere('email', 'LIKE', "%$keyword%")
                ->orWhere('gender', 'LIKE', "%$keyword%")
                ->orWhere('birth', 'LIKE', "%$keyword%")
                ->orWhere('area_id', 'LIKE', "%$keyword%")
                ->orWhere('lat', 'LIKE', "%$keyword%")
                ->orWhere('lang', 'LIKE', "%$keyword%")
                ->orWhere('mental_condition', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $reports = Report::latest()->paginate($perPage);
        }

        return view('report.reports.index', compact('reports'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('report.reports.create');
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
			'name' => 'required|max:255',
			'email' => 'required|max:255|email',
			'phone' => 'required|max:255',
			'birth' => 'required',
			'gender' => 'required',
			'area_id' => 'required|numeric',
			'lat' => 'numeric',
			'lang' => 'numeric'
		]);
        $requestData = $request->all();

        Report::create($requestData);

        return redirect('report/reports')->with('flash_message', 'Report added!');
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
        $report = Report::findOrFail($id);

        return view('report.reports.show', compact('report'));
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
        $report = Report::findOrFail($id);

        return view('report.reports.edit', compact('report'));
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
			'name' => 'required|max:255',
			'email' => 'required|max:255|email',
			'phone' => 'required|max:255',
			'birth' => 'required',
			'gender' => 'required',
			'area_id' => 'required|numeric',
			'lat' => 'numeric',
			'lang' => 'numeric'
		]);
        $requestData = $request->all();

        $report = Report::findOrFail($id);
        $report->update($requestData);

        return redirect('report/reports')->with('flash_message', 'Report updated!');
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
        Report::destroy($id);

        return redirect('report/reports')->with('flash_message', 'Report deleted!');
    }
}

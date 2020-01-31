<?php

namespace App\Http\Controllers\Area;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Area;
use App\City;
use Illuminate\Http\Request;

class AreasController extends Controller
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
            $areas = Area::where('name', 'LIKE', "%$keyword%")
                ->orWhere('city_id', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $areas = Area::latest()->paginate($perPage);
        }

        return view('area.areas.index', compact('areas'));
    }

    public function areasRating(Request $request)
    {
        $keyword = $request->get('search');

        if (!empty($keyword)) {
            $areas = Area::where('name', 'LIKE', "%$keyword%")
                ->orWhere('city_id', 'LIKE', "%$keyword%")
                ->where('city_id', $request->id)
                ->with('reports');
                // ->paginate($perPage);
        } else {
            $areas = Area::latest()
                ->with('reports')
                ->where('city_id', $request->id);
                // ->paginate($perPage);
        }
        $areas = $areas->get();

        $areas_count = [];
        foreach ($areas as $key => $value) {
          $areas_count[] = ['name' => $value->name,'id' => $value->id,'count' => $value->reports->count()];
        }

        // function ($a, $b)
        // {
        //   return strnatcmp($a['count'], $b['count']);
        // }

        // sort alphabetically by name
        usort($areas_count, function ($a, $b)
        {
          return strnatcmp($a['count'], $b['count']);
        });

        $areas = array_reverse($areas_count);
        // return var_dump($areas_count);
        return view('area.areas.rating', compact('areas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $cities = City::all();
        return view('area.areas.create', compact('cities'));
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
			'name' => 'max:255',
			'city_id' => 'numeric'
		]);
        $requestData = $request->all();

        Area::create($requestData);

        return redirect('area/areas')->with('flash_message', 'Area added!');
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
        $area = Area::findOrFail($id);

        return view('area.areas.show', compact('area'));
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
        $area = Area::findOrFail($id);
        $cities = City::all();

        return view('area.areas.edit', compact('area', 'cities'));
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
			'name' => 'max:255',
			'city_id' => 'numeric'
		]);
        $requestData = $request->all();

        $area = Area::findOrFail($id);
        $area->update($requestData);

        return redirect('area/areas')->with('flash_message', 'Area updated!');
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
        Area::destroy($id);

        return redirect('area/areas')->with('flash_message', 'Area deleted!');
    }
}

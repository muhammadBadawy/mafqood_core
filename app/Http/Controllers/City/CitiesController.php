<?php

namespace App\Http\Controllers\City;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\City;
use Illuminate\Http\Request;

class CitiesController extends Controller
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
            $cities = City::where('name', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $cities = City::latest()->paginate($perPage);
        }

        return view('city.cities.index', compact('cities'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('city.cities.create');
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

        City::create($requestData);

        return redirect('city/cities')->with('flash_message', 'City added!');
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
        $city = City::findOrFail($id);

        return view('city.cities.show', compact('city'));
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
        $city = City::findOrFail($id);

        return view('city.cities.edit', compact('city'));
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

        $city = City::findOrFail($id);
        $city->update($requestData);

        return redirect('city/cities')->with('flash_message', 'City updated!');
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
        City::destroy($id);

        return redirect('city/cities')->with('flash_message', 'City deleted!');
    }

    public function citiesRating(Request $request)
    {
        $keyword = $request->get('search');

        if (!empty($keyword)) {
            $cities = City::where('name', 'LIKE', "%$keyword%")
                ->with('reports');
                // ->paginate($perPage);
        } else {
            $cities = City::latest()
                ->with('reports');
                // ->paginate($perPage);
        }
        $cities = $cities->get();

        $cities_count = [];
        foreach ($cities as $key => $value) {
          $cities_count[] = ['name' => $value->name,'id' => $value->id,'count' => $value->reports->count()];
        }

        // function ($a, $b)
        // {
        //   return strnatcmp($a['count'], $b['count']);
        // }

        // sort alphabetically by name
        usort($cities_count, function ($a, $b)
        {
          return strnatcmp($a['count'], $b['count']);
        });

        $cities = array_reverse($cities_count);
        // return var_dump($cities_count);
        return view('city.cities.rating', compact('cities'));
    }
}

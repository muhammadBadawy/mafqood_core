<?php

namespace App\Http\Controllers\Stamp;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Stamp;
use Illuminate\Http\Request;

class StampsController extends Controller
{

    public function __construct()
    {
      $this->middleware('auth', ['except' => ['store', 'index']]);
      $this->middleware('auth.basic', ['except' => ['store', 'index']]);
      // $this->middleware('signed', ['except' => ['store']]);

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 10;

        if (!empty($keyword)) {
            $stamps = Stamp::where('print', 'LIKE', "%$keyword%")
                ->orWhere('image', 'LIKE', "%$keyword%")
                ->orWhere('bbox', 'LIKE', "%$keyword%")
                ->orWhere('report_id', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $stamps = Stamp::latest()->paginate($perPage);
        }

        return view('stamp.stamps.index', compact('stamps'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('stamp.stamps.create');
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
    			// 'print' => 'required',
    			// 'image' => 'required|max:255',
    			// 'bbox' => 'required',
    			'report_id' => 'required|numeric',
          // 'images' => 'required',
          'images.*' => 'image|mimes:jpeg,png,jpg|max:2048'
    		]);
        ini_set('max_execution_time', 300);
        $requestData = $request->all();

        $data = [];
        // return '1';
        if($request->hasfile('images'))
        {

            foreach($request->file('images') as $image)
            {
                $name=$image->getClientOriginalExtension();
                $name = randomString(10).".".$name;
                $image->move(storage_path().'/app/public/people/', $name);
                // return storage_path().'/people/'.$name;
                $prints = extractFace(storage_path().'/app/public/people/'.$name);
                // return $prints;
                foreach ($prints as $instance) {
                  Stamp::create([
                    "report_id" => $requestData['report_id'],
                    "print" => serialize($instance->print),
                    "bbox" => serialize($instance->bbox),
                    "image" => $name,
                  ]);
                }
                $data[] = $name;
            }
         }
         // return '2';

         return $data;
        // return back()->with('success', 'Your images has been successfully');

        // return redirect('stamp/stamps')->with('flash_message', 'Stamp added!');
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
        $stamp = Stamp::findOrFail($id);

        return view('stamp.stamps.show', compact('stamp'));
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
        $stamp = Stamp::findOrFail($id);

        return view('stamp.stamps.edit', compact('stamp'));
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
			'print' => 'required',
			'image' => 'required|max:255',
			'bbox' => 'required',
			'report_id' => 'required|numeric'
		]);
        $requestData = $request->all();

        $stamp = Stamp::findOrFail($id);
        $stamp->update($requestData);

        return redirect('stamp/stamps')->with('flash_message', 'Stamp updated!');
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
        Stamp::destroy($id);

        return redirect('stamp/stamps')->with('flash_message', 'Stamp deleted!');
    }
}

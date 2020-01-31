<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Storage;
use App\Stamp;

class StampController extends Controller
{
    public function extractFace(Request $request)
    {
        $this->validate($request, [
			// 'print' => 'required',
			'image' => 'required|max:255',
			// 'bbox' => 'required',
			'report_id' => 'required|numeric',
      // 'images' => 'required',
      'images.*' => 'image|mimes:jpeg,png,jpg|max:2048'
		]);
        $requestData = $request->all();

        $data[] = [];
        return '1';
        if($request->hasfile('images'))
         {

            foreach($request->file('images') as $image)
            {
                $name=$image->getClientOriginalName();
                $image->move(storage_path().'/people/', $name);

                $prints = extractFace(storage_path().'/people/', $name);
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
         return '2';

         return $data;
        // return back()->with('success', 'Your images has been successfully');

        // return redirect('stamp/stamps')->with('flash_message', 'Stamp added!');
    }
}

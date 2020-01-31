<?php

namespace App\Http\Controllers\Report;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Report;
use App\Stamp;
use App\Area;
use App\Suspect;
use App\Match;
use Validator;
use File;
use View;
use Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
                ->orWhere('type', 'LIKE', "%$keyword%")
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
        $areas = Area::all();
        return view('report.reports.create', compact('areas'));
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

        // Validation rules
        $rules = [
          'name' => 'required|max:255',
    			'email' => 'required|max:255|email',
    			'phone' => 'required|max:255',
          // 'phone' => 'required|numeric|digits:11',
    			'birth' => 'required',
    			'gender' => 'required',
    			'type' => 'required',
    			'area_id' => 'required|numeric',
    			'lat' => 'numeric',
    			'lang' => 'numeric',
          'images.*' => 'image|mimes:jpeg,png,jpg|max:5000'
        ];

        // Validation
        $valid = Validator::make($request->all(), $rules);
        if ($valid->fails()) {
            return response()->json(['status' => 400,
                                     'data' => $valid->errors()->all()], 200);
        }

        // Create the request
        $requestData = $request->all();
        $report = Report::create($requestData);

        $data = [];
        $final_stamps = [];
        $excluded_stamps = [];

        // increase max exec time to 900
        ini_set('max_execution_time', 900);


        if($request->hasfile('images'))
        {

          // return var_dump($request->file('images'));
            // for every image in the request
            foreach($request->file('images') as $image)
            {
                // Extract the face prints
                $name=$image->getClientOriginalExtension();
                $name = randomString(10).".".$name;
                $image->move(storage_path().'/app/public/people/', $name);
                // return storage_path().'/people/'.$name;
                $prints = extractFace(storage_path().'/app/public/people/'.$name);

                // Save the faces found in the image
                $i = 0;
                foreach ($prints as $instance) {
                  if($i == 0){
                    $data[] = $name;
                  }
                  $tmp_stamp = Stamp::create([
                    "report_id" => $report->id,
                    "print" => serialize($instance->print),
                    "bbox" => serialize($instance->bbox),
                    "image" => $name,
                  ]);
                  $i++;

                  // getting the stamps in the format that the compareFaces function will understand
                  $final_stamps[$tmp_stamp->id.""] = array(
                      "print" => $instance->print,
                      "bbox"	=> $instance->bbox
                    );
                  // Collect the ids of generated stamps for future use
                  $excluded_stamps[] = $tmp_stamp->id;
                }
            }
         }
         else{
           return response()->json(['status' => 400,
                                    'data' => ['No files found']], 200);
         }

         // if nofaces found selete the request and return the an error message
         if(empty($data)){
           $report->delete();
           return response()->json(['status' => 400,
                                    'data' => ['No faces found on any images']], 200);
         }

         $my_stamps_json = json_encode($final_stamps);

         // make rthe missing report search the found report and vise versa
         if ($request->type == 'missing') {
           $target = "found";
         }
         else{
           $target = "missing";
         }

         // select the stmaps with out the generated before
         $other_stamps_json = [];
         $other_stamps = Stamp::
         whereNotIn('id', $excluded_stamps)
         ->whereHas('report', function ($query) use ($target) {
             $query->where('type', $target);
         })
         ->get();

         // getting the stamps in the format that the compareFaces function will understand
         foreach ($other_stamps as $value) {
           $other_stamps_json[$value->id.""] = array(
               "print" => unserialize($value->print),
               "bbox"	=> unserialize($value->bbox)
             );
         }

         $other_stamps_json = json_encode($other_stamps_json);


         // save stamps in json files so the compareFaces function can use them
         $fileName1 = time().'first_datafile.json';
         $fileName2 = time().'second_datafile.json';

         File::put(public_path('storage/json/'.$fileName1), $my_stamps_json);
         File::put(public_path('storage/json/'.$fileName2), $other_stamps_json);

         $similarity = compareFaces(storage_path().'/app/public/json/'.$fileName1, storage_path().'/app/public/json/'.$fileName2);

         // sort them by desc by the distance

         usort($similarity, function ($a, $b){
           if($a->distance == $b->distance){ return 0 ; }
           return ($a->distance < $b->distance) ? -1 : 1;
         });

         // filter the prints with distance more than  0.6
         $reports_id = [];
         foreach ($similarity as $key => $value) {
           if($value->distance <=  0.6)
            $reports_id[] = $value->missingID;
         }

         // Get the required reports with it's own stamps
         $required_reports = Report::
         whereHas('stamps', function ($query) use ($reports_id) {
             $query->whereIn('id', $reports_id);
         })
         // ->with('stamps')
         ->get();

         foreach ($required_reports as $value) {

           $value->images = $value->images();

           if ($request->type == 'missing') {
             $tmp_match = Match::where('missing_id', $report->id)->where('found_id', $value->id)->first();
           }
           else{
             $tmp_match = Match::where('missing_id', $value->id)->where('found_id', $report->id)->first();
           }

           $last_match = Match::orderBy('created_at', 'desc')->first();
            if(!$last_match){
              $last_match = 0;
            }
            else{
              $last_match = $last_match->serial + 1;
            }

           if(!$tmp_match){
             if ($request->type == 'missing') {
               Match::create(['missing_id' => $report->id, 'found_id' => $value->id, 'serial' => $last_match]);
             }
             else{
               Match::create(['missing_id' => $value->id, 'found_id' => $report->id, 'serial' => $last_match]);
             }
           }

         }

         // return the final response
         return response()->json([
             'data' => $required_reports,
             'status' =>  200
             ], 200);
    }

    public function storeFromCamera(Request $request)
    {

        // Validation rules
        $rules = [
          'name' => 'required|max:255',
          'email' => 'required|max:255|email',
    			'phone' => 'required|max:255',
    			'type' => 'required',
    			'reporter_type' => 'required',
    			'lat' => 'numeric',
          'lang' => 'numeric',
    			'area_id' => 'numeric',
          'images.*' => 'image|mimes:jpeg,png,jpg|max:5000'
        ];

        // Validation
        $valid = Validator::make($request->all(), $rules);
        if ($valid->fails()) {
            return response()->json(['status' => 400,
                                     'data' => $valid->errors()->all()], 200);
        }

        // Create the request
        $requestData = $request->all();
        // return response()->json(['status' => 200,
        //                          'data' => $requestData], 200);

        $request->type = 'found';
        $requestData['type'] = 'found';

        // try {
          $report = Report::create($requestData);
        // }
        // catch(Exception $e) {
        //   // echo 'Message: ' .$e->getMessage();
        //   return response()->json(['status' => 200,
        //                            'data' => $e->getMessage()], 200);
        // }



        $data = [];
        $final_stamps = [];
        $excluded_stamps = [];

        // increase max exec time to 900
        ini_set('max_execution_time', 900);


        if($request->hasfile('images'))
        {
            // for every image in the request
            foreach($request->file('images') as $image)
            {
                // Extract the face prints
                $name=$image->getClientOriginalExtension();
                $name = randomString(10).".".$name;
                $image->move(storage_path().'/app/public/people/', $name);
                // return storage_path().'/people/'.$name;
                $prints = extractFace(storage_path().'/app/public/people/'.$name);

                // Save the faces found in the image
                $i = 0;
                foreach ($prints as $instance) {
                  if($i == 0){
                    $data[] = $name;
                  }
                  $tmp_stamp = Stamp::create([
                    "report_id" => $report->id,
                    "print" => serialize($instance->print),
                    "bbox" => serialize($instance->bbox),
                    "image" => $name,
                  ]);
                  $i++;

                  // getting the stamps in the format that the compareFaces function will understand
                  $final_stamps[$tmp_stamp->id.""] = array(
                      "print" => $instance->print,
                      "bbox"	=> $instance->bbox
                    );
                  // Collect the ids of generated stamps for future use
                  $excluded_stamps[] = $tmp_stamp->id;
                }
            }
         }

         // if nofaces found selete the request and return the an error message
         if(empty($data)){
           $report->delete();
           return response()->json([
               'status' =>  200
               ], 200);
         }

         $my_stamps_json = json_encode($final_stamps);

         // select the stmaps with out the generated before
         $other_stamps_json = [];
         $other_stamps = Stamp::
         whereNotIn('id', $excluded_stamps)
         ->whereHas('report', function ($query) {
             $query->where('type', 'missing');
         })
         ->doesnthave('suspect')
         ->get();

         // getting the stamps in the format that the compareFaces function will understand
         foreach ($other_stamps as $value) {
           $other_stamps_json[$value->id.""] = array(
               "print" => unserialize($value->print),
               "bbox"	=> unserialize($value->bbox)
             );
         }

         $other_stamps_json = json_encode($other_stamps_json);


         // save stamps in json files so the compareFaces function can use them
         $fileName1 = time().'first_datafile.json';
         $fileName2 = time().'second_datafile.json';

         File::put(public_path('storage/json/'.$fileName1), $my_stamps_json);
         File::put(public_path('storage/json/'.$fileName2), $other_stamps_json);

         $similarity = compareFaces(storage_path().'/app/public/json/'.$fileName1, storage_path().'/app/public/json/'.$fileName2);

         Storage::delete(['/public/json/'.$fileName1, '/public/json/'.$fileName2]);
         // return $similarity;
         $num_of_high_similarity = 0;
         foreach ($similarity as $value) {
           if($value->distance <=  0.6){
             $num_of_high_similarity++;
          }
         }
         if($num_of_high_similarity == 0){
           $report->delete();
           Stamp::destroy($excluded_stamps);
           // print_r('public/people/'.$name);
           Storage::delete('/public/people/'.$name);
           return response()->json([
               'message' => 'no_missing_person_found',
               'status' =>  200
               ], 200);
         }

         // filter the prints with distance more than  0.6
         $reports_id = [];
         $execluded_from_suspects_id = [];

         // foreach ($similarity as $key => $value) {
         //   if($value->distance <= 0.6)
         //    $reports_id[] = $value->foundID;
         //   else{
         //    $suspects_id[] = $value->missingID;
         //    $suspects_id[] = $value->foundID;
         //   }
         // }
         // print_r($similarity);
         foreach ($similarity as $key => $value) {
           if($value->distance <= 0.6){
             // echo "distance between ".$value->missingID." and ".$value->foundID;
            $reports_id[] = $value->missingID;
            $execluded_from_suspects_id[] = $value->foundID;
          }
         }

         $execluded_from_suspects_id = array_unique($execluded_from_suspects_id);
         $suspects_stamps_id = array_diff($excluded_stamps, $execluded_from_suspects_id);
         $suspects_stamps_id = array_unique($suspects_stamps_id);

         // print_r($excluded_stamps);
         // print_r($execluded_from_suspects_id);
         // print_r($suspects_stamps_id);
         // return $suspects_stamps_id;


         $old_suspects_stamps = Stamp::
         whereNotIn('id', $suspects_stamps_id)
         ->has('suspect')
         ->get();

         $old_suspects_stamps = $old_suspects_stamps->unique('suspect_id');

         if($old_suspects_stamps->isEmpty()){
           $new_suspects_stamps = Stamp::
           whereIn('id', $suspects_stamps_id)
           // ->doesnthave('suspect')
           ->get();
           foreach ($new_suspects_stamps as $new_suspects_stamp) {
             $tmp_suspect = Suspect::create(['name' => '']);
             $new_suspects_stamp->suspect_id = $tmp_suspect->id;
             $new_suspects_stamp->save();
           }

           return response()->json([
               'message' => 'created_initial_new_suspects',
               'status' =>  200
               ], 200);
         }

         $old_suspects_tokens = [];
         foreach ($old_suspects_stamps as $value) {
           $old_suspects_tokens[$value->id.""] = array(
               "print" => unserialize($value->print),
               "bbox"	=> unserialize($value->bbox)
             );
         }

         $new_suspects_stamps = Stamp::
         whereIn('id', $suspects_stamps_id)
         // ->doesnthave('suspect')
         ->get();

         $new_suspects_tokens = [];
         foreach ($new_suspects_stamps as $value) {
           $new_suspects_tokens[$value->id.""] = array(
               "print" => unserialize($value->print),
               "bbox"	=> unserialize($value->bbox)
             );
         }

         $new_suspects_json = json_encode($new_suspects_tokens);
         $old_suspects_json = json_encode($old_suspects_tokens);


         // save stamps in json files so the compareFaces function can use them
         $suspectsFileName1 = time().'new_suspects_datafile.json';
         $suspectsFileName2 = time().'old_suspects_datafile.json';

         File::put(public_path('storage/json/'.$suspectsFileName1), $new_suspects_json);
         File::put(public_path('storage/json/'.$suspectsFileName2), $old_suspects_json);

         $suspects_similarity = compareFaces(storage_path().'/app/public/json/'.$suspectsFileName1, storage_path().'/app/public/json/'.$suspectsFileName2);

         Storage::delete(['/public/json/'.$suspectsFileName1, '/public/json/'.$suspectsFileName2]);

         // print_r($suspects_similarity);
         // print_r($old_suspects_stamps);

         //
         // foreach ($suspects_similarity as $single_similarity) {
         //   // echo 'distance is '.$single_similarity->distance;
         //   if($single_similarity->distance <=  0.6){
         //     echo 'distance is '.$single_similarity->distance.' less |';
         //     $tmp_stamp = Stamp::find($single_similarity->missingID);
         //     $tmp_stamp->suspect->appearance_times++;
         //     $tmp_stamp->suspect->save();
         //   }
         //   else{
         //     echo 'distance is '.$single_similarity->distance.' more |';
         //     $tmp_stamp = Stamp::find($single_similarity->foundID);
         //     $tmp_suspect = Suspect::create(['name' => '']);
         //     $tmp_stamp->suspect_id = $tmp_suspect->id;
         //     $tmp_stamp->save();
         //   }
         // }

         $flag = 0;
         for ($i=0; $i < count($suspects_similarity) ; $i++) {
           if($suspects_similarity[$i]->distance <=  0.6){
             // echo 'distance is '.$suspects_similarity[$i]->distance.' less |';
             $tmp_stamp = Stamp::find($suspects_similarity[$i]->missingID);
             $tmp_stamp->suspect->appearance_times++;
             $tmp_stamp->suspect->save();

             $other_tmp_stamp = Stamp::find($suspects_similarity[$i]->foundID);
             $other_tmp_stamp->suspect_id = $tmp_stamp->suspect_id;
             $other_tmp_stamp->save();

             $flag = 1;
           }
           else{

             if( !isset($suspects_similarity[$i+1]) ){
               if($flag == 0){
                 // echo 'distance is '.$suspects_similarity[$i]->distance.' more |';
                 $tmp_stamp = Stamp::find($suspects_similarity[$i]->foundID);
                 $tmp_suspect = Suspect::create(['name' => '']);
                 $tmp_stamp->suspect_id = $tmp_suspect->id;
                 $tmp_stamp->save();
               }
               $flag = 0;
               break;
             }
             elseif($suspects_similarity[$i]->foundID == $suspects_similarity[$i+1]->foundID){
               continue;
             }
             if($flag == 0){
               // echo 'distance is '.$suspects_similarity[$i]->distance.' more |';
               $tmp_stamp = Stamp::find($suspects_similarity[$i]->foundID);
               $tmp_suspect = Suspect::create(['name' => '']);
               $tmp_stamp->suspect_id = $tmp_suspect->id;
               $tmp_stamp->save();
             }
             $flag = 0;
           }
         }



         // Get the required reports with it's own stamps
         $required_reports = Report::
         whereHas('stamps', function ($query) use ($reports_id) {
             $query->whereIn('id', $reports_id);
         })
         ->with('stamps')
         ->get();
         // return $required_reports;

         // Fire notifications to the reports owners
         $matches =[];
         foreach ($required_reports as $single_report) {
           // Send SMS to this phone $single_report->phone
           $tmp_match = Match::where('missing_id', $single_report->id)->where('found_id', $report->id)->first();
           $last_match = Match::orderBy('created_at', 'desc')->first();
            if(!$last_match){
              $last_match = 0;
            }
            else{
              $last_match = $last_match->serial + 1;
            }

           if(!$tmp_match){
             $matches[] = Match::create(['missing_id' => $single_report->id, 'found_id' => $report->id, 'serial' => $last_match]);
           }

         }

         return $matches;

         // return the final response
         return response()->json([
             'message' => 'success_added_some_suspects',
             'status' =>  200
             ], 200);
    }

    public function storePersonWithSuspect(Request $request)
    {

        // Validation rules
        $rules = [
          'name' => 'required|max:255',
    			'email' => 'required|max:255|email',
    			'phone' => 'required|max:255',
          // 'phone' => 'required|numeric|digits:11',
    			'birth' => 'required',
    			'gender' => 'required',
    			'type' => 'required',
    			'area_id' => 'required|numeric',
    			'lat' => 'numeric',
    			'lang' => 'numeric',
          'images.*' => 'image|mimes:jpeg,png,jpg|max:5000',
          'suspect_images.*' => 'image|mimes:jpeg,png,jpg|max:5000'
        ];

        // Validation
        $valid = Validator::make($request->all(), $rules);
        if ($valid->fails()) {
            return response()->json(['status' => 400,
                                     'data' => $valid->errors()->all()], 200);
        }

        // Create the request
        $requestData = $request->all();
        $report = Report::create($requestData);

        $data = [];
        $final_stamps = [];
        $excluded_stamps = [];

        // increase max exec time to 900
        ini_set('max_execution_time', 900);


        if($request->hasfile('images'))
        {

          // return var_dump($request->file('images'));
            // for every image in the request
            foreach($request->file('images') as $image)
            {
                // Extract the face prints
                $name=$image->getClientOriginalExtension();
                $name = randomString(10).".".$name;
                $image->move(storage_path().'/app/public/people/', $name);
                // return storage_path().'/people/'.$name;
                $prints = extractFace(storage_path().'/app/public/people/'.$name);

                // Save the faces found in the image
                $i = 0;
                foreach ($prints as $instance) {
                  if($i == 0){
                    $data[] = $name;
                  }
                  $tmp_stamp = Stamp::create([
                    "report_id" => $report->id,
                    "print" => serialize($instance->print),
                    "bbox" => serialize($instance->bbox),
                    "image" => $name,
                  ]);
                  $i++;

                  // getting the stamps in the format that the compareFaces function will understand
                  $final_stamps[$tmp_stamp->id.""] = array(
                      "print" => $instance->print,
                      "bbox"	=> $instance->bbox
                    );
                  // Collect the ids of generated stamps for future use
                  $excluded_stamps[] = $tmp_stamp->id;
                }
            }
         }
         else{
           return response()->json(['status' => 400,
                                    'data' => ['No files found']], 200);
         }
         if(empty($data)){
           $report->delete();
           return response()->json(['status' => 400,
                                    'data' => ['No faces sent on any images']], 200);
         }

         $suspects_data = [];
         $suspects_final_stamps = [];
         $suspects_excluded_stamps = [];

         if($request->hasfile('suspect_images'))
         {

           // return var_dump($request->file('images'));
             // for every image in the request
             foreach($request->file('suspect_images') as $image)
             {
                 // Extract the face prints
                 $name=$image->getClientOriginalExtension();
                 $name = randomString(10).".".$name;
                 $image->move(storage_path().'/app/public/people/', $name);
                 // return storage_path().'/people/'.$name;
                 $prints = extractFace(storage_path().'/app/public/people/'.$name);

                 // Save the faces found in the image
                 $i = 0;
                 foreach ($prints as $instance) {
                   if($i == 0){
                     $suspects_data[] = $name;
                   }
                   $tmp_stamp = Stamp::create([
                     "report_id" => $report->id,
                     "print" => serialize($instance->print),
                     "bbox" => serialize($instance->bbox),
                     "image" => $name,
                   ]);
                   $i++;

                   // getting the stamps in the format that the compareFaces function will understand
                   $suspects_final_stamps[$tmp_stamp->id.""] = array(
                       "print" => $instance->print,
                       "bbox"	=> $instance->bbox
                     );
                   // Collect the ids of generated stamps for future use
                   $suspects_excluded_stamps[] = $tmp_stamp;
                 }
             }
          }
          else{
            return response()->json(['status' => 400,
                                     'data' => ['No files found']], 200);
          }

         // if nofaces found selete the request and return the an error message
         if(empty($suspects_data)){
           $report->delete();
           return response()->json(['status' => 400,
                                    'data' => ['No suspected faces sent on any images']], 200);
         }

         //////////////////////////////////////////////////////
         $my_stamps_json = json_encode($final_stamps);

         // select the stmaps with out the generated before
         $other_stamps_json = [];
         $other_stamps = Stamp::
         whereNotIn('id', $excluded_stamps)
         ->whereHas('report', function ($query) {
             $query->where('type', 'missing');
         })
         ->get();

         // getting the stamps in the format that the compareFaces function will understand
         foreach ($other_stamps as $value) {
           $other_stamps_json[$value->id.""] = array(
               "print" => unserialize($value->print),
               "bbox"	=> unserialize($value->bbox)
             );
         }

         $other_stamps_json = json_encode($other_stamps_json);


         // save stamps in json files so the compareFaces function can use them
         $fileName1 = time().'first_datafile.json';
         $fileName2 = time().'second_datafile.json';

         File::put(public_path('storage/json/'.$fileName1), $my_stamps_json);
         File::put(public_path('storage/json/'.$fileName2), $other_stamps_json);

         $similarity = compareFaces(storage_path().'/app/public/json/'.$fileName1, storage_path().'/app/public/json/'.$fileName2);

         // sort them by desc by the distance

         usort($similarity, function ($a, $b){
           if($a->distance == $b->distance){ return 0 ; }
           return ($a->distance < $b->distance) ? -1 : 1;
         });

         // filter the prints with distance more than  0.6
         $reports_id = [];
         foreach ($similarity as $key => $value) {
           if($value->distance <=  0.6)
            $reports_id[] = $value->missingID;
         }

         //////////////////////////////////////////////////////


         $old_suspects_stamps = Stamp::
         has('suspect')
         ->get();

         $old_suspects_stamps = $old_suspects_stamps->unique('suspect_id');

         if($old_suspects_stamps->isEmpty()){
           foreach ($suspects_excluded_stamps as $new_suspects_stamp) {
             $tmp_suspect = Suspect::create(['name' => '']);
             $new_suspects_stamp->suspect_id = $tmp_suspect->id;
             $new_suspects_stamp->save();
           }
         }

         $old_suspects_tokens = [];
         foreach ($old_suspects_stamps as $value) {
           $old_suspects_tokens[$value->id.""] = array(
               "print" => unserialize($value->print),
               "bbox"	=> unserialize($value->bbox)
             );
         }


         $new_suspects_json = json_encode($suspects_final_stamps);
         $old_suspects_json = json_encode($old_suspects_tokens);


         // save stamps in json files so the compareFaces function can use them
         $suspectsFileName1 = time().'new_suspects_datafile.json';
         $suspectsFileName2 = time().'old_suspects_datafile.json';

         File::put(public_path('storage/json/'.$suspectsFileName1), $new_suspects_json);
         File::put(public_path('storage/json/'.$suspectsFileName2), $old_suspects_json);

         $suspects_similarity = compareFaces(storage_path().'/app/public/json/'.$suspectsFileName1, storage_path().'/app/public/json/'.$suspectsFileName2);

         Storage::delete(['/public/json/'.$suspectsFileName1, '/public/json/'.$suspectsFileName2]);

         // print_r($suspects_similarity);
         // print_r($old_suspects_stamps);

         $flag = 0;
         for ($i=0; $i < count($suspects_similarity) ; $i++) {
           if($suspects_similarity[$i]->distance <=  0.6){
             // echo 'distance is '.$suspects_similarity[$i]->distance.' less |';
             $tmp_stamp = Stamp::find($suspects_similarity[$i]->missingID);
             $tmp_stamp->suspect->appearance_times++;
             $tmp_stamp->suspect->save();

             $other_tmp_stamp = Stamp::find($suspects_similarity[$i]->foundID);
             $other_tmp_stamp->suspect_id = $tmp_stamp->suspect_id;
             $other_tmp_stamp->save();

             $flag = 1;
           }
           else{
             if( !isset($suspects_similarity[$i+1]) ){
               if($flag == 0){
                 // echo 'distance is '.$suspects_similarity[$i]->distance.' more |';
                 $tmp_stamp = Stamp::find($suspects_similarity[$i]->foundID);
                 $tmp_suspect = Suspect::create(['name' => '']);
                 $tmp_stamp->suspect_id = $tmp_suspect->id;
                 $tmp_stamp->save();
                 // print_r($suspects_similarity[$i]);
                 // echo "one";
               }
               $flag = 0;
               break;
             }
             if($suspects_similarity[$i]->foundID == $suspects_similarity[$i+1]->foundID){
               continue;
             }
             if($flag == 0){
               // echo 'distance is '.$suspects_similarity[$i]->distance.' more |';
               $tmp_stamp = Stamp::find($suspects_similarity[$i]->foundID);
               $tmp_suspect = Suspect::create(['name' => '']);
               $tmp_stamp->suspect_id = $tmp_suspect->id;
               $tmp_stamp->save();
               // print_r($suspects_similarity[$i]);
               // echo "two";
             }
             $flag = 0;
           }
         }


         // Get the required reports with it's own stamps
         $required_reports = Report::
         whereHas('stamps', function ($query) use ($reports_id) {
             $query->whereIn('id', $reports_id);
         })
         // ->with('stamps')
         ->get();


         foreach ($required_reports as $value) {
           // $value->stamps_info;
           $value->images = $value->images();

           $tmp_match = Match::where('missing_id', $value->id)->where('found_id', $report->id)->first();
           $last_match = Match::orderBy('created_at', 'desc')->first();
if(!$last_match){
  $last_match = 0;
}
else{
  $last_match = $last_match->serial + 1;
}

           if(!$tmp_match){
             Match::create(['missing_id' => $value->id, 'found_id' => $report->id, 'serial' => $last_match]);
           }

         }

         // return the final response
         return response()->json([
             'data' => $required_reports,
             'status' =>  200
             ], 200);
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


    public function jsonFile()
    {
      $data1 = '{ "11": {"print": [-0.13078740239143372, 0.05699743330478668, 0.0589718222618103, -0.1072985827922821, -0.11302723735570908, -0.0031552023719996214, 0.033165525645017624, -0.1062813550233841, 0.2005702257156372, -0.1361413300037384, 0.16931617259979248, -0.054699208587408066, -0.21879363059997559, 0.03022335283458233, -0.020066550001502037, 0.17841319739818573, -0.22869303822517395, -0.20276740193367004, -0.14039075374603271, -0.08215776085853577, -0.08989905565977097, 0.04933314770460129, -0.01939287781715393, -0.004038335755467415, -0.11520800739526749, -0.28747519850730896, -0.03420889005064964, -0.051934294402599335, 0.10838402062654495, -0.0637214407324791, 0.029490092769265175, 0.12088758498430252, -0.23536428809165955, -0.013444767333567142, 0.0562337227165699, 0.04788058623671532, -0.14770621061325073, -0.09646288305521011, 0.2513740360736847, -0.06407211720943451, -0.18936043977737427, -0.06315891444683075, 0.1016397699713707, 0.2033207267522812, 0.2637816369533539, -0.06604819744825363, -0.038224391639232635, -0.11189572513103485, 0.14019499719142914, -0.37019094824790955, 0.023677010089159012, 0.17967143654823303, 0.031952787190675735, 0.1488112211227417, 0.03313243389129639, -0.08275288343429565, -0.020179321989417076, 0.1846354454755783, -0.1602129191160202, 0.014229552820324898, 0.07779120653867722, -0.09080684930086136, -0.0685044527053833, -0.041470710188150406, 0.21089725196361542, 0.07565251737833023, -0.1546817421913147, -0.11907748878002167, 0.16255958378314972, -0.1308763474225998, -0.022589828819036484, 0.04033413156867027, -0.056725796312093735, -0.16327108442783356, -0.23837986588478088, 0.047994405031204224, 0.3449160158634186, 0.04401777312159538, -0.03072611801326275, 0.09828738123178482, -0.08149770647287369, -0.07519827038049698, -0.0361797958612442, 0.03440305218100548, -0.13723783195018768, -0.11258047819137573, 0.012476296164095402, 0.10378436744213104, 0.23691529035568237, -0.044350843876600266, 0.04065922275185585, 0.19867028295993805, -0.03780180215835571, -0.05688966438174248, -0.005433179438114166, 0.12115970999002457, -0.05887814238667488, -0.03966984152793884, -0.12887559831142426, 0.004306728020310402, 0.07863118499517441, -0.04490264877676964, 0.018101640045642853, 0.1560482680797577, -0.16682109236717224, 0.17697609961032867, -0.019891230389475822, -0.0015683422097936273, 0.08109551668167114, -0.0920928567647934, -0.0395466610789299, -0.056501008570194244, 0.11505547165870667, -0.2343451827764511, 0.20471294224262238, 0.10457388311624527, -0.02879505604505539, 0.13459616899490356, 0.05213336646556854, 0.029725395143032074, 0.02558990754187107, 0.0037445526104420424, -0.14207430183887482, -0.06088924780488014, 0.12535886466503143, 0.049325697124004364, -0.020970076322555542, 0.030013367533683777], "bbox": [96, 404, 186, 315]}, "64": {"print": [-0.02070416882634163, -0.011537429876625538, -0.02721215970814228, 0.019968967884778976, -0.06270445138216019, -0.0708947628736496, -0.0219064150005579, -0.0767543613910675, 0.05427514389157295, -0.10872095823287964, 0.15872569382190704, -0.08593966066837311, -0.20005598664283752, -0.05580668896436691, 0.0587969645857811, 0.10683813691139221, -0.11605394631624222, 0.0005828288267366588, -0.1585129201412201, -0.09462446719408035, -0.03234988823533058, 0.07510856539011002, -0.0013528293929994106, 0.009748931974172592, 0.012261715717613697, -0.31037867069244385, -0.032887425273656845, -0.0858936607837677, 0.06960128992795944, -0.003651997772976756, 0.07042116671800613, 0.10139604657888412, -0.1907023936510086, -0.0924278125166893, 0.05000436678528786, 0.009696352295577526, -0.1401595175266266, -0.04757005348801613, 0.2196255773305893, 0.0395989827811718, -0.1739826649427414, -0.0068080732598900795, 0.0013396571157500148, 0.25100064277648926, 0.28414952754974365, -0.012823651544749737, -0.052673421800136566, -0.006386162713170052, 0.14775587618350983, -0.32073774933815, -0.0017836869228631258, 0.13808147609233856, 0.08920954912900925, 0.04210071265697479, 0.12756936252117157, -0.07702484726905823, 0.0033401534892618656, 0.12411274015903473, -0.19363150000572205, 0.016940826550126076, 0.04360027611255646, -0.07644880563020706, -0.029129059985280037, -0.07946601510047913, 0.14764323830604553, 0.08276494592428207, -0.1033242717385292, -0.09326183050870895, 0.10649038106203079, -0.19995753467082977, -0.04657309129834175, 0.03798101097345352, -0.07647096365690231, -0.18073052167892456, -0.324674129486084, 0.05716531723737717, 0.26705917716026306, 0.15291914343833923, -0.1357075423002243, 0.0039789411239326, 0.028095446527004242, -0.02166990377008915, 0.1146150678396225, 0.04411555081605911, -0.04230969771742821, -0.10104762017726898, 0.02721075899899006, -0.02874230220913887, 0.11648387461900711, -0.03161235153675079, -0.08653569966554642, 0.20742374658584595, -0.11835654079914093, -0.07438044995069504, 0.045073483139276505, 0.0016241512494161725, -0.09453078359365463, -0.09108607470989227, -0.16421641409397125, -0.06438042223453522, 0.11672285944223404, -0.15238063037395477, 0.05468588322401047, 0.12799441814422607, -0.17506642639636993, 0.175753653049469, -0.10463547706604004, 0.01860506646335125, 0.07388805598020554, -0.06423480808734894, -0.132423534989357, 0.07956398278474808, 0.17072373628616333, -0.1362118273973465, 0.25558948516845703, 0.09550351649522781, -0.03754504770040512, 0.10693741589784622, 0.10106996446847916, 0.02027699165046215, -0.07662919908761978, 0.021443696692585945, -0.1665932834148407, -0.15738248825073242, 0.03553364798426628, 0.08244133740663528, 0.039142873138189316, -0.049538686871528625], "bbox": [140, 294, 247, 187]}}';

      $data2 = '{"0": {"print": [-0.0812625139951706, 0.06787895411252975, 0.09088464081287384, -0.0631825253367424, -0.10153525322675705, -0.03600146621465683, -0.04250733554363251, 0.009166432544589043, 0.08061385154724121, 0.006225584074854851, 0.30494096875190735, -0.030662087723612785, -0.23977135121822357, -0.04638512805104256, -0.02310442551970482, 0.07413017004728317, -0.13984081149101257, -0.0014846117701381445, -0.13579727709293365, -0.0898546576499939, 0.032539624720811844, 0.03065079264342785, 0.033364225178956985, -0.026200788095593452, -0.24857400357723236, -0.36708420515060425, -0.051768261939287186, -0.048551395535469055, 0.07155207544565201, -0.14599525928497314, 0.0397927463054657, 0.06400136649608612, -0.17871461808681488, -0.085935078561306, 0.07205653935670853, 0.04688960313796997, -0.10451062768697739, -0.07707422226667404, 0.18605388700962067, 0.05849635228514671, -0.16126759350299835, 0.07791361957788467, 0.0809348076581955, 0.30458155274391174, 0.19054867327213287, 0.03642189875245094, 0.03942261263728142, -0.10074981302022934, 0.14109529554843903, -0.25678178668022156, 0.08977402001619339, 0.15731662511825562, 0.16716612875461578, 0.08002547919750214, 0.07114626467227936, -0.12307547777891159, 0.02474382147192955, 0.2209848165512085, -0.2939227819442749, 0.09988752752542496, 0.07849174737930298, -0.044572118669748306, -0.06344396620988846, -0.09934299439191818, 0.1588796228170395, 0.1050034761428833, -0.2252665013074875, -0.08983524888753891, 0.1298246681690216, -0.0815972313284874, 0.05891713872551918, 0.10296003520488739, -0.10248153656721115, -0.2450372874736786, -0.25610435009002686, 0.14481808245182037, 0.3474407494068146, 0.14756982028484344, -0.19586297869682312, -0.08176887780427933, -0.07931870967149734, -0.036270447075366974, -0.030975937843322754, 0.09577395766973495, -0.1257384568452835, -0.08343110978603363, -0.0017380217323079705, 0.06325674802064896, 0.11185410618782043, 0.008553785271942616, -0.011478283442556858, 0.2599863111972809, 0.03712335228919983, -0.0048448750749230385, -0.0036418987438082695, 0.06265851855278015, -0.2763870060443878, -0.03213778883218765, -0.07885895669460297, -0.011100365780293941, 0.014153815805912018, -0.1565496027469635, 0.041810836642980576, 0.12598079442977905, -0.15506115555763245, 0.25405198335647583, -0.012408371083438396, -0.04065000265836716, 0.067290760576725, -0.030153725296258926, -0.013538709841668606, -0.08060675859451294, 0.10899674147367477, -0.2998250126838684, 0.1929979771375656, 0.181269109249115, 0.11967215687036514, 0.1548060029745102, 0.10780161619186401, 0.030950117856264114, 0.03202132508158684, 0.056150950491428375, -0.1163189560174942, -0.03846355155110359, -0.01140556950122118, -0.11385630071163177, 0.02791486494243145, -0.05197514221072197], "bbox": [72, 527, 146, 453]}, "1": {"print": [-0.06454494595527649, 0.11610178649425507, 0.05866191163659096, -0.04840463399887085, 0.010394838638603687, -0.03782980144023895, -0.046886708587408066, -0.06009560823440552, 0.16031929850578308, -0.06966986507177353, 0.25829654932022095, 0.023357704281806946, -0.14192251861095428, -0.11095933616161346, 0.024011919274926186, 0.1251877248287201, -0.22394348680973053, -0.039007216691970825, -0.18742212653160095, 0.0031455163843929768, -0.014570966362953186, -0.01292214635759592, 0.12136238813400269, 0.012507655657827854, -0.08472990989685059, -0.4345657229423523, -0.10044340044260025, -0.061162397265434265, 0.09331356734037399, -0.1324671059846878, -0.04561009258031845, -0.04780697450041771, -0.2600319981575012, -0.11496637016534805, -0.05004294961690903, -0.010482133366167545, -0.03704367205500603, -0.023391805589199066, 0.21184389293193817, 0.08031219244003296, -0.16417594254016876, 0.10019156336784363, -0.0593215674161911, 0.24504201114177704, 0.2869440019130707, 0.05927431955933571, 0.035695631057024, -0.03502620756626129, 0.10013378411531448, -0.2668636441230774, 0.058527685701847076, 0.11765900254249573, 0.08310134708881378, 0.030588295310735703, 0.02637466788291931, -0.08843602985143661, -0.026284368708729744, 0.22170598804950714, -0.16837775707244873, 0.10390792787075043, 0.041082292795181274, -0.12843438982963562, -0.05581916868686676, 0.027265194803476334, 0.17325833439826965, 0.12377951294183731, -0.12085484713315964, -0.13542136549949646, 0.12814544141292572, -0.08305761218070984, 0.04640631005167961, -0.011057484894990921, -0.09619507193565369, -0.2004881203174591, -0.32930418848991394, 0.09983634203672409, 0.31449976563453674, 0.1203010156750679, -0.22871391475200653, -0.018442165106534958, -0.1401933878660202, 0.031889669597148895, 0.04728382080793381, 0.09661262482404709, -0.07274297624826431, -0.05560405179858208, -0.08965516835451126, 0.031062563881278038, 0.10186588019132614, 0.03505909815430641, -0.035910747945308685, 0.24648889899253845, -0.05304611101746559, 0.04649351164698601, 0.004572571720927954, 0.037178970873355865, -0.14777621626853943, -0.01779826171696186, -0.12412025779485703, 0.018545160070061684, 0.04816563054919243, -0.04114297032356262, -0.02052842266857624, 0.123965322971344, -0.14869970083236694, 0.17509375512599945, -0.05057443305850029, -0.01046690158545971, 0.07116741687059402, -0.01027715764939785, -0.01254033762961626, -0.07660669088363647, 0.10155359655618668, -0.2288699746131897, 0.2338864952325821, 0.2771592140197754, -0.0029745649080723524, 0.15172332525253296, 0.07742849737405777, 0.057065390050411224, 0.0015481646405532956, 0.09415253251791, -0.19699080288410187, -0.03406874090433121, 0.01248161680996418, -0.009576761163771152, 0.04752383381128311, -0.0042405626736581326], "bbox": [55, 179, 130, 104]}, "2": {"print": [-0.13078740239143372, 0.05699743330478668, 0.0589718222618103, -0.1072985827922821, -0.11302723735570908, -0.0031552023719996214, 0.033165525645017624, -0.1062813550233841, 0.2005702257156372, -0.1361413300037384, 0.16931617259979248, -0.054699208587408066, -0.21879363059997559, 0.03022335283458233, -0.020066550001502037, 0.17841319739818573, -0.22869303822517395, -0.20276740193367004, -0.14039075374603271, -0.08215776085853577, -0.08989905565977097, 0.04933314770460129, -0.01939287781715393, -0.004038335755467415, -0.11520800739526749, -0.28747519850730896, -0.03420889005064964, -0.051934294402599335, 0.10838402062654495, -0.0637214407324791, 0.029490092769265175, 0.12088758498430252, -0.23536428809165955, -0.013444767333567142, 0.0562337227165699, 0.04788058623671532, -0.14770621061325073, -0.09646288305521011, 0.2513740360736847, -0.06407211720943451, -0.18936043977737427, -0.06315891444683075, 0.1016397699713707, 0.2033207267522812, 0.2637816369533539, -0.06604819744825363, -0.038224391639232635, -0.11189572513103485, 0.14019499719142914, -0.37019094824790955, 0.023677010089159012, 0.17967143654823303, 0.031952787190675735, 0.1488112211227417, 0.03313243389129639, -0.08275288343429565, -0.020179321989417076, 0.1846354454755783, -0.1602129191160202, 0.014229552820324898, 0.07779120653867722, -0.09080684930086136, -0.0685044527053833, -0.041470710188150406, 0.21089725196361542, 0.07565251737833023, -0.1546817421913147, -0.11907748878002167, 0.16255958378314972, -0.1308763474225998, -0.022589828819036484, 0.04033413156867027, -0.056725796312093735, -0.16327108442783356, -0.23837986588478088, 0.047994405031204224, 0.3449160158634186, 0.04401777312159538, -0.03072611801326275, 0.09828738123178482, -0.08149770647287369, -0.07519827038049698, -0.0361797958612442, 0.03440305218100548, -0.13723783195018768, -0.11258047819137573, 0.012476296164095402, 0.10378436744213104, 0.23691529035568237, -0.044350843876600266, 0.04065922275185585, 0.19867028295993805, -0.03780180215835571, -0.05688966438174248, -0.005433179438114166, 0.12115970999002457, -0.05887814238667488, -0.03966984152793884, -0.12887559831142426, 0.004306728020310402, 0.07863118499517441, -0.04490264877676964, 0.018101640045642853, 0.1560482680797577, -0.16682109236717224, 0.17697609961032867, -0.019891230389475822, -0.0015683422097936273, 0.08109551668167114, -0.0920928567647934, -0.0395466610789299, -0.056501008570194244, 0.11505547165870667, -0.2343451827764511, 0.20471294224262238, 0.10457388311624527, -0.02879505604505539, 0.13459616899490356, 0.05213336646556854, 0.029725395143032074, 0.02558990754187107, 0.0037445526104420424, -0.14207430183887482, -0.06088924780488014, 0.12535886466503143, 0.049325697124004364, -0.020970076322555542, 0.030013367533683777], "bbox": [96, 404, 186, 315]}, "3": {"print": [-0.02070416882634163, -0.011537429876625538, -0.02721215970814228, 0.019968967884778976, -0.06270445138216019, -0.0708947628736496, -0.0219064150005579, -0.0767543613910675, 0.05427514389157295, -0.10872095823287964, 0.15872569382190704, -0.08593966066837311, -0.20005598664283752, -0.05580668896436691, 0.0587969645857811, 0.10683813691139221, -0.11605394631624222, 0.0005828288267366588, -0.1585129201412201, -0.09462446719408035, -0.03234988823533058, 0.07510856539011002, -0.0013528293929994106, 0.009748931974172592, 0.012261715717613697, -0.31037867069244385, -0.032887425273656845, -0.0858936607837677, 0.06960128992795944, -0.003651997772976756, 0.07042116671800613, 0.10139604657888412, -0.1907023936510086, -0.0924278125166893, 0.05000436678528786, 0.009696352295577526, -0.1401595175266266, -0.04757005348801613, 0.2196255773305893, 0.0395989827811718, -0.1739826649427414, -0.0068080732598900795, 0.0013396571157500148, 0.25100064277648926, 0.28414952754974365, -0.012823651544749737, -0.052673421800136566, -0.006386162713170052, 0.14775587618350983, -0.32073774933815, -0.0017836869228631258, 0.13808147609233856, 0.08920954912900925, 0.04210071265697479, 0.12756936252117157, -0.07702484726905823, 0.0033401534892618656, 0.12411274015903473, -0.19363150000572205, 0.016940826550126076, 0.04360027611255646, -0.07644880563020706, -0.029129059985280037, -0.07946601510047913, 0.14764323830604553, 0.08276494592428207, -0.1033242717385292, -0.09326183050870895, 0.10649038106203079, -0.19995753467082977, -0.04657309129834175, 0.03798101097345352, -0.07647096365690231, -0.18073052167892456, -0.324674129486084, 0.05716531723737717, 0.26705917716026306, 0.15291914343833923, -0.1357075423002243, 0.0039789411239326, 0.028095446527004242, -0.02166990377008915, 0.1146150678396225, 0.04411555081605911, -0.04230969771742821, -0.10104762017726898, 0.02721075899899006, -0.02874230220913887, 0.11648387461900711, -0.03161235153675079, -0.08653569966554642, 0.20742374658584595, -0.11835654079914093, -0.07438044995069504, 0.045073483139276505, 0.0016241512494161725, -0.09453078359365463, -0.09108607470989227, -0.16421641409397125, -0.06438042223453522, 0.11672285944223404, -0.15238063037395477, 0.05468588322401047, 0.12799441814422607, -0.17506642639636993, 0.175753653049469, -0.10463547706604004, 0.01860506646335125, 0.07388805598020554, -0.06423480808734894, -0.132423534989357, 0.07956398278474808, 0.17072373628616333, -0.1362118273973465, 0.25558948516845703, 0.09550351649522781, -0.03754504770040512, 0.10693741589784622, 0.10106996446847916, 0.02027699165046215, -0.07662919908761978, 0.021443696692585945, -0.1665932834148407, -0.15738248825073242, 0.03553364798426628, 0.08244133740663528, 0.039142873138189316, -0.049538686871528625], "bbox": [140, 294, 247, 187]}}';

      $fileName1 = 'one_datafile.json';
      $fileName2 = 'two_datafile.json';

      File::put(public_path('storage/json/'.$fileName1),$data1);
      File::put(public_path('storage/json/'.$fileName2),$data2);

      $similarity = compareFaces(storage_path().'/app/public/json/'.$fileName1, storage_path().'/app/public/json/'.$fileName2);
      return $similarity;

    }

    public function getStamp($id)
    {
        $report = Stamp::find($id);
        return $report->MyImage();

        // return $report;
    }

    public function getStamps()
    {
        $report = Stamp::
        has('suspect')
        // ->distinct('suspect_id')
        // ->groupBy('suspect_id')
        ->get();

        $report = $report->unique('suspect_id');
        return $report;

        // return $report;
    }

    public function getReport($id)
    {
        $report = Report::find($id);
        $report->images = $report->images();
        return $report;
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
        $areas = Area::all();
        return view('report.reports.edit', compact('report', 'areas'));
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
			'type' => 'required',
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

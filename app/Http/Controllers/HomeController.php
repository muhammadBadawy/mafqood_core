<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Report;
use App\Stamp;
use App\Suspect;
use App\User;
use App\City;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $now = Carbon::now();
        // $after = $now->copy()->addDays(10);
        $before = $now->copy()->subDays(10);

        $reports = Report::
          whereBetween('created_at', array($before, $now))
          ->groupBy('date')
          ->get(array(
              DB::raw('Date(created_at) as date'),
              DB::raw('COUNT(*) as "count"')
          ));
          // ->get();
        // return $reports;

        $doughnut = City::
            with('reports')
          ->get();

          $doughnut_data = [];
          foreach ($doughnut as $key => $value) {
            $doughnut_data[] = ['name' => $value->name,'id' => $value->id,'count' => $value->reports->count()];
          }

          $reports_number = Report::all()->count();
          $stamps_number = Stamp::all()->count();
          $suspects_number = Suspect::all()->count();
          $admins_number = User::all()->count();

        return view('home', compact('reports', 'doughnut_data', 'reports_number', 'stamps_number', 'suspects_number', 'admins_number'));
    }
}

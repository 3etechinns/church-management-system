<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Event;
use DB;

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
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = \Auth::user();
        $c_types = \App\CollectionsType::getTypes();
         $eventsall =  \App\Announcement::leftjoin('users',"announcements.branchcode", '=','users.branchcode')->where('announcements.branchcode', $user->branchcode)->orWhere('announcements.branch_id', $user->branchcode)->orderBy('announcements.id', 'desc')->get();
        $members = \App\Member::where('branch_id', $user->branchcode)->get();
        $events = Event::where('branch_id', $user->branchcode)->orderBy('date', 'asc')->get();
        $options = DB::table('head_office_options')->where('HOID',1)->first();
        $num_members = $user->isAdmin() ? DB::table('members')->count() : DB::table('members')->where('branch_id', \Auth::user()->branchcode)->count();
        $num_pastors = $user->isAdmin() ? DB::table('members')->where('position', 'pastor')->orWhere('position', 'senior pastor')->count() : DB::table('members')->where('position', 'pastor')->orWhere('position', 'senior pastor')->where('branch_id', \Auth::user()->branchcode)->count();
        $num_workers = $user->isAdmin() ? DB::table('members')->where('position', 'worker')->count() : DB::table('members')->where('position', 'worker')->where('branch_id', \Auth::user()->branchcode)->count();
        $total = ['workers' => $num_workers, 'pastors' => $num_pastors, 'members' => $num_members];
        $currency = \App\Options::getOneBranchOption('currency', \Auth::user());
        // $currency = \App\Options::where('name', 'currency')->first();
        $currency = DB::table('country')->where('currency_symbol', isset($currency->value) ? $currency->value : '₦')->first();
        //$events = Event::all();
        return view('dashboard.index', compact('events','options','total','members', 'eventsall', 'c_types', 'currency'));
    }

    public function gallery()
    {
      return view('gallery.gallery');
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Models\Announcement;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserAnnouncementController extends Controller
{
    public function index(Request $request)
    {
        // wrong
        $filter = $request->query('filter', 'all');
        $searchfilter = $request->input('search');
        $query = Announcement ::query();
        if ($filter === 'past') {
            $query->where('date', '<', $now->toDateString())
                  ->orWhere(function ($query) use ($now) {
                      $query->where('date', '=', $now->toDateString())
                            ->where('time', '<', $now->toTimeString());
                  });
        } elseif ($filter === 'upcoming') {
            $query->where('date', '>', $now->toDateString())
                  ->orWhere(function ($query) use ($now) {
                      $query->where('date', '=', $now->toDateString())
                            ->where('time', '>=', $now->toTimeString());
                  });
        }
        $perPage = $request->input('per_page', 3); 

        $announcements = $query->orderBy('date', 'asc')->orderBy('time', 'asc')->paginate($perPage);

        return response()->json(['announcements' => $announcements]);
        // $announcements = announcements::all();
        // $announcements = $query->get();
        // $permissions = Permission::all();
        // $announcements = $query->paginate(5);
        // $perPage = $request->input('per_page', 3); 

        // $announcements->appends(['searchfilter' => $searchfilter, 'filter' => $filter]);

        // return view('userside.announcement.index', [
        //     'announcements' => $announcements,
        //     'filter' => $filter,
        //     'searchfilter' => $searchfilter,
        // ]);

        // $announcements = announcements::all();
        // return view('userside.announcement.index',['announcements' => $announcements]);
    }
    public function show($id)
    {
        $announcement = Announcement::findOrFail($id);

        // $announcement->update(['status' => true]);

        if ($announcement->status === 'N') {
            $announcement->status = 'S';
            $announcement->save();
        }

        // return view('userside.announcement.view', compact('announcement'));
        return response()->json(['announcement' => $announcement]);

    }

}

<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;
use App\Models\Announcement;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AnnouncementController extends Controller
{
    /**
     * List of all announcements.
     */
    public function index(Request $request)
    {
        $filter = $request->input('filter', 'all');
     
        $now = Carbon::now();
// dd($now);
    // Query announcements based on the filter
    $query = Announcement::query();

    if ($filter === 'past' || $filter === 'p' || $filter === 'P'  || $filter === 'Past' || $filter === 'PAST') {
        $query->where(function ($query) use ($now) {
            $query->where('date', '<', $now->toDateString())
                  ->orWhere(function ($query) use ($now) {
                      $query->where('date', '=', $now->toDateString())
                            ->where('time', '<', $now->toTimeString());
                  });
        });
    } elseif ($filter === 'upcoming' || $filter === 'u' || $filter === 'future' || $filter === 'upcome' || $filter === 'U') {
        $query->where('date', '>', $now->toDateString())
              ->orWhere(function ($query) use ($now) {
                  $query->where('date', '=', $now->toDateString())
                        ->where('time', '>=', $now->toTimeString());
              });
    }else {
        return response()->json(['error' => 'Invalid filter parameter'], 400);
    }

    $perPage = $request->input('per_page', 2); 

    $announcements = $query->orderBy('date', 'asc')->orderBy('time', 'asc')->paginate($perPage);

    
        // Return JSON response with announcements data
        return response()->json(['announcements' => $announcements]);
        // $announcements = announcements::all();
        // return response()->json($announcements);
    }

     /**
     * Create announcements.
     */
    // public function create()
    // {
    //     return view('admin.announcements.create');
    // }

     /**
     * Store announcement in database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|max:64',
            'date' => 'required|date',
            'time' => 'required',
        ]);
        $announcement = Announcement::create($request->only(['message', 'date', 'time']));

        return response()->json([
            'message' => 'Announcement created successfully',
            'announcement' => $announcement,
        ]);

        // return redirect()->route('announcements-index')->with('success', 'Announcement created successfully!');
    }

     /**
     * Open announcements form.
     */
    // public function edit($id)
    // {
    //     $announcement = announcements::findOrFail($id);

    //     // return view('admin.announcements.edit', compact('announcement'));

        
    // }

    public function view($id){
        $announcement = Announcement::findOrFail($id);
        if(!$announcement){
            return response()->json([
                'status' => 'error',
            ],401);
        }
        return response()->json([
            'announcement' => $announcement,
        ],200);
    }
     /**
     * Update announcements.
     */
    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'message' => 'required|max:64',
    //         'date' => 'required|date',
    //         'time' => 'required',
    //     ]);
    //     // past announcement can't be update validation check is missing
    //     $announcement = announcements::findOrFail($id);

    //     $announcement->update($request->only(['message', 'date', 'time']));

    //     return response()->json([

    //         'message' => 'Announcement updated successfully.',
    //         'announcement' => $announcement,
    //     ],200);

    //     // return redirect()->route('announcements-index')->with('success', 'Announcement updated successfully!');
    // }
    public function update(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|max:64',
            'date' => 'required|date',
            'time' => 'required',
        ]);
    
        $announcement = Announcement::findOrFail($id);
    
        // Check if the provided date and time are in the future
        $dateTime = $request->input('date') . ' ' . $request->input('time');
        if (strtotime($dateTime) <= time()) {
            return response()->json([
                'error' => 'You cannot update past announcements.',
            ], 422); // Use appropriate HTTP status code for validation error
        }
    
        $announcement->update($request->only(['message', 'date', 'time']));
    
        return response()->json([
            'message' => 'Announcement updated successfully.',
            'announcement' => $announcement,
        ], 200);
    }
    
     /**
     * Delete announcements.
     */
    public function destroy(Request $request,$id)
    {
        // validation missing
        // past announcement can't be delete validation missing
        // softdelete applied on modal but never used
        // $announcement = Announcement ::findOrFail($id);

        // $announcementDateTime = "{$announcement->date} {$announcement->time}";

        // // Compare the announcement datetime with the current datetime
        // if (strtotime($announcementDateTime) <= strtotime(now())) {
        //     // If announcement datetime is in the past or present, return error
        //     return response()->json([
        //         'error' => 'Cannot delete past or current announcements.',
        //     ], 422);
        // }

        // $announcement->delete();

        // return response()->json([
        //     'message' => 'Announcement delete successfully.',
        //     'announcement' => $announcement,
        // ],200);

        // return redirect()->route('announcements-index')->with('success', 'Announcement deleted successfully!');

        $validator = Validator::make($request->all(), [
            'delete_type' => 'required|in:soft,hard',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid delete_type parameter.'], 422);
        }
    
        // Find the announcement by ID
        $announcement = Announcement::findOrFail($id);
    
        // Check if announcement datetime is in the past or current
        $announcementDateTime = "{$announcement->date} {$announcement->time}";
        if (strtotime($announcementDateTime) <= strtotime(now())) {
            return response()->json(['error' => 'Cannot delete past or current announcements.'], 422);
        }
    
        // Determine the delete type based on the request payload
        $deleteType = $request->input('delete_type');
    
        // Perform the appropriate delete operation
        if ($deleteType === 'soft') {
            $announcement->delete(); // Soft delete (mark as deleted)
            $message = 'Announcement soft deleted.';
        } elseif ($deleteType === 'hard') {
            $announcement->forceDelete(); // Hard delete (permanently remove)
            $message = 'Announcement permanently deleted.';
        }
    
        // Return response indicating the result of the delete operation
        return response()->json(['message' => $message, 'announcement' => $announcement], 200);
    }


}

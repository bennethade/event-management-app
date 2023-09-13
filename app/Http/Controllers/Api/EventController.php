<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // return Event::all();
        return EventResource::collection(Event::with('user')->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        $event = Event::create([

            ...$request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|',
                'start_time' => 'required|date',
                'end_time' => 'required|date|after:start_time'
            ]),

            'user_id' => 1,
    
        ]);

        // return $event;
        return new EventResource($event);

    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        $event->load('user', 'attendees');

        // return $event;
        return new EventResource($event);
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, string $id)
    public function update(Request $request, Event $event) //THIS IS CALLED ROUTE MODEL BINDING (USING THE MODEL NAME AND THE VARIABLE, INSTEAD OF THE ID)
    {
        $event->update(
                $request->validate([
                'name' => 'sometimes|string|max:255',
                'description' => 'nullable|string|',
                'start_time' => 'sometimes|date',
                'end_time' => 'sometimes|date|after:start_time'
            ])
        );

        // return $event;
        return new EventResource($event);
       
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        $event->delete();

         //YOU CAN USE THIS CODE FOR THE RETURN 
        // return response()->json([
        //     'message' => 'Event Deleted Successfully!',
        // ]);


        //OR YOU CAN USE THE RESPONSE CODE
        return response(status: 204);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;

class EventController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except('index','show');

        $this->middleware('throttle:60,1')//This is 60 requests per minute
            ->only(['store','update', 'destroy']); //The only([]) makes it applied to the specified functions

        $this->authorizeResource(Event::class, 'event'); //THIS WAS ADDED FOR THE POLICIES TO WORK
    }



    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Event::query();
        $relations = ['user', 'attendees', 'attendee.user'];

        foreach($relations as $relation){
            $query->when(
                $this->shouldIncludeRelation($relation),
                fn($q) => $q->with($relation)
            );
        }

        // return Event::all();
        // return EventResource::collection(Event::with('user')->paginate());
        return EventResource::collection($query->latest()->paginate());
    }


    protected function shouldIncludeRelation(string $relation): bool
    {
        $include = request()->query('include');

        if(!$include){
            return false;
        }

        $relations = array_map('trim',explode(',', $include));

        // dd($relations);

        return in_array($relation, $relations);
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

            'user_id' => $request->user()->id
    
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

        $this->authorize('update-event', $event); //THIS IS CODED FIRST IN 'AUTHSERVICEPROVIDER.PHP' FILE USING GATES

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

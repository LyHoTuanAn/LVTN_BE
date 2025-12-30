<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoomRequest;
use App\Http\Requests\Admin\UpdateRoomRequest;
use App\Models\Cinema;
use App\Models\RoomType;
use App\Services\Cinema\RoomService;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    protected RoomService $roomService;

    public function __construct(RoomService $roomService)
    {
        $this->roomService = $roomService;
    }

    /**
     * Display a listing of rooms
     */
    public function index(Request $request)
    {
        $rooms = $this->roomService->getAllRooms($request->all());
        $cinemas = Cinema::orderBy('name')->get();
        $roomTypes = RoomType::active()->orderBy('name')->get();

        return view('admin.rooms.index', compact('rooms', 'cinemas', 'roomTypes'));
    }

    /**
     * Show the form for creating a new room
     */
    public function create(Request $request)
    {
        $cinemas = Cinema::orderBy('name')->get();
        $roomTypes = RoomType::active()->orderBy('name')->get();
        $selectedCinemaId = $request->query('cinema_id');

        return view('admin.rooms.create', compact('cinemas', 'roomTypes', 'selectedCinemaId'));
    }

    /**
     * Store a newly created room in storage
     */
    public function store(StoreRoomRequest $request)
    {
        try {
            $data = $request->validated();

            $room = $this->roomService->createRoom($data);

            return redirect()
                ->route('admin.rooms.show', $room->id)
                ->with('success', __('Room created successfully'));
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => __('Failed to create room: :message', ['message' => $e->getMessage()])])
                ->withInput();
        }
    }

    /**
     * Display the specified room
     */
    public function show(int $id)
    {
        $room = $this->roomService->getRoomById($id);

        if (!$room) {
            abort(404, __('Room not found'));
        }

        return view('admin.rooms.show', compact('room'));
    }

    /**
     * Show the form for editing the specified room
     */
    public function edit(int $id)
    {
        $room = $this->roomService->getRoomById($id);

        if (!$room) {
            abort(404, __('Room not found'));
        }

        $cinemas = Cinema::orderBy('name')->get();
        $roomTypes = RoomType::active()->orderBy('name')->get();

        return view('admin.rooms.edit', compact('room', 'cinemas', 'roomTypes'));
    }

    /**
     * Update the specified room in storage
     */
    public function update(UpdateRoomRequest $request, int $id)
    {
        try {
            $room = $this->roomService->getRoomById($id);

            if (!$room) {
                abort(404, __('Room not found'));
            }

            $data = $request->validated();
            $this->roomService->updateRoom($id, $data);

            return redirect()
                ->route('admin.rooms.show', $id)
                ->with('success', __('Room updated successfully'));
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => __('Failed to update room: :message', ['message' => $e->getMessage()])])
                ->withInput();
        }
    }

    /**
     * Remove the specified room from storage
     */
    public function destroy(int $id)
    {
        try {
            $room = $this->roomService->getRoomById($id);

            if (!$room) {
                abort(404, __('Room not found'));
            }

            // Check if room has showtimes
            if ($room->showtimes->count() > 0) {
                return back()->withErrors(['error' => __('Cannot delete room with existing showtimes')]);
            }

            $this->roomService->deleteRoom($id);

            return redirect()
                ->route('admin.rooms.index')
                ->with('success', __('Room deleted successfully'));
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => __('Failed to delete room: :message', ['message' => $e->getMessage()])]);
        }
    }
}

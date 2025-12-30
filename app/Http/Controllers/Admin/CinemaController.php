<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CinemaResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Cinema;
use App\Models\User;
use App\Services\Cinema\CinemaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CinemaController extends Controller
{
    use ApiResponseTrait;

    protected CinemaService $cinemaService;

    public function __construct(CinemaService $cinemaService)
    {
        $this->cinemaService = $cinemaService;
    }

    /**
     * Display a listing of the cinemas.
     */
    public function index(Request $request)
    {
        $cinemas = $this->cinemaService->getAllCinemas($request->all());
        $users = User::whereHas('role', function ($query) {
            $query->whereIn('name', ['admin', 'partner']);
        })->get();

        return view('admin.cinemas.index', compact('cinemas', 'users'));
    }

    /**
     * Show the form for creating a new cinema.
     */
    public function create()
    {
        $users = User::whereHas('role', function ($query) {
            $query->whereIn('name', ['admin', 'partner']);
        })->get();

        return view('admin.cinemas.create', compact('users'));
    }

    /**
     * Store a newly created cinema in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:cinemas,name',
            'location' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'phone' => 'nullable|string|max:20',
            'user_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $this->cinemaService->createCinema($request->all());

        return redirect()->route('admin.cinemas.index')
            ->with('success', __('success.CINEMA_CREATED_SUCCESS'));
    }

    /**
     * Display the specified cinema.
     */
    public function show($id)
    {
        $cinema = $this->cinemaService->getCinemaById($id);

        if (!$cinema) {
            return redirect()->route('admin.cinemas.index')
                ->with('error', __('errors.NOT_FOUND'));
        }

        return view('admin.cinemas.show', compact('cinema'));
    }

    /**
     * Show the form for editing the specified cinema.
     */
    public function edit($id)
    {
        $cinema = $this->cinemaService->getCinemaById($id);

        if (!$cinema) {
            return redirect()->route('admin.cinemas.index')
                ->with('error', __('errors.NOT_FOUND'));
        }

        $users = User::whereHas('role', function ($query) {
            $query->whereIn('name', ['admin', 'partner']);
        })->get();

        return view('admin.cinemas.edit', compact('cinema', 'users'));
    }

    /**
     * Update the specified cinema in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:cinemas,name,' . $id,
            'location' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'phone' => 'nullable|string|max:20',
            'user_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $result = $this->cinemaService->updateCinema($id, $request->all());

        if (!$result) {
            return redirect()->route('admin.cinemas.index')
                ->with('error', __('errors.NOT_FOUND'));
        }

        return redirect()->route('admin.cinemas.index')
            ->with('success', __('success.CINEMA_UPDATED_SUCCESS'));
    }

    /**
     * Remove the specified cinema from storage.
     */
    public function destroy($id)
    {
        $result = $this->cinemaService->deleteCinema($id);

        if (!$result) {
            return redirect()->route('admin.cinemas.index')
                ->with('error', __('errors.NOT_FOUND'));
        }

        return redirect()->route('admin.cinemas.index')
            ->with('success', __('success.CINEMA_DELETED_SUCCESS'));
    }

    /**
     * Show the form for creating multiple cinemas.
     */
    public function createMany()
    {
        $users = User::whereHas('role', function ($query) {
            $query->whereIn('name', ['admin', 'partner']);
        })->get();

        return view('admin.cinemas.create-many', compact('users'));
    }

    /**
     * Store multiple newly created cinemas in storage.
     */
    public function storeMany(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cinemas' => 'required|array|min:1',
            'cinemas.*.name' => 'required|string|max:255',
            'cinemas.*.location' => 'required|string|max:255',
            'cinemas.*.address' => 'required|string|max:500',
            'cinemas.*.phone' => 'nullable|string|max:20',
            'cinemas.*.user_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $cinemasData = $request->input('cinemas');

        // Validate for duplicates and existing names
        $validationErrors = $this->cinemaService->validateManyCinemas($cinemasData);

        if (!empty($validationErrors)) {
            return redirect()->back()
                ->with('validation_errors', $validationErrors)
                ->withInput();
        }

        try {
            $createdCinemas = $this->cinemaService->createManyCinemas($cinemasData);

            return redirect()->route('admin.cinemas.index')
                ->with('success', __('success.CINEMAS_CREATED_SUCCESS', ['count' => $createdCinemas->count()]));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('errors.CINEMA_CREATE_FAILED') . ': ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * API: Store multiple cinemas (for API calls)
     */
    public function apiStoreMany(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cinemas' => 'required|array|min:1',
            'cinemas.*.name' => 'required|string|max:255',
            'cinemas.*.location' => 'required|string|max:255',
            'cinemas.*.address' => 'required|string|max:500',
            'cinemas.*.phone' => 'nullable|string|max:20',
            'cinemas.*.user_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(
                'VALIDATION_ERROR',
                $validator->errors()->toArray(),
                null,
                422
            );
        }

        $cinemasData = $request->input('cinemas');

        // Validate for duplicates and existing names
        $validationErrors = $this->cinemaService->validateManyCinemas($cinemasData);

        if (!empty($validationErrors)) {
            return $this->errorResponse(
                'CINEMA_VALIDATION_FAILED',
                $validationErrors,
                null,
                422
            );
        }

        try {
            $createdCinemas = $this->cinemaService->createManyCinemas($cinemasData);
            $createdCinemas->load(['user', 'rooms']);

            return $this->successResponse(
                'CINEMAS_CREATED_SUCCESS',
                CinemaResource::collection($createdCinemas),
                __('success.CINEMAS_CREATED_SUCCESS', ['count' => $createdCinemas->count()])
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'CINEMA_CREATE_FAILED',
                ['error' => $e->getMessage()],
                null,
                500
            );
        }
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMovieRequest;
use App\Http\Requests\Admin\UpdateMovieRequest;
use App\Services\Movie\MovieService;
use App\Services\Media\MediaService;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    protected MovieService $movieService;
    protected MediaService $mediaService;

    public function __construct(MovieService $movieService, MediaService $mediaService)
    {
        $this->movieService = $movieService;
        $this->mediaService = $mediaService;
    }

    /**
     * Display a listing of movies
     */
    public function index(Request $request)
    {
        $movies = $this->movieService->getAllMovies($request->all());

        return view('admin.movies.index', compact('movies'));
    }

    /**
     * Show the form for creating a new movie
     */
    public function create()
    {
        return view('admin.movies.create');
    }

    /**
     * Store a newly created movie in storage
     */
    public function store(StoreMovieRequest $request)
    {
        try {
            $data = $request->validated();

            // Handle poster upload
            if ($request->hasFile('poster')) {
                $posterFile = $this->mediaService->uploadImage($request->file('poster'), auth()->id());
                $data['poster_id'] = $posterFile->id;
            }

            // Handle trailer upload
            if ($request->hasFile('trailer')) {
                $trailerFile = $this->mediaService->uploadVideo($request->file('trailer'), auth()->id());
                $data['trailer_id'] = $trailerFile->id;
            }

            $movie = $this->movieService->createMovie($data);

            // Handle directors from dynamic form - create directly in director_movie table
            if ($request->has('directors')) {
                foreach ($request->input('directors') as $index => $directorData) {
                    if (!empty($directorData['name'])) {
                        $directorInfo = [
                            'name' => $directorData['name'],
                            'movie_id' => $movie->id,
                        ];
                        
                        // Upload avatar if provided
                        if ($request->hasFile("directors.{$index}.avatar")) {
                            $avatarFile = $this->mediaService->uploadImage($request->file("directors.{$index}.avatar"), auth()->id());
                            $directorInfo['avatar_id'] = $avatarFile->id;
                        }
                        
                        \App\Models\DirectorMovie::create($directorInfo);
                    }
                }
            }

            // Handle actors from dynamic form - create directly in actor_movie table
            if ($request->has('actors')) {
                foreach ($request->input('actors') as $index => $actorData) {
                    if (!empty($actorData['name'])) {
                        $actorInfo = [
                            'name' => $actorData['name'],
                            'movie_id' => $movie->id,
                        ];
                        
                        // Upload avatar if provided
                        if ($request->hasFile("actors.{$index}.avatar")) {
                            $avatarFile = $this->mediaService->uploadImage($request->file("actors.{$index}.avatar"), auth()->id());
                            $actorInfo['avatar_id'] = $avatarFile->id;
                        }
                        
                        \App\Models\ActorMovie::create($actorInfo);
                    }
                }
            }

            return redirect()
                ->route('admin.movies.show', $movie->id)
                ->with('success', __('Movie created successfully'));
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => __('Failed to create movie: :message', ['message' => $e->getMessage()])])
                ->withInput();
        }
    }

    /**
     * Display the specified movie
     */
    public function show(int $id)
    {
        $movie = $this->movieService->getMovieById($id);

        if (!$movie) {
            abort(404, __('Movie not found'));
        }

        $movie->load(['directors.avatar', 'actors.avatar', 'showtimes.room']);

        return view('admin.movies.show', compact('movie'));
    }

    /**
     * Show the form for editing the specified movie
     */
    public function edit(int $id)
    {
        $movie = $this->movieService->getMovieById($id);

        if (!$movie) {
            abort(404, __('Movie not found'));
        }

        $movie->load(['directors.avatar', 'actors.avatar']);

        return view('admin.movies.edit', compact('movie'));
    }

    /**
     * Update the specified movie in storage
     */
    public function update(UpdateMovieRequest $request, int $id)
    {
        try {
            $movie = $this->movieService->getMovieById($id);

            if (!$movie) {
                abort(404, __('Movie not found'));
            }

            $data = $request->validated();

            // Handle poster upload
            if ($request->hasFile('poster')) {
                // Delete old poster if exists
                if ($movie->poster_id) {
                    $this->mediaService->deleteMediaFile($movie->poster_id);
                }
                $posterFile = $this->mediaService->uploadImage($request->file('poster'), auth()->id());
                $data['poster_id'] = $posterFile->id;
            }

            // Handle trailer upload
            if ($request->hasFile('trailer')) {
                // Delete old trailer if exists
                if ($movie->trailer_id) {
                    $this->mediaService->deleteMediaFile($movie->trailer_id);
                }
                $trailerFile = $this->mediaService->uploadVideo($request->file('trailer'), auth()->id());
                $data['trailer_id'] = $trailerFile->id;
            }

            $this->movieService->updateMovie($id, $data);

            // Handle existing directors (update name/avatar)
            if ($request->has('existing_directors')) {
                foreach ($request->input('existing_directors') as $index => $directorData) {
                    if (!empty($directorData['id']) && !empty($directorData['name'])) {
                        $director = \App\Models\DirectorMovie::find($directorData['id']);
                        if ($director && $director->movie_id == $movie->id) {
                            $director->name = $directorData['name'];
                            
                            // Upload new avatar if provided
                            if ($request->hasFile("existing_directors.{$index}.avatar")) {
                                if ($director->avatar_id) {
                                    $this->mediaService->deleteMediaFile($director->avatar_id);
                                }
                                $avatarFile = $this->mediaService->uploadImage($request->file("existing_directors.{$index}.avatar"), auth()->id());
                                $director->avatar_id = $avatarFile->id;
                            }
                            
                            $director->save();
                        }
                    }
                }
            }

            // Handle deleted directors
            if ($request->has('deleted_directors')) {
                foreach ($request->input('deleted_directors') as $directorId) {
                    $director = \App\Models\DirectorMovie::find($directorId);
                    if ($director && $director->movie_id == $movie->id) {
                        if ($director->avatar_id) {
                            $this->mediaService->deleteMediaFile($director->avatar_id);
                        }
                        $director->delete();
                    }
                }
            }

            // Handle new directors
            if ($request->has('directors')) {
                foreach ($request->input('directors') as $index => $directorData) {
                    if (!empty($directorData['name'])) {
                        $directorInfo = [
                            'name' => $directorData['name'],
                            'movie_id' => $movie->id,
                        ];
                        
                        if ($request->hasFile("directors.{$index}.avatar")) {
                            $avatarFile = $this->mediaService->uploadImage($request->file("directors.{$index}.avatar"), auth()->id());
                            $directorInfo['avatar_id'] = $avatarFile->id;
                        }
                        
                        \App\Models\DirectorMovie::create($directorInfo);
                    }
                }
            }

            // Handle existing actors (update name/avatar)
            if ($request->has('existing_actors')) {
                foreach ($request->input('existing_actors') as $index => $actorData) {
                    if (!empty($actorData['id']) && !empty($actorData['name'])) {
                        $actor = \App\Models\ActorMovie::find($actorData['id']);
                        if ($actor && $actor->movie_id == $movie->id) {
                            $actor->name = $actorData['name'];
                            
                            // Upload new avatar if provided
                            if ($request->hasFile("existing_actors.{$index}.avatar")) {
                                if ($actor->avatar_id) {
                                    $this->mediaService->deleteMediaFile($actor->avatar_id);
                                }
                                $avatarFile = $this->mediaService->uploadImage($request->file("existing_actors.{$index}.avatar"), auth()->id());
                                $actor->avatar_id = $avatarFile->id;
                            }
                            
                            $actor->save();
                        }
                    }
                }
            }

            // Handle deleted actors
            if ($request->has('deleted_actors')) {
                foreach ($request->input('deleted_actors') as $actorId) {
                    $actor = \App\Models\ActorMovie::find($actorId);
                    if ($actor && $actor->movie_id == $movie->id) {
                        if ($actor->avatar_id) {
                            $this->mediaService->deleteMediaFile($actor->avatar_id);
                        }
                        $actor->delete();
                    }
                }
            }

            // Handle new actors
            if ($request->has('actors')) {
                foreach ($request->input('actors') as $index => $actorData) {
                    if (!empty($actorData['name'])) {
                        $actorInfo = [
                            'name' => $actorData['name'],
                            'movie_id' => $movie->id,
                        ];
                        
                        if ($request->hasFile("actors.{$index}.avatar")) {
                            $avatarFile = $this->mediaService->uploadImage($request->file("actors.{$index}.avatar"), auth()->id());
                            $actorInfo['avatar_id'] = $avatarFile->id;
                        }
                        
                        \App\Models\ActorMovie::create($actorInfo);
                    }
                }
            }

            return redirect()
                ->route('admin.movies.show', $id)
                ->with('success', __('Movie updated successfully'));
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => __('Failed to update movie: :message', ['message' => $e->getMessage()])])
                ->withInput();
        }
    }

    /**
     * Remove the specified movie from storage
     */
    public function destroy(int $id)
    {
        try {
            $movie = $this->movieService->getMovieById($id);

            if (!$movie) {
                abort(404, __('Movie not found'));
            }

            // Check if movie has showtimes
            if ($movie->showtimes->count() > 0) {
                return back()->withErrors(['error' => __('Cannot delete movie with existing showtimes')]);
            }

            $this->movieService->deleteMovie($id);

            return redirect()
                ->route('admin.movies.index')
                ->with('success', __('Movie deleted successfully'));
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => __('Failed to delete movie: :message', ['message' => $e->getMessage()])]);
        }
    }
}

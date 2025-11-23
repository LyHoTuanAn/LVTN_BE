<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Services\User\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $users = $this->userService->getAllUsers($request->all());

        return view('admin.users.index', compact('users'));
    }

    /**
     * Display the specified user
     */
    public function show(int $id)
    {
        $user = $this->userService->getUserById($id);

        if (!$user) {
            abort(404, __('User not found'));
        }

        $roles = $this->userService->getAllRoles();

        return view('admin.users.show', compact('user', 'roles'));
    }

    /**
     * Update the specified user
     */
    public function update(UpdateUserRequest $request, int $id)
    {
        try {
            $data = $request->validated();
            $avatarFile = $request->file('avatar');

            $user = $this->userService->updateUser($id, $data, $avatarFile);

            return redirect()
                ->route('admin.users.show', $id)
                ->with('success', __('User updated successfully'));
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => __('Failed to update user: :message', ['message' => $e->getMessage()])])
                ->withInput();
        }
    }

    /**
     * Verify user email
     */
    public function verifyEmail(Request $request, int $id)
    {
        try {
            $user = $this->userService->verifyUserEmail($id);

            return redirect()
                ->route('admin.users.show', $id)
                ->with('success', __('User email verified successfully'));
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => __('Failed to verify email: :message', ['message' => $e->getMessage()])]);
        }
    }
}


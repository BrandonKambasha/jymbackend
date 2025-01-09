<?php
namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    /**
     * Display a listing of all groups.
     */
    public function index()
    {
        return response()->json(Group::all());
    }

    /**
     * Store a newly created group in the database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'visibility' => 'required|in:public,private',
            'duration_days' => 'required|integer|min:1',
            'contribution' => 'required|numeric|min:0',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048' // Allow image upload

        ]);

        $iconPath = null;

        if ($request->hasFile('icon')) {
            $iconFile = $request->file('icon');
            $iconName = time() . '_' . $iconFile->getClientOriginalName();
            $iconFile->move(public_path('groupIcons'), $iconName);
            $iconPath = 'groupIcons/' . $iconName; // Store relative path
        }

        $inviteCode = null;
            if ($request->visibility === 'private') {
                $inviteCode = strtoupper(substr(md5(uniqid()), 0, 8)); // Generate unique 8-character invite code
            }

        $group = Group::create([
            'name' => $request->name,
            'description' => $request->description,
            'visibility' => $request->visibility,
            'owner_id' => Auth::id(), // The currently logged-in user is the owner
            'duration_days' => $request->duration_days,
            'contribution' => $request->contribution,
            'icon' => $iconPath, // Store icon path

        ]);

        return response()->json($group, 201);
    }

    /**
     * Display the specified group.
     */
    public function show(string $id)
    {
        $group = Group::with('users')->findOrFail($id);
        return response()->json($group);
    }

    /**
     * Update the specified group.
     */
    public function update(Request $request, string $id)
    {
        $group = Group::findOrFail($id);

        // Ensure only the owner can update the group
        if ($group->owner_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'visibility' => 'required|in:public,private',
            'duration_days' => 'integer|min:1',
            'contribution' => 'numeric|min:0',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048' // Allow image upload

        ]);

        if ($request->hasFile('icon')) {
            // Delete the old icon if it exists
            if ($group->icon && File::exists(public_path($group->icon))) {
                File::delete(public_path($group->icon));
            }
    
            // Store new icon
            $iconFile = $request->file('icon');
            $iconName = time() . '_' . $iconFile->getClientOriginalName();
            $iconFile->move(public_path('groupIcons'), $iconName);
            $group->icon = 'groupIcons/' . $iconName; // Store relative path
        }
        
        $group->update($request->all());

        return response()->json($group);
    }

    /**
     * Remove the specified group.
     */
    public function destroy(string $id)
    {
        $group = Group::findOrFail($id);

        // Ensure only the owner can delete the group
        if ($group->owner_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $group->delete();

        return response()->json(['message' => 'Group deleted successfully']);
    }

    /**
     * Join a group.
     */
    public function joinGroup(Request $request)
    {
        $request->validate([
            'group_id' => 'required|exists:groups,id',
            'invite_code' => 'nullable|string',
        ]);
    
        $group = Group::findOrFail($request->group_id);
    
        // If the group is private, require an invite code
        if ($group->visibility === 'private') {
            if (!$request->invite_code || $group->invite_code !== $request->invite_code) {
                return response()->json(['error' => 'Invalid invite code'], 403);
            }
        }
    
        // Add the user to the group
        $user = auth()->user();
        $user->groups()->attach($group->id);
    
        return response()->json(['message' => 'Joined group successfully']);
    }

    /**
     * Get all groups of the currently authenticated user.
     */
    public function userGroups()
    {
        $user = Auth::user();
        return response()->json($user->groups);
    }

    public function availableGroups()
    {
        $groups = Group::where('visibility', 'public')->withCount('users')->get();
        return response()->json($groups);
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Friend;
use App\Models\Post;
use App\Models\PostTag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MainController extends Controller
{
    public function formatCreatedAt($createdAt){
        $createdAt = Carbon::parse($createdAt);
        $now = Carbon::now();

        $diffInSeconds = round($createdAt->diffInSeconds($now));
        $diffInMinutes = round($createdAt->diffInMinutes($now));
        $diffInHours = round($createdAt->diffInHours($now));
        $diffInDays = round($createdAt->diffInDays($now));
        $diffInWeeks = round($createdAt->diffInWeeks($now));

        if ($diffInSeconds < 60) {
            return $diffInSeconds . ' seconds ago';
        } elseif ($diffInMinutes < 60) {
            return $diffInMinutes . ' minutes ago';
        } elseif ($diffInHours < 24) {
            return $diffInHours . ' hours ago';
        } elseif ($diffInDays < 7) {
            return $diffInDays . ' days ago';
        } else {
            return $diffInWeeks . ' weeks ago';
        }
    }

    public function getFriendData(){
        $user = Auth::user();
        $friends = $user->friends;
        $youMightKnow = $user->youMightKnow();
        // $youMightKnow = User::all();
        return [$friends, $youMightKnow];
    }


    public function index()
    {
        $user = Auth::user();
        [$friends, $youMightKnow] = $this->getFriendData();
        $userPosts = $user->posts;
        $friendsIds = $user->friends->pluck('id')->toArray();
        $openFriendsPosts = Post::whereIn('user_id', $friendsIds)
                                ->where('is_closed_friend', false)
                                ->get();
        $isCloseFriendOfIds = $user->isCloseFriendOf->pluck('id')->toArray();
        $closedFriendsPosts = Post::whereIn('user_id', $isCloseFriendOfIds)
                                ->where('is_closed_friend', true)
                                ->get();
        $friendsPosts = $openFriendsPosts->merge($closedFriendsPosts);
        $homePosts = $userPosts->merge($friendsPosts)->sortByDesc('created_at');

        $closeFriends = $user->closefriends;
        $suggestedFriends = User::whereNotIn('id', $closeFriends->pluck('id'))->take(5)->get();

        $homePosts->transform(function ($post) {
            $post->time = $this->formatCreatedAt($post->created_at);
            return $post;
        });
        return view('dashboard', [
            'posts' => $homePosts,
            'friends' => $friends,
            'youMightKnow' => $youMightKnow,
            'closeFriends' => $closeFriends,
            'suggestedFriends' => $suggestedFriends,
        ]);
    }

    public function store(Request $request)
    {
        try {
            $user = Auth::user();

            $request->validate([
                'pict' => 'required|image|max:2048',
                'tags' => 'array',
                'is_closed_friend' => 'required',
            ]);

            if (!$request->hasFile('pict')) {
                throw new \Exception('No file uploaded');
            }

            $photoFile = $request->file('pict');
            $imageName = date('YmdHis') . '_' . uniqid() . '.' . $photoFile->getClientOriginalExtension();

            // Load image with GD
            $imagePath = $photoFile->getRealPath();
            $sourceImage = imagecreatefromstring(file_get_contents($imagePath));

            if (!$sourceImage) {
                throw new \Exception('Could not create image resource');
            }

            // Get dimensions
            $width = imagesx($sourceImage);
            $height = imagesy($sourceImage);

            // Create mirrored image
            $mirroredImage = imagecreatetruecolor($width, $height);

            // Flip image horizontally
            for ($x = 0; $x < $width; $x++) {
                imagecopy($mirroredImage, $sourceImage, $width - $x - 1, 0, $x, 0, 1, $height);
            }

            // Save the flipped image to the local directory
            $savePath = public_path('user_post/' . $imageName);
            if (!imagejpeg($mirroredImage, $savePath)) {
                throw new \Exception('Failed to save mirrored image');
            }

            // Free up memory
            imagedestroy($sourceImage);
            imagedestroy($mirroredImage);

            DB::beginTransaction();
            $post = Post::create([
                'user_id' => $user->id,
                'pict' => $imageName,
                'caption' => $request->caption ?? null,
                'location' => $request->location ?? null,
                'is_closed_friend' => $request->is_closed_friend,
            ]);

            if ($request->filled('tags')) {
                foreach ($request->tags as $userId) {
                    PostTag::create([
                        'post_id' => $post->id,
                        'user_id' => $userId,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('home')->with('success', 'Successfully added new post');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Failed to create post: ' . $e->getMessage());
        }
    }


    public function searchUsers(Request $request){
        $query = $request->get('query');
        $users = User::where('name', 'like', '%' . $query . '%')->get();
        return response()->json($users);
    }

    public function searchFriends(Request $request){
    $query = $request->get('query');
    $user = Auth::user();

    $filteredFriends = $user->friends->filter(function ($friend) use ($query) {
        return stripos($friend->name, $query) !== false;
    })->values()->toArray();

    $userQuery = User::where('link', 'LIKE', "%{$query}%")
        ->where('id', '!=', $user->id)
        ->first();

    if ($userQuery) {
        $isFriend = $user->friends()->where('friend_id', $userQuery->id)->exists();
        $userQueryResult = [
            [
                'id' => $userQuery->id,
                'name' => $userQuery->name,
                'profile_pics' => $userQuery->profile_pics,
                'type' => $isFriend ? 'old' : 'new'
            ]
        ];
        $combinedResults = array_merge($filteredFriends, $userQueryResult);
    } else {
        $combinedResults = $filteredFriends;
    }
        return response()->json($combinedResults);
    }

    public function follow($friendId){
        try {
            $user = auth()->user();
            $friend = User::find($friendId);
            if (!$friend) {
                throw new \Exception('User not found.');
            }
            $user->friends()->attach($friendId);
            $friend->friends()->attach($user->id);
            return redirect()->route('home')->with('success', 'Successfully followed ' . $friend->name . '.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to follow user: ' . $e->getMessage());
        }
    }

    public function unfollow($friendId){
        try {
            $user = auth()->user();
            $friend = User::find($friendId);
            if (!$friend) {
                throw new \Exception('User not found.');
            }
            $user->friends()->detach($friendId);
            $friend->friends()->detach($user->id);
            return redirect()->route('home')->with('success', 'Successfully unfollowed ' . $friend->name . '.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to unfollow user: ' . $e->getMessage());
        }
    }

    public function gallery(){
        $user = Auth::user();
        $posts = Post::where('user_id', $user->id)->get();
        [$friends, $youMightKnow] = $this->getFriendData();
        return view('history', compact('posts', 'friends', 'youMightKnow'));
    }
}

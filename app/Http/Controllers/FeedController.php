<?php

namespace App\Http\Controllers;

use App\Models\Idea;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    /**
     * Handle the incoming request.
     */
    // ! invokable controller is a simple controller that performs only one action:
    public function __invoke(Request $request)
    {
        $user = auth()->user();

        // ! followings is the method that we have in the user model.
        // ! pluck helps to get the specific collumn that we want:
        $followingIDs = $user->followings()->pluck('user_id');

        // ! whereIn is to show just the ideas of the ones that we followed:
        $ideas = Idea::whereIn('user_id', $followingIDs)->latest();

        if (request()->has('search')) {
            // ? first way of doing search:
            // todo: $ideas = $ideas->where('content', 'like', '%' . request()->get('search') . '%');
            // ? the code abobe with scope that is written in idea controller
            // ! search is coming from scopeSearch, we do not need the scope when we use the method:
            $ideas = $ideas->search(request('search', ''));
        }

        return view('dashboard', [
            'ideas' => $ideas->paginate(3)
        ]);
    }
}

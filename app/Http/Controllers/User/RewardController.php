<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UserReward;
use App\Services\RewardService;
use Illuminate\Support\Facades\Auth;

class RewardController extends Controller
{
    public function index(RewardService $rewardService)
    {
        $user = Auth::user();

        // Evaluate eligible rewards
        $rewardService->evaluateAndAwardRewards($user);

        $directBusiness = $rewardService->getUserDirectBusiness($user);
        $teamBusiness = $rewardService->getUserTeamBusiness($user);

        $directRewardTiers = $rewardService->getDirectBusinessRewardTiers();
        $teamRewardTiers = $rewardService->getTeamBusinessRewardTiers();

        $achievedRewards = UserReward::where('user_id', $user->id)->get();

        return view('user.rewards.index', compact(
            'user',
            'directBusiness',
            'teamBusiness',
            'directRewardTiers',
            'teamRewardTiers',
            'achievedRewards'
        ));
    }
}

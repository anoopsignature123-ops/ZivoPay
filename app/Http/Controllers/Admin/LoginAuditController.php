<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoginAudit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LoginAuditController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'channel' => ['nullable', Rule::in(['mobile_api', 'member_web', 'admin_web'])],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $query = LoginAudit::query()->with('user');

        if (! empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function (Builder $query) use ($search): void {
                $query->where('ip_address', 'like', "%{$search}%")
                    ->orWhere('device_name', 'like', "%{$search}%")
                    ->orWhere('device_model', 'like', "%{$search}%")
                    ->orWhereHas('user', function (Builder $userQuery) use ($search): void {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('mobile', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('referral_code', 'like', "%{$search}%");
                    });

                if (ctype_digit($search)) {
                    $query->orWhere('user_id', (int) $search);
                }
            });
        }

        if (! empty($filters['channel'])) {
            $query->where('channel', $filters['channel']);
        }

        if (! empty($filters['from'])) {
            $query->whereDate('logged_in_at', '>=', $filters['from']);
        }

        if (! empty($filters['to'])) {
            $query->whereDate('logged_in_at', '<=', $filters['to']);
        }

        $logins = (clone $query)->latest('logged_in_at')->paginate(25)->withQueryString();

        return view('admin.reports.login-audit', [
            'logins' => $logins,
            'filters' => $filters,
            'totalLogins' => (clone $query)->count(),
            'todayLogins' => (clone $query)->whereDate('logged_in_at', today())->count(),
            'mobileLogins' => (clone $query)->where('channel', 'mobile_api')->count(),
            'uniqueUsers' => (clone $query)->distinct()->count('user_id'),
        ]);
    }
}

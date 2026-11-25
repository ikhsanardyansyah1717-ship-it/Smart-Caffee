<?php

namespace App\Providers;

use App\Models\Employee;
use App\Models\EmployeeLeaveRequest;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use App\Models\Payment;
use App\Models\ProductReview;
use App\Models\EmployeeSalary;
use App\Models\EmployeeSalaryPayment;
use App\Models\Branch;
use App\Events\QuattroDataChanged;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $publishChange = static function (string $type): void {
            DB::afterCommit(static function () use ($type): void {
                // A lightweight revision endpoint keeps the UI live when a phone
                // cannot reach the local Reverb port through the current tunnel.
                $revision = (string) Str::uuid();
                Cache::store('file')->forever('quattro.live.revision.' . $type, $revision);

                if (config('broadcasting.default') !== 'reverb') {
                    return;
                }

                try {
                    event(new QuattroDataChanged($type, $revision));
                } catch (\Throwable $exception) {
                    report($exception);
                }
            });
        };

        $liveModels = [
            Product::class => 'products',
            ProductReview::class => 'products',
            Order::class => 'orders',
            Payment::class => 'orders',
            User::class => 'owner',
            Employee::class => 'owner',
            EmployeeLeaveRequest::class => 'owner',
            EmployeeSalary::class => 'owner',
            EmployeeSalaryPayment::class => 'owner',
            Expense::class => 'owner',
            Branch::class => 'owner',
        ];

        foreach ($liveModels as $model => $type) {
            $model::saved(static fn () => $publishChange($type));
            $model::deleted(static fn () => $publishChange($type));
        }

        View::composer('layouts.owner', function ($view) {
            if (auth()->user()?->role !== 'owner') {
                $view->with('ownerMenuNotifications', []);
                return;
            }

            $defaultSince = now()->startOfDay();
            $ownerId = auth()->id();
            $seenAt = fn (string $menu) => Cache::store('file')->get('owner.notifications.seen.' . $ownerId . '.' . $menu, $defaultSince);
            $changedSince = static function ($readAt) {
                return static function ($query) use ($readAt): void {
                    $query->where(function ($changes) use ($readAt) {
                        $changes->where('created_at', '>', $readAt)
                            ->orWhere('updated_at', '>', $readAt);
                    });
                };
            };

            $latestValues = static function ($query, string $menu, string $column) use ($changedSince, $seenAt): array {
                return $query->where($changedSince($seenAt($menu)))
                    ->orderByDesc('updated_at')
                    ->limit(4)
                    ->pluck($column)
                    ->filter()
                    ->values()
                    ->all();
            };
            $describe = static function (array $values): string {
                $hasMore = count($values) > 3;
                $values = array_slice($values, 0, 3);
                if (count($values) === 0) {
                    return 'Ada perubahan data';
                }
                if ($hasMore) {
                    $values[] = 'dan lainnya';
                }
                return implode(', ', $values);
            };

            $financeEntries = Expense::where($changedSince($seenAt('finance')))
                ->orderByDesc('updated_at')->limit(4)->get(['description', 'category'])
                ->map(fn ($entry) => $entry->description ?: $entry->category)->filter()->values()->all();
            $leaveEntries = EmployeeLeaveRequest::with('employee:id,name')->where('status', 'Pending')
                ->where($changedSince($seenAt('leaves')))->orderByDesc('updated_at')->limit(4)->get()
                ->map(fn ($leave) => $leave->employee?->name ?: 'Pengajuan cuti')->values()->all();

            $view->with('ownerMenuNotifications', [
                'finance' => Expense::where($changedSince($seenAt('finance')))->count(),
                'customers' => User::where('role', 'customer')
                    ->where($changedSince($seenAt('customers')))->count(),
                'products' => Product::where($changedSince($seenAt('products')))->count(),
                'employees' => Employee::where($changedSince($seenAt('employees')))->count(),
                'leaves' => EmployeeLeaveRequest::where('status', 'Pending')
                    ->where($changedSince($seenAt('leaves')))->count(),
            ]);
            $view->with('ownerMenuNotificationDetails', [
                'finance' => $describe($financeEntries),
                'customers' => $describe($latestValues(User::where('role', 'customer'), 'customers', 'name')),
                'products' => $describe($latestValues(Product::query(), 'products', 'name')),
                'employees' => $describe($latestValues(Employee::query(), 'employees', 'name')),
                'leaves' => $describe($leaveEntries),
            ]);
        });
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $status = strtolower(
            trim(
                (string) $request->query(
                    'status',
                    'all'
                )
            )
        );

        if (
            ! in_array(
                $status,
                ['all', 'unread', 'read'],
                true
            )
        ) {
            $status = 'all';
        }


        $search = mb_substr(
            trim(
                (string) $request->query(
                    'q',
                    ''
                )
            ),
            0,
            120
        );


        $baseQuery = AppNotification::query()
            ->where(
                'recipient_email',
                $request->user()->email
            );


        $totalCount =
            (clone $baseQuery)->count();

        $unreadCount =
            (clone $baseQuery)
                ->where('is_read', false)
                ->count();

        $readCount =
            $totalCount - $unreadCount;


        $listQuery =
            clone $baseQuery;


        if ($search !== '') {

            $needle =
                '%'
                . mb_strtolower($search)
                . '%';


            $listQuery->where(
                function ($query) use ($needle) {

                    $query
                        ->whereRaw(
                            'LOWER(title) LIKE ?',
                            [$needle]
                        )
                        ->orWhereRaw(
                            'LOWER(body) LIKE ?',
                            [$needle]
                        )
                        ->orWhereRaw(
                            "LOWER(COALESCE(module, '')) LIKE ?",
                            [$needle]
                        );
                }
            );
        }


        $notifications =
            $listQuery

                ->when(
                    $status === 'unread',
                    fn ($query) =>
                        $query->where(
                            'is_read',
                            false
                        )
                )

                ->when(
                    $status === 'read',
                    fn ($query) =>
                        $query->where(
                            'is_read',
                            true
                        )
                )

                ->orderByDesc('created_at')

                ->paginate(15)

                ->withQueryString();


        return view(
            'notifications.index',
            compact(
                'notifications',
                'status',
                'totalCount',
                'unreadCount',
                'readCount',
                'search'
            )
        );
    }

    public function open(
        Request $request,
        AppNotification $notification
    ) {
        abort_unless(
            $notification->recipient_email ===
                $request->user()->email,
            403
        );


        if (! $notification->is_read) {
            $notification->update([
                'is_read' => true,
            ]);
        }


        $link = trim(
            (string) $notification->link
        );


        /*
         * Only allow internal application paths.
         * Prevent redirecting users to an external URL.
         */
        if (
            $link === ''
            || ! str_starts_with($link, '/')
            || str_starts_with($link, '//')
        ) {
            return redirect()->route(
                'notifications.index'
            );
        }


        return redirect()->to($link);
    }

    public function markRead(Request $request, AppNotification $notification)
    {
        abort_unless(
            $notification->recipient_email === $request->user()->email,
            403
        );

        $notification->update([
            'is_read' => true,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
            ]);
        }

        return back()->with('status', 'Notification marked as read.');
    }

    public function markAllRead(Request $request)
    {
        AppNotification::where(
            'recipient_email',
            $request->user()->email
        )->update([
            'is_read' => true,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
            ]);
        }

        return back()->with('status', 'All notifications marked as read.');
    }
}
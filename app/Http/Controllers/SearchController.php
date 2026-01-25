<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{

    public function global(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        $limit = $request->get('limit', 20);

        if (empty($query)) {
            return response()->json([
                'message' => 'Search query is required',
            ], 400);
        }

        $results = [
            'accounts' => Account::search($query)->take($limit)->get(),
            'transactions' => Transaction::search($query)->take($limit)->get(),
            'users' => User::search($query)->take($limit)->get(),
        ];

        return response()->json([
            'query' => $query,
            'results' => $results,
            'total_results' => count($results['accounts']) + count($results['transactions']) + count($results['users']),
        ]);
    }


    public function accounts(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        $type = $request->get('type');
        $state = $request->get('state');
        $limit = $request->get('limit', 20);

        if (empty($query)) {
            return response()->json([
                'message' => 'Search query is required',
            ], 400);
        }

        $searchQuery = Account::search($query);


        if ($type) {
            $searchQuery->where('type', $type);
        }

        if ($state) {
            $searchQuery->where('state', $state);
        }

        $results = $searchQuery->take($limit)->get();

        return response()->json([
            'query' => $query,
            'filters' => [
                'type' => $type,
                'state' => $state,
            ],
            'results' => $results,
            'total_results' => $results->count(),
        ]);
    }

    public function transactions(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        $type = $request->get('type');
        $status = $request->get('status');
        $minAmount = $request->get('min_amount');
        $maxAmount = $request->get('max_amount');
        $limit = $request->get('limit', 20);

        if (empty($query)) {
            return response()->json([
                'message' => 'Search query is required',
            ], 400);
        }

        $searchQuery = Transaction::search($query);

        if ($type) {
            $searchQuery->where('type', $type);
        }

        if ($status) {
            $searchQuery->where('status', $status);
        }

        $results = $searchQuery->take($limit)->get();

        if ($minAmount || $maxAmount) {
            $results = $results->filter(function ($transaction) use ($minAmount, $maxAmount) {
                if ($minAmount && $transaction->amount < $minAmount) {
                    return false;
                }
                if ($maxAmount && $transaction->amount > $maxAmount) {
                    return false;
                }

                return true;
            });
        }

        return response()->json([
            'query' => $query,
            'filters' => [
                'type' => $type,
                'status' => $status,
                'min_amount' => $minAmount,
                'max_amount' => $maxAmount,
            ],
            'results' => $results->values(),
            'total_results' => $results->count(),
        ]);
    }

    public function users(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        $role = $request->get('role');
        $limit = $request->get('limit', 20);

        if (empty($query)) {
            return response()->json([
                'message' => 'Search query is required',
            ], 400);
        }

        $searchQuery = User::search($query);

        if ($role) {
            $searchQuery->where('role', $role);
        }

        $results = $searchQuery->take($limit)->get();

        return response()->json([
            'query' => $query,
            'filters' => [
                'role' => $role,
            ],
            'results' => $results,
            'total_results' => $results->count(),
        ]);
    }


    public function suggestions(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        $limit = $request->get('limit', 10);

        if (empty($query)) {
            return response()->json([
                'suggestions' => [],
            ]);
        }

        $suggestions = [];

        $accounts = Account::search($query)->take($limit)->get(['nickname', 'type']);
        foreach ($accounts as $account) {
            $suggestions[] = [
                'type' => 'account',
                'text' => $account->nickname ?? "Account {$account->type}",
                'value' => $account->nickname ?? $account->type,
            ];
        }

        $users = User::search($query)->take($limit)->get(['name', 'email']);
        foreach ($users as $user) {
            $suggestions[] = [
                'type' => 'user',
                'text' => $user->name,
                'value' => $user->email,
            ];
        }

        $transactionTypes = ['deposit', 'withdrawal', 'transfer'];
        $matchingTypes = array_filter($transactionTypes, function ($type) use ($query) {
            return stripos($type, $query) !== false;
        });

        foreach ($matchingTypes as $type) {
            $suggestions[] = [
                'type' => 'transaction_type',
                'text' => ucfirst($type),
                'value' => $type,
            ];
        }

        return response()->json([
            'query' => $query,
            'suggestions' => array_slice($suggestions, 0, $limit),
        ]);
    }
}

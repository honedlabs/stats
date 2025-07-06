<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    /**
     * Show the user resource.
     */
    public function show(User $user): Response
    {
        return Inertia::render('Users/Show', [
            ...$user->stats()->toArray(),
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateContextNameRequest;
use App\Models\Context;
use Illuminate\Http\RedirectResponse;

class ContextController extends Controller
{
    public function update(UpdateContextNameRequest $request, Context $context): RedirectResponse
    {
        $context->name = $request->validated('name');
        $context->save();

        return to_route('users.index')->with('status', __('users.context_updated'));
    }
}

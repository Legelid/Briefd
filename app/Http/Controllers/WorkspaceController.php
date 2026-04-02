<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WorkspaceController extends Controller
{
    public function switch(Request $request): RedirectResponse
    {
        $request->validate(['workspace_id' => 'required|integer']);

        $workspace = $request->user()
            ->workspaces()
            ->findOrFail($request->workspace_id);

        session(['current_workspace_id' => $workspace->id]);

        return redirect()->back();
    }
}

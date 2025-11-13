<?php

namespace App\Http\Requests\Workspace;

use App\Models\Workspace;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SwitchWorkspaceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && $this->workspace();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'workspace_id' => ['required', 'exists:workspaces,id'],
        ];
    }

    /**
     * Get the workspace instance for the given
     * workspace ID that belongs to current user.
     */
    public function workspace(): ?Workspace
    {
        return auth()->user()->workspaces()->find($this->input('workspace_id'));
    }
}

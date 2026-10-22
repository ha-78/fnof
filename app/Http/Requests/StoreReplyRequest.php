<?php

namespace App\Http\Requests;

use App\Models\Thread;
use App\Rules\NoImages;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreReplyRequest extends FormRequest
{
    public function authorize(): bool
    {
        $thread = $this->route('thread');

        return $thread instanceof Thread && $this->user()->can('view', $thread);
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('body')) {
            $this->merge([
                'body' => trim((string) $this->input('body')),
            ]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'max:10000', new NoImages],
        ];
    }

    /**
     * @return array<int, callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $thread = $this->route('thread');

                if ($thread instanceof Thread && $thread->isArchived()) {
                    $validator->errors()->add('body', __('threads.archived_no_replies'));
                }
            },
        ];
    }
}

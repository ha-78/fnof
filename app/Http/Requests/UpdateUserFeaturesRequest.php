<?php

namespace App\Http\Requests;

use App\Enums\Feature;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateUserFeaturesRequest extends FormRequest
{
    public function authorize(): bool
    {
        $subject = $this->route('user');

        return $subject instanceof User
            && $this->user()->can('assignFeatures', $subject);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'features' => ['present', 'array'],
            'features.*' => ['boolean'],
        ];
    }

    /**
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $features = $this->input('features');

                if (! is_array($features)) {
                    return;
                }

                $expected = array_map(
                    fn (Feature $feature): string => $feature->value,
                    Feature::cases(),
                );

                $keys = array_keys($features);

                if (array_diff($expected, $keys) !== [] || array_diff($keys, $expected) !== []) {
                    $validator->errors()->add('features', __('users.features_invalid'));
                }
            },
        ];
    }

    /**
     * @return array<string, bool>
     */
    public function grantedFeatures(): array
    {
        $granted = [];

        foreach (Feature::cases() as $feature) {
            $granted[$feature->value] = $this->boolean('features.'.$feature->value);
        }

        return $granted;
    }
}

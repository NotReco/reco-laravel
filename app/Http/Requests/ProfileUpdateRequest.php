<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'avatar' => ['nullable', 'image', 'max:2048'], // Max 2MB
            'cover_photo' => ['nullable', 'image', 'max:5120'], // Max 5MB
            'bio' => ['nullable', 'string', 'max:500'],
            'location' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'string', 'url', 'max:255'],
            'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
            'movie_quote' => ['nullable', 'string', 'max:255'],
            'active_title_id' => ['nullable', 'exists:user_title_inventory,title_id,user_id,' . $this->user()->id], 
            'active_frame_id' => ['nullable', 'exists:user_frame_inventory,frame_id,user_id,' . $this->user()->id],
            'top_movies' => ['nullable', 'array', 'max:4'],
            'top_movies.*' => ['nullable', 'exists:movies,id'],
        ];
    }
}

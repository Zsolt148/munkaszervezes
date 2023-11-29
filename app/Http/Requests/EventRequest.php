<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules = [];

        $rules['event_from_date'] = ['string', 'sometimes'];
        $rules['event_to_date'] = ['string', 'sometimes'];
        $rules['event_to_time'] = ['string', 'sometimes'];
        $rules['event_from_time'] = ['string', 'sometimes'];
        $rules['publish_date'] = ['string', 'sometimes'];
        $rules['publish_time'] = ['string', 'sometimes'];

        $rules['tags'] = ['sometimes', 'array'];

        foreach (config('app.locales') as $key => $locale) {
            $rules['title.'.$key] = ['string', 'max:255'];
            $rules['excerpt.'.$key] = ['string', 'max:255'];
            $rules['content.'.$key] = ['string'];
            $rules['seo_title.'.$key] = ['string', 'max:255'];
            $rules['seo_description.'.$key] = ['string', 'max:3000'];
            $rules['seo_url.'.$key] = ['string', 'max:255'];
        }

        return $rules;
    }
}

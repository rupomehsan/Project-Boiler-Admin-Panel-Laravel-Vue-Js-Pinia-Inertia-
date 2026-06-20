<?php

namespace Modules\Management\BlogManagement\Blog\Validations;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class DataStoreValidation extends FormRequest
{
    /**
     * Determine if the  is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    /**
     * validateError to make this request.
     */
    public function validateError($data)
    {
        $errorPayload =  $data->getMessages();
        return response(['status' => 'validation_error', 'errors' => $errorPayload], 422);
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->validateError($validator->errors()));
        if ($this->wantsJson() || $this->ajax()) {
            throw new HttpResponseException($this->validateError($validator->errors()));
        }
        parent::failedValidation($validator);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'blog_category_id' => 'required | sometimes',
            'writer_id' => 'required | sometimes',
            'title' => 'required | sometimes',
            'short_description' => 'required | sometimes',
            'content' => 'required | sometimes',
            'reading_time' => 'required | sometimes',
            'average_rating' => 'required | sometimes',
            'publish_date' => 'required | sometimes',
            'scheduled_at' => 'required | sometimes',
            'thumbnail_image' => 'required | sometimes',
            'gallery' => 'required | sometimes',
            'blog_type' => 'required | sometimes',
            'content_format' => 'required | sometimes',
            'external_url' => 'required | sometimes',
            'show_on_top' => 'required | sometimes',
            'allow_comments' => 'required | sometimes',
            'is_featured' => 'required | sometimes',
            'is_published' => 'required | sometimes',
            'video_link' => 'required | sometimes',
            'meta_title' => 'required | sometimes',
            'meta_description' => 'required | sometimes',
            'meta_keywords' => 'required | sometimes',
            'status' => ['sometimes', Rule::in(['active', 'inactive'])],
        ];
    }
}
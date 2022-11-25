<?php

namespace App\Http\Requests\Api\Deals;

use App\Models\Property;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;

class TenantPropertyRule implements Rule
{
    /**
     * @var \Illuminate\Http\Request
     */
    private $request;

    /**
     * Create a new rule instance.
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return Property::where([['tenant_id', tenant()->id], ['id', $this->request->property_id]])->first();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Property does not belong to tenant';
    }
}

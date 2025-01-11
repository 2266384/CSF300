<?php

namespace App\Http\Controllers;

use App\Services\AttributeService;
use Illuminate\Http\Request;

class AttributeController extends Controller
{
    protected AttributeService $attributeService;

    // Inject the AttributeService into this Controller
    public function __construct(AttributeService $attributeService)
    {
        $this->attributeService = $attributeService;
    }

    public function actions() {

        return $this->attributeService->attributesWithActions();

    }

}

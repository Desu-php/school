<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\SupportTicketCategoryResource;
use App\Models\SupportTicketCategory;
use Illuminate\Http\Request;

class SupportTicketCategoryController extends Controller
{
    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $support_ticket_category = SupportTicketCategory::all();

        return SupportTicketCategoryResource::collection($support_ticket_category);
    }
}

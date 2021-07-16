<?php

namespace App\Http\Controllers\Web;

use App\Events\SampleEvent;
use App\Http\Controllers\Controller;
use App\Http\Resources\SupportTicketCategoryResource;
use App\Http\Resources\SupportTicketResource;
use App\Models\SupportTicket;
use App\Models\SupportTicketCategory;
use App\Models\SupportTicketMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupportTicketController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user_id = auth('api')->id();
        $support_tickets = SupportTicket::orderBy('id', 'Desc')->where('user_id', $user_id)->with('support_ticket_category', 'support_ticket_messages');
        $page = $request->input('page') ? : 1;
        $take = $request->input('count') ? : 6;
        $count = $support_tickets->count();

        event(
          new SampleEvent()
        );

        if ($page) {
            $skip = $take * ($page - 1);
            $support_tickets = $support_tickets->take($take)->skip($skip);
        } else {
            $support_tickets = $support_tickets->take($take)->skip(0);
        }

        return response()->json([
            'data' =>  SupportTicketResource::collection($support_tickets->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ], 200);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'message'    => 'required',
            'support_ticket_category_id' => 'required|exists:support_ticket_categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }


        $data['user_id'] = auth('api')->id();
        $data['sender_is_user'] = true;


        $support_ticket = SupportTicket::create($data);
        $support_ticket->support_ticket_messages()->create($data);
        $support_ticket->load('support_ticket_category', 'support_ticket_messages');

        return response()->json([
            'data' => $support_ticket
        ], 200);
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead($id)
    {
        $support_ticket = SupportTicket::findOrFail($id);
        $support_ticket->support_ticket_messages()->where('sender_is_user', false)->update(['is_read' => true]);

        return response()->json([
            'data' => new SupportTicketResource($support_ticket)
        ], 200);
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $support_ticket = SupportTicket::findOrFail($id);

        return response()->json([
            'data' => new SupportTicketResource($support_ticket),
        ], 200);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessage(Request $request, $id)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'message'    => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $support_ticket = SupportTicket::findOrFail($id);
        $data['user_id'] = auth('api')->id();
        $data['sender_is_user'] = true;

        $support_ticket_message =  $support_ticket->support_ticket_messages()->where('sender_is_user', true)->create($data);

        $support_ticket_message->load('user', 'admin');

        return response()->json([
            'data' => $support_ticket_message
        ], 200);
    }


}

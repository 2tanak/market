<?php

namespace Modules\Chat\Model\Messages;

use Carbon\Carbon;
use Modules\Entity\Services\ModelFilter;

class Filter extends ModelFilter
{
    // В твоем Filter.php
    public function filter()
    {
        $request = $this->request;
        $myId = auth()->id();

        // chat_id
        $chat_id = $request->chat_id;

        $this->query->where(function ($q) use ($chat_id) {
            $q->where('chat_id', $chat_id);
            // $q->where('sender_id', $myId)->where('_id', $friendId)
            //     ->orWhere('sender_id', $friendId)->where('receiver_id', $myId);
        });

        if (! empty($request->created_at) && $request->created_at !== '0' && $request->created_at !== 'null') {
            $newm = filter_var($request->newm, FILTER_VALIDATE_BOOLEAN);
            $formattedDate = Carbon::parse($request->created_at)
                ->timezone(config('app.timezone'))
                ->format('Y-m-d H:i:s');

            if ($request->page == 1 && $newm === false) {
                $this->query->where('created_at', '<', $formattedDate);
            }

            if ($request->page == 1 && $newm === true) {
                $this->query->where('created_at', '>', $formattedDate);
            }
        }

        $this->query->orderBy('id', 'desc');
    }
}

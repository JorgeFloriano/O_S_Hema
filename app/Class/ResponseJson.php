<?php

namespace App\Class;

class ResponseJson
{
    public function error($conditions = [], $message)
    {

        // Verify if user has access to create users
        foreach ($conditions as $key => $condition) {
            if (!isset($condition)) {
                return response()->json([
                    'error' => $message,
                    'message' => $message
                ], 200);
            }

            if (!$condition) {
                return response()->json([
                    'error' => $message,
                    'message' => $message
                ], 200);
            }
        }

        return false;
    }
}

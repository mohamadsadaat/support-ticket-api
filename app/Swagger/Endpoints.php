<?php

namespace App\Swagger;

/**
 * @OA\Post(
 *     path="/api/login",
 *     tags={"Auth"},
 *     summary="Login",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email","password"},
 *             @OA\Property(property="email", type="string", format="email"),
 *             @OA\Property(property="password", type="string", format="password")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Login success")
 * )
 *
 * @OA\Post(
 *     path="/api/register",
 *     tags={"Auth"},
 *     summary="Register",
 *     @OA\Response(response=201, description="User registered")
 * )
 *
 * @OA\Get(
 *     path="/api/tickets",
 *     tags={"Tickets"},
 *     security={{"bearerAuth":{}}},
 *     summary="List tickets with filters",
 *     @OA\Parameter(name="status_id", in="query", @OA\Schema(type="integer")),
 *     @OA\Parameter(name="priority_id", in="query", @OA\Schema(type="integer")),
 *     @OA\Parameter(name="category_id", in="query", @OA\Schema(type="integer")),
 *     @OA\Parameter(name="assigned_to", in="query", @OA\Schema(type="integer")),
 *     @OA\Parameter(name="search", in="query", @OA\Schema(type="string")),
 *     @OA\Response(response=200, description="List of tickets")
 * )
 *
 * @OA\Post(
 *     path="/api/tickets",
 *     tags={"Tickets"},
 *     security={{"bearerAuth":{}}},
 *     summary="Create ticket",
 *     @OA\Response(response=201, description="Ticket created")
 * )
 *
 * @OA\Post(
 *     path="/api/tickets/{ticket}/replies",
 *     tags={"Replies"},
 *     security={{"bearerAuth":{}}},
 *     summary="Add reply to ticket",
 *     @OA\Parameter(name="ticket", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=201, description="Reply created")
 * )
 */
class Endpoints
{
}

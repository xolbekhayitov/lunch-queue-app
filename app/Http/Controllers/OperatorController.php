<?php

namespace App\Http\Controllers;

use App\Models\Operator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class OperatorController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/operators",
     *     summary="Foydalanuvchilar ro'yxati",
     *     tags={"Operators"},
     *     @OA\Response(
     *         response=200,
     *         description="Muvaffaqiyatli",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Operator")
     *         )
     *     )
     * )
     */
    public function index()
    {
        // $operators = Cache::remember('operators', 60 * 60, function () {
        //     return Operator::all();
        // });
        // return $operators;
        return Operator::all();
    }

    /**
     * @OA\Post(
     *     path="/api/operators",
     *     tags={ "Operators" },
     *     summary="Create a new operator",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","chat_id", "is_supervisor"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="chat_id", type="integer", example="your telegram chat_id"),
     *             @OA\Property(
     *                 property="is_supervisor",
     *                 type="boolean",
     *                 description="Agar admin bo‘lsa true, oddiy operator bo‘lsa false",
     *                 example="false"
     *             ),
     *         )
     *     ),
     *     @OA\Response(response=201, description="Author created successfully"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string',
            'chat_id'       => 'required|numeric|unique',
            'is_supervisor' => 'required|boolean'
        ]);

        $operator = Operator::create($validated);
        return response()->json($operator, 201);
    }

        /**
     * @OA\Get(
     *     path="/api/operators/{id}",
     *     summary="Get operator by ID",
     *     tags={"Operators"},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Operator",
     *          @OA\JsonContent(ref="#/components/schemas/Operator")
     *      ),
     *      @OA\Response(response=404, description="Operator not found")
     *
     * )
     */


    public function show(string $id)
    {
        $operator=Operator::findOrFail($id);
        return response()->json($operator);
    }

    /**
     * @OA\Put(
     *     path="/api/operators/{id}",
     *     summary="Update an operator by ID",
     *     tags={"Operators"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the operator to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="Yangi Nomi"),
     *             @OA\Property(property="chat_id", type="integer", example=123456789),
     *             @OA\Property(property="is_supervisor", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Operator updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Operator")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Operator not found"
     *     )
     * )
     */


    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name'          => 'sometimes|required|string',
            'chat_id'       => 'sometimes|required|numeric',
            'is_supervisor' => 'sometimes|boolean'
        ]);

        $operator = Operator::findOrFail($id);
        $operator->update($validated);
        return response()->json($operator);
    }

     /**
     * @OA\Delete(
     *     path="/api/operators/{id}",
     *     summary="Delete an operator by ID",
     *     tags={"Operators"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the operator to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Operator deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Operator not found"
     *     )
     * )
     */
    public function destroy(string $id)
    {
        $operator = Operator::findOrFail($id);
        $operator->delete();
        return response()->json(null, 201);
    }

}

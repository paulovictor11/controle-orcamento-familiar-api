<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

abstract class BaseController extends Controller
{
    private object $model;

    public function __construct(object $model)
    {
        $this->model = $model;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            if (isset($request->description)) {
                $description = $request->description;
                $instance = $this->model
                    ->where('description', 'like', "%{$description}%")
                    ->paginate();
            } else {
                $instance = $this->model->paginate();
            }

            return response()->json($instance);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $this->validate($request, [
                'description' => 'required',
                'value'       => 'required',
                'date'        => 'required',
            ]);

            $isModelInstanceAlreadySavedInThisMonth = $this->checkIfRequestIsAlreadySavedInThisMonth($request);

            if ($isModelInstanceAlreadySavedInThisMonth) {
                throw new \Exception("{$this->model->getTable()} already saved in this month");
            }

            $instance = $this->model->create($request->all());

            return response()->json([
                'message' => "{$this->model->getTable()} saved successfuly",
                'data'    => $instance,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            return response()->json($this->model->findOrFail($id));
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function update(int $id, Request $request)
    {
        try {
            $this->validate($request, [
                'description' => 'required',
                'value'       => 'required',
                'date'        => 'required',
            ]);

            $instance = $this->model->findOrFail($id);
            $instance->fill($request->all());
            $instance->save();

            return response()->json([
                'message' => "{$this->model->getTable()} updated successfuly",
                'data'    => $instance,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $instance = $this->model->destroy($id);

            if ($instance === 0) {
                throw new \Exception("Unable to remove {$this->model->getTable()}. Not found in our system.");
            }

            return response()->json([
                'message' => "{$this->model->getTable()} removed successfuly",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function getByMonth(string $year, string $month)
    {
        try {
            $firstDayOfMonth = date("{$year}-{$month}-01");
            $lastDayOfMonth = date("{$year}-{$month}-t");

            $instance = $this->model
                ->query()
                ->whereBetween('date', [$firstDayOfMonth, $lastDayOfMonth])
                ->get();

            return response()->json($instance);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    private function checkIfRequestIsAlreadySavedInThisMonth(Request $request)
    {
        [, $month,] = explode('-', $request->date);

        $firstDayOfMonth = date("Y-{$month}-01");
        $lastDayOfMonth = date("Y-{$month}-t");

        return $this->model
            ->query()
            ->where('description', $request->description)
            ->whereBetween('date', [$firstDayOfMonth, $lastDayOfMonth])
            ->first();
    }
}

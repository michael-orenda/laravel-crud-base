<?php

namespace Rminchrist\CrudBase;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

abstract class BaseController extends \App\Http\Controllers\Controller
{
    protected $model;

    public function __construct()
    {
        $this->model = $this->resolveModel();
    }

    protected function resolveModel(): Model
    {
        $controller = class_basename(static::class);
        $modelName = Str::replaceLast('Controller', '', $controller);

        $modelClass = "App\\Models\\{$modelName}";

        if (!class_exists($modelClass)) {
            throw new \Exception("Model class {$modelClass} not found");
        }

        return new $modelClass();
    }

    public function index()
    {
        return response()->json($this->model->all());
    }

    public function show($id)
    {
        return response()->json($this->model->findOrFail($id));
    }

    public function store(Request $request)
    {
        $record = $this->model->create($request->all());
        return response()->json($record, 201);
    }

    public function update(Request $request, $id)
    {
        $record = $this->model->findOrFail($id);
        $record->update($request->all());
        return response()->json($record);
    }

    public function destroy($id)
    {
        $record = $this->model->findOrFail($id);
        $record->delete();
        return response()->json(null, 204);
    }
}

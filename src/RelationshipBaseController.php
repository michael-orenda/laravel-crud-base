<?php

namespace Rminchrist\CrudBase;

use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class RelationshipBaseController extends BaseController
{
    public function children($id)
    {
        $model = $this->model->findOrFail($id);

        if (!method_exists($model, 'detectChildrenRelations')) {
            throw new NotFoundHttpException("Model does not support relationship scanning");
        }

        $children = $model->detectChildrenRelations();

        if (empty($children)) {
            return response()->json([]);
        }

        // For now, return ALL children relations (could add parameter to choose specific one)
        $result = [];

        foreach ($children as $relation) {
            $result[$relation] = $model->{$relation}()->get();
        }

        return response()->json($result);
    }

    public function parent($id)
    {
        $model = $this->model->findOrFail($id);

        if (!method_exists($model, 'detectParentRelation')) {
            throw new NotFoundHttpException("Model does not support relationship scanning");
        }

        $relation = $model->detectParentRelation();

        if (!$relation) {
            return response()->json(null);
        }

        return response()->json($model->{$relation}()->first());
    }

    public function relations($id)
    {
        $model = $this->model->findOrFail($id);

        $data = [
            "parent"   => $model,
            "parent_relation" => null,
            "children" => []
        ];

        // Detect parent
        if (method_exists($model, 'detectParentRelation')) {
            $parentRel = $model->detectParentRelation();
            if ($parentRel) {
                $data["parent_relation"] = [
                    "relation" => $parentRel,
                    "data"     => $model->{$parentRel}()->first()
                ];
            }
        }

        // Detect children
        if (method_exists($model, 'detectChildrenRelations')) {
            $children = $model->detectChildrenRelations();
            foreach ($children as $childRel) {
                $data["children"][$childRel] = $model->{$childRel}()->get();
            }
        }

        return response()->json($data);
    }
}

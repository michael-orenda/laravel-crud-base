<?php
namespace Rminchrist\CrudBase;

use Illuminate\Http\Request;

abstract class RelationshipBaseController extends BaseController {
    public function children($id){
        $m=$this->model->findOrFail($id);
        $out=[];
        foreach($m->detectChildrenRelations() as $r){
            $out[$r]=$m->{$r}()->get();
        }
        return response()->json($out);
    }
    public function parent($id){
        $m=$this->model->findOrFail($id);
        $r=$m->detectParentRelation();
        return $r? $m->{$r}()->first():null;
    }
    public function relations($id){
        $m=$this->model->findOrFail($id);
        $d=['parent'=>$m,'parent_relation'=>null,'children'=>[]];
        $pr=$m->detectParentRelation();
        if($pr){
            $d['parent_relation']=['relation'=>$pr,'data'=>$m->{$pr}()->first()];
        }
        foreach($m->detectChildrenRelations() as $r){
            $d['children'][$r]=$m->{$r}()->get();
        }
        return response()->json($d);
    }
    public function __call($method,$args){
        if(str_starts_with($method,'relation_'))
            return $this->explicit($method,$args);
        if(str_starts_with($method,'mtm_'))
            return $this->mtm($method,$args);
        return parent::__call($method,$args);
    }
    protected function explicit($method,$args){
        $rel=str_replace('relation_','',$method);
        $id=$args[0];
        $m=$this->model->findOrFail($id);
        return response()->json($m->{$rel}()->get());
    }
    protected function mtm($method,$args){
        $id=$args[0];
        $m=$this->model->findOrFail($id);
        [$p,$action,$rel]=explode('_',$method,3);
        $r=$m->{$rel}();
        switch($action){
            case'get': return response()->json($r->get());
            case'attach': $r->attach(request('id')); return response()->json(['status'=>'attached']);
            case'detach': $r->detach(request('id')); return response()->json(['status'=>'detached']);
            case'sync': $r->sync(request('ids',[])); return response()->json(['status'=>'synced']);
        }
    }
}?>

<?php
namespace Rminchrist\CrudBase\Traits;

use ReflectionClass;
use Illuminate\Database\Eloquent\Relations\Relation;

trait DetectsRelationships {
    public function detectRelations(): array {
        $class = new ReflectionClass($this);
        $methods=$class->getMethods();
        $rels=[];
        foreach($methods as $m){
            if($m->class!==$class->getName()) continue;
            if($m->getNumberOfParameters()>0) continue;
            if(str_starts_with($m->getName(),'__')) continue;
            if(!preg_match('/^[a-z][A-Za-z0-9_]*$/',$m->getName())) continue;
            $rt=$m->getReturnType();
            if($rt && !$this->isRelationReturnType($rt)) continue;
            try{$res=$this->{$m->getName()}();
                if($res instanceof Relation){$rels[$m->getName()]=$res;}
            }catch(\Throwable $e){}
        }
        return $rels;
    }
    private function isRelationReturnType($rt): bool {
        $rc=[
            \Illuminate\Database\Eloquent\Relations\Relation::class,
            \Illuminate\Database\Eloquent\Relations\HasMany::class,
            \Illuminate\Database\Eloquent\Relations\BelongsTo::class,
            \Illuminate\Database\Eloquent\Relations\HasOne::class,
            \Illuminate\Database\Eloquent\Relations\BelongsToMany::class,
            \Illuminate\Database\Eloquent\Relations\MorphMany::class,
            \Illuminate\Database\Eloquent\Relations\MorphTo::class,
            \Illuminate\Database\Eloquent\Relations\MorphOne::class,
        ];
        foreach($rc as $r){ if(is_a($rt->getName(),$r,true)) return true; }
        return false;
    }
    public function detectParentRelation(): ?string {
        foreach($this->detectRelations() as $n=>$r){
            if($r instanceof \Illuminate\Database\Eloquent\Relations\BelongsTo) return $n;
        }
        return null;
    }
    public function detectChildrenRelations(): array {
        $out=[];
        foreach($this->detectRelations() as $n=>$r){
            if($r instanceof \Illuminate\Database\Eloquent\Relations\HasMany) $out[]=$n;
        }
        return $out;
    }
    public function detectManyToManyRelations(): array {
        $out=[];
        foreach($this->detectRelations() as $n=>$r){
            if($r instanceof \Illuminate\Database\Eloquent\Relations\BelongsToMany) $out[]=$n;
        }
        return $out;
    }
}?>

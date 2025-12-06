# Developer Usage Guide

## Controllers
Use:
```
class CustomerController extends RelationshipBaseController {}
```

## Models
```
use Rminchrist\CrudBase\Traits\DetectsRelationships;
class Invoice extends Model {
  use DetectsRelationships;
}
```

## How Relationships Are Detected
- `belongsTo` → parent
- `hasMany` → children
- `belongsToMany` → many-to-many routes

## Examples
See API.md for real-world examples (customer → invoice → items → payments).

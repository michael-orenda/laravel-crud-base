# API Documentation

## CRUD ENDPOINTS
These are automatically created for any controller extending BaseController:

### List all
GET /{model}

### Create
POST /{model}

### Show
GET /{model}/{id}

### Update
PUT /{model}/{id}

### Delete
DELETE /{model}/{id}


## RELATIONSHIP ENDPOINTS
Automatically created for any controller extending RelationshipBaseController.

### Get children
GET /{model}/{id}/children  
Returns all detected hasMany relations.

### Get parent
GET /{model}/{id}/parent  
Returns detected belongsTo parent relation.

### Create child
POST /{model}/{id}/children  
Creates a child model under the parent.

### Assign parent
PUT /{model}/{id}/parent  
Assigns a parent to the model.

---

## NEW UNIFIED RELATIONSHIP ENDPOINT (v1.2.1)

### Get parent & children in a single response
GET /{model}/{id}/relations

**Response example:**
```json
{
  "parent": {
    "id": 1,
    "name": "John Doe"
  },
  "parent_relation": {
    "relation": "customer",
    "data": { "id": 1, "name": "John Doe" }
  },
  "children": {
    "invoices": [ ... ],
    "payments": [ ... ]
  }
}
```

This endpoint works for ANY model automatically.


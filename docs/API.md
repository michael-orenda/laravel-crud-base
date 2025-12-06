# CrudBase API Documentation (v1.3.0)

## CRUD Routes
`GET /{model}`
`POST /{model}`
`GET /{model}/{id}`
`PUT /{model}/{id}`
`DELETE /{model}/{id}`

## Relationship Routes
### Children
`GET /{model}/{id}/children`

### Parent
`GET /{model}/{id}/parent`

### Unified Relations
`GET /{model}/{id}/relations`

## Explicit Relation Routes
If a model has:
```
public function invoices() { ... }
public function payments() { ... }
```

Routes auto-generate:
```
GET /customer/{id}/invoices
GET /customer/{id}/payments
```

## Many‑to‑Many Routes
```
GET  /user/{id}/roles
POST /user/{id}/roles/attach
POST /user/{id}/roles/detach
POST /user/{id}/roles/sync
```

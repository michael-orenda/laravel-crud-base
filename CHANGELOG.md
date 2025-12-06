# Changelog

## v1.3.0 (2025‑12‑06)
### Added
- Explicit relation routes
- Many-to-many auto routes
- Unified `/relations` endpoint
- Strict auto-detection via DetectsRelationships trait
- Improved ServiceProvider routing engine

### Fixed
- Parent/child detection inconsistencies

### Notes
Models using RelationshipBaseController must include:
```
use DetectsRelationships;
```
